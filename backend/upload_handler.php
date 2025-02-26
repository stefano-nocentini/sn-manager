<?php
require_once 'base_model.php';

// Classe UploadHandler per la gestione dei file
class UploadHandler extends BaseModel
{
    private $tableAllegati = 'allegati_cantiere';
    private $tableFotogallery = 'photogallery_cantiere';


    
    public function find($id){
        throw new Exception('Metodo non implementato per UploadHandler');
    }

    public function all(){
        throw new Exception('Metodo non implementato per UploadHandler');
    }

    public function create($data){
        throw new Exception('Metodo non implementato per UploadHandler');
    }

    public function update($id, $data){
        throw new Exception('Metodo non implementato per UploadHandler');
    }

    public function delete($id){
        throw new Exception('Metodo non implementato per UploadHandler');
    }



    public function uploadFiles($idCantiere, $files, $type)
    {
        if (!$idCantiere || !$files) {
            throw new Exception('ID cantiere o file non validi');
        }

        $directoryBase = $type === 'allegati' ? '../allegati' : '../photogallery';
        $subdir = $type === 'allegati' ? '' : '/img';
        $thumbDir = $type === 'allegati' ? '' : '/thumbnail';

        $targetDir = "$directoryBase/$idCantiere$subdir";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        if ($type === 'fotogallery' && !is_dir("$directoryBase/$idCantiere$thumbDir")) {
            mkdir("$directoryBase/$idCantiere$thumbDir", 0777, true);
        }

        $tableName = $type === 'allegati' ? $this->tableAllegati : $this->tableFotogallery;

        foreach ($files['tmp_name'] as $index => $tmpName) {
            $originalName = $files['name'][$index];
            $fileExtension = pathinfo($originalName, PATHINFO_EXTENSION);
            $newFileName = $this->generateFileName($targetDir, $fileExtension);
            $destination = "$targetDir/$newFileName";

            if (move_uploaded_file($tmpName, $destination)) {
                if ($type === 'fotogallery') {
                    $this->createThumbnail($destination, "$directoryBase/$idCantiere$thumbDir/$newFileName");
                }

                $query = $this->conn->prepare("INSERT INTO $tableName (id_cantiere, nome_file) VALUES (?, ?)");
                $query->bind_param('is', $idCantiere, $newFileName);
                $query->execute();
            } else {
                throw new Exception("Errore nel caricamento del file $originalName");
            }
        }

        return true;
    }

