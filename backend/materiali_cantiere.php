<?php
require_once 'base_model.php';

class MaterialiCantiere extends BaseModel
{
    private $tableMateriali = 'materiali';
    private $tableMaterialiCantiere = 'materiali_cantiere';


    public function find($id)
    {
        throw new Exception("Metodo non implementato.");
    }

    public function all()
    {
        $sql = "SELECT * FROM {$this->tableMaterialiCantiere}";
        $result = $this->conn->query($sql);

        if (!$result) {
            throw new Exception("Errore nella query: " . $this->conn->error);
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function create($data)
    {
        throw new Exception("Metodo non implementato.");
    }

    public function update($id, $data)
    {
        throw new Exception("Metodo non implementato.");
    }

    public function updateQuantity($idMateriale, $quantita)
    {
        // Verifica che l'ID del materiale e la quantità siano validi
        if (!is_numeric($idMateriale) || !is_numeric($quantita) || $quantita <= 0) {
            throw new Exception("Dati non validi: ID materiale o quantità.");
        }

        // Prepara la query per l'aggiornamento
        $query = "UPDATE {$this->tableMaterialiCantiere} SET quantita = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $this->conn->prepare($query);

        if (!$stmt) {
            throw new Exception("Errore nella preparazione della query: " . $this->conn->error);
        }

        // Associa i parametri alla query
        $stmt->bind_param('ii', $quantita, $idMateriale);

        // Esegui la query e gestisci eventuali errori
        if (!$stmt->execute()) {
            throw new Exception("Errore durante l'aggiornamento della quantità: " . $stmt->error);
        }

        // Chiude lo statement
        $stmt->close();
    }


    public function delete($id)
    {
        throw new Exception("Metodo non implementato.");
    }


    public function getMaterialsByCantiere($idCantiere)
    {
        $query = "SELECT id, nome, descrizione, quantita, prezzo FROM {$this->tableMaterialiCantiere} WHERE id_cantiere = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $idCantiere);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function saveMaterials($idCantiere, $materials)
    {
        if (empty($idCantiere) || empty($materials)) {
            throw new Exception("Dati mancanti: ID cantiere o materiali.");
        }

        // Rimuove i materiali esistenti del cantiere
        $deleteQuery = "DELETE FROM {$this->tableMaterialiCantiere} WHERE id_cantiere = ?";
        $deleteStmt = $this->conn->prepare($deleteQuery);
        $deleteStmt->bind_param('i', $idCantiere);
        if (!$deleteStmt->execute()) {
            throw new Exception("Errore durante la pulizia dei materiali: " . $deleteStmt->error);
        }

        // Inserisce i nuovi materiali
        $insertQuery = "INSERT INTO {$this->tableMaterialiCantiere} (id_cantiere, nome, descrizione, quantita, prezzo, created_at, updated_at) 
                        VALUES (?, ?, ?, ?, ?, NOW(), NOW())";
        $insertStmt = $this->conn->prepare($insertQuery);
        if (!$insertStmt) {
            throw new Exception("Errore nella preparazione della query: " . $this->conn->error);
        }

        foreach ($materials as $materiale) {
            $nome = $materiale['nome'];
            $descrizione = $materiale['descrizione'] ?? '';
            $quantita = $materiale['quantita'];
            $prezzo = $materiale['prezzo'] ?? 0; // Prezzo passato dal frontend

            $insertStmt->bind_param('issid', $idCantiere, $nome, $descrizione, $quantita, $prezzo);

            if (!$insertStmt->execute()) {
                throw new Exception("Errore durante l'inserimento del materiale: " . $insertStmt->error);
            }
        }

        $insertStmt->close();
    }

    public function search($query)
    {
        $query = "%" . $this->conn->real_escape_string($query) . "%";
        $sql = "SELECT id, nome, descrizione, prezzo FROM {$this->tableMateriali} WHERE nome LIKE ? LIMIT 10";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $query);
        $stmt->execute();

        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function deleteMaterial($idMateriale)
    {
        $query = "DELETE FROM {$this->tableMaterialiCantiere} WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Errore nella preparazione della query: " . $this->conn->error);
        }

        $stmt->bind_param('i', $idMateriale);

        if (!$stmt->execute()) {
            throw new Exception("Errore durante l'eliminazione del materiale: " . $stmt->error);
        }

        $stmt->close();
    }
}