    public function fetchFiles($idCantiere, $type)
    {
        if (!$idCantiere) {
            throw new Exception('ID cantiere non valido');
        }

        $tableName = $type === 'allegati' ? $this->tableAllegati : $this->tableFotogallery;
        $query = $this->conn->prepare("SELECT id, nome_file FROM $tableName WHERE id_cantiere = ?");
        $query->bind_param('i', $idCantiere);
        $query->execute();

        $result = $query->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    private function generateFileName($directory, $extension)
    {
        $i = 1;
        while (file_exists("$directory/$i.$extension")) {
            $i++;
        }
        return "$i.$extension";
    }

    private function createThumbnail($source, $destination)
    {
        $thumbnailWidth = 200;
        $thumbnailHeight = 200;

        list($width, $height) = getimagesize($source);
        $thumb = imagecreatetruecolor($thumbnailWidth, $thumbnailHeight);

        $sourceImage = null;
        $extension = strtolower(pathinfo($source, PATHINFO_EXTENSION));

        switch ($extension) {
            case 'jpg':
            case 'jpeg':
                $sourceImage = imagecreatefromjpeg($source);
                break;
            case 'png':
                $sourceImage = imagecreatefrompng($source);
                break;
            case 'gif':
                $sourceImage = imagecreatefromgif($source);
                break;
            default:
                throw new Exception('Formato immagine non supportato');
        }

        if ($sourceImage) {
            imagecopyresampled($thumb, $sourceImage, 0, 0, 0, 0, $thumbnailWidth, $thumbnailHeight, $width, $height);

            switch ($extension) {
                case 'jpg':
                case 'jpeg':
                    imagejpeg($thumb, $destination);
                    break;
                case 'png':
                    imagepng($thumb, $destination);
                    break;
                case 'gif':
                    imagegif($thumb, $destination);
                    break;
            }

            imagedestroy($sourceImage);
            imagedestroy($thumb);
        }
    }
}



// Gestione delle richieste
try {
    require_once 'database.php';
    require_once 'base_model.php';

    // Connessione al database
    $db = (new Database())->connect();
    $uploadHandler = new UploadHandler($db);

    $action = $_GET['action'] ?? $_POST['action'] ?? null;
    if (!$action) {
        throw new Exception('Azione non specificata');
    }

    switch ($action) {
        case 'get_files':
            $type = $_GET['type'] ?? null;
            $idCantiere = $_GET['id_cantiere'] ?? null;

            if (!$type || !$idCantiere) {
                throw new Exception('Parametri mancanti: type o id_cantiere');
            }

            $files = $uploadHandler->fetchFiles($idCantiere, $type);
            echo json_encode(['success' => true, 'files' => $files]);
            break;

        case 'upload_files':
            $type = $_POST['type'] ?? null;
            $idCantiere = $_POST['id_cantiere'] ?? null;
            $files = $_FILES['allegati'] ?? ($_FILES['fotogallery'] ?? null); // Supporta entrambi i tipi di file
        
            if (!$type || !$idCantiere || !$files) {
                throw new Exception('Parametri mancanti: type, id_cantiere o files');
            }
        
            $uploadHandler->uploadFiles($idCantiere, $files, $type);
            echo json_encode(['success' => true, 'message' => 'File caricati con successo']);
            break;

        case 'delete_allegato':
            $logFile = __DIR__ . '/debug_log.txt';

            $data = json_decode(file_get_contents('php://input'), true);

            $id = $data['id'] ?? null;
            $filePath = $data['file_path'] ?? null;
        
            if (!$id || !$filePath) {
                echo json_encode(['success' => false, 'message' => 'Parametri mancanti: id o file_path']);
                exit;
            }
        
            try {
                // Elimina dal database
                $query = $db->prepare("DELETE FROM allegati_cantiere WHERE id = ?");
                $query->bind_param('i', $id);
                $query->execute();
        
                if ($query->affected_rows > 0) {
                    $absolutePath = dirname(__DIR__) . '/' . $filePath;
                    $absolutePath = str_replace('/', DIRECTORY_SEPARATOR, $absolutePath);
        
                    if (file_exists($absolutePath)) {
                        if (unlink($absolutePath)) {
                            echo json_encode(['success' => true, 'message' => 'Allegato eliminato con successo.']);
                        } else {
                            echo json_encode(['success' => false, 'message' => 'Errore durante l\'eliminazione fisica del file.']);
                        }
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Il file non esiste nel file system.']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Errore durante l\'eliminazione dal database.']);
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Errore: ' . $e->getMessage()]);
            }
            exit;
            
            
        case 'delete_image':
            // Decodifica il payload JSON
            $data = json_decode(file_get_contents('php://input'), true);
        
            // Ottieni i parametri
            $id = $data['id'] ?? null;
            $filePath = $data['file_path'] ?? null;
        
            if (!$id || !$filePath) {
                echo json_encode(['success' => false, 'message' => 'Parametri mancanti: id o file_path']);
                exit;
            }
        
            try {
                // Rimuovi l'immagine dal database
                $query = $db->prepare("DELETE FROM photogallery_cantiere WHERE id = ?");
                $query->bind_param('i', $id);
                $query->execute();
        
                if ($query->affected_rows > 0) {
                    // Rimuovi fisicamente il file dalla directory `img`
                    $absolutePath = dirname(__DIR__) . '/' . $filePath; // Percorso assoluto per il file originale
                    $absolutePath = str_replace('/', DIRECTORY_SEPARATOR, $absolutePath); // Uniforma i separatori
        
                    // Calcola il percorso per il file `thumbnail`
                    $normalizedPath = str_replace('\\', '/', $absolutePath); // Converte i backslash in slash
                    $thumbnailPath = str_replace('/img/', '/thumbnail/', $normalizedPath); // Sostituisce `/img/` con `/thumbnail/`
                    $thumbnailPath = str_replace('/', DIRECTORY_SEPARATOR, $thumbnailPath); // Riconverti il percorso thumbnail
        
                    // Elimina il file dalla directory `img`
                    if (file_exists($absolutePath)) {
                        unlink($absolutePath);
                    }
        
                    // Elimina il file dalla directory `thumbnail`
                    if (file_exists($thumbnailPath)) {
                        unlink($thumbnailPath);
                    }
        
                    echo json_encode(['success' => true, 'message' => 'Immagine eliminata con successo.']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Errore durante l\'eliminazione dal database.']);
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Errore: ' . $e->getMessage()]);
            }
            exit;
                                  
        default:
            throw new Exception('Azione non riconosciuta');
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
