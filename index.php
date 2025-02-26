<?php
require_once 'backend/database.php';
require_once 'backend/utenti.php';
require_once 'backend/config.php';


// Connessione al database
$db = (new Database())->connect();
$utenti = new Utenti($db);

// Definisci i titoli delle pagine
$pageTitles = [
    'dashboard' => 'Dashboard',
    'banche' => 'Banche',
    'materiali' => 'Materiali',
    'aziende' => 'Aziende',
    'fornitori' => 'Fornitori',
    'utenti' => 'Utenti',
    'zone' => 'Zone',
    'clienti' => 'Clienti',
    'cantieri' => 'Cantieri',
    'spese' => 'Spese',
    'scadenze' => 'Scadenze',
    'listino' => 'Listino',
    'stato_cantieri' => 'Stato Cantieri',
    'motivo_spese' => 'Motivo Spese',
    'logout' => 'Logout',
    'login' => 'Login'
];

// Identifica la rotta
$page = isset($_GET['page']) ? $_GET['page'] : 'login';


// Routing
switch ($page) {
    // Login
    case 'login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $utenti->login($_POST['email'], $_POST['password']);
                header('Location: index.php?page=dashboard');
                exit();
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
        include 'views/login.php';
        break;

    // Dashboard
    case 'dashboard':
        if (!$utenti->isAuthenticated()) {
            header('Location: index.php?page=login');
            exit();
        }

        require_once 'backend/cantieri.php';
        require_once 'backend/scadenze.php';

        $cantieri = new Cantieri($db);
        $scadenze = new Scadenze($db);

        $totaliData = $cantieri->getTotali();
        $prossimeScadenze = $scadenze->getUpcoming();

        include 'views/dashboard.php';
        break;

    // Banche
    case 'banche':
        if (!$utenti->isAuthenticated()) {
            header('Location: index.php?page=login');
            exit();
        }

        //Motore di ricerca Comuni con AJAX
        if (isset($_GET['ajax']) && $_GET['ajax'] === 'get_comuni') {
            require_once 'backend/comuni.php';
            $comuni = new Comuni($db);
        
            try {
                if (isset($_GET['query']) && strlen($_GET['query']) > 1) {
                    $comuneQuery = $_GET['query'];
                    $comuniData = $comuni->getWithFilters(['comune' => $comuneQuery]);
                    header('Content-Type: application/json');
                    echo json_encode($comuniData);
                } else {
                    if (!headers_sent()) {
                        header('Content-Type: application/json');
                    }
                    echo json_encode(['error' => 'Query non valida o troppo breve']);
                }
            } catch (Exception $e) {
                if (!headers_sent()) {
                    header('Content-Type: application/json');
                }
                echo json_encode(['error' => $e->getMessage()]);
            }
            exit();
        }

        // Dettagli del comune con AJAX
        if (isset($_GET['ajax']) && $_GET['ajax'] === 'get_comune_details') {
            require_once 'backend/comuni.php';
            require_once 'backend/province.php';
            require_once 'backend/regioni.php';
        
            $comuni = new Comuni($db);
            $province = new Province($db);
            $regioni = new Regioni($db);
        
            $idComune = $_GET['id_comune'] ?? null;
        
            if ($idComune) {
                try {
                    $comune = $comuni->find($idComune);
        
                    if (!$comune) {
                        throw new Exception('Comune non trovato');
                    }
        
                    $provincia = $province->find($comune['id_provincia']);
                    $regione = $regioni->find($provincia['id_regione']);
        
                    echo json_encode([
                        'id_provincia' => $provincia['id'],
                        'provincia_nome' => $provincia['provincia'],
                        'id_regione' => $regione['id'],
                        'regione_nome' => $regione['regione']
                    ]);
                } catch (Exception $e) {
                    echo json_encode(['error' => $e->getMessage()]);
                }
            } else {
                echo json_encode(['error' => 'ID comune mancante']);
            }
            exit();
        }
    
        require_once 'backend/banche.php';
        $banche = new Banche($db);
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'];
            try {
                if ($action === 'create') {
                    $banche->create($_POST);
                } elseif ($action === 'update') {
                    $banche->update($_POST['id'], $_POST);
                } elseif ($action === 'delete') {
                    $banche->delete($_POST['id']);
                }
                header('Location: index.php?page=banche');
                exit();
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
    
        $bancheData = $banche->all();
        include 'views/banche.php';
        break;

    // Listino
    case 'listino':
        if (!$utenti->isAuthenticated()) {
            header('Location: index.php?page=login');
            exit();
        }
    
        require_once 'backend/listino.php';
        $listino = new Listino($db);
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'];
            try {
                if ($action === 'create') {
                    $listino->create($_POST);
                } elseif ($action === 'update') {
                    $listino->update($_POST['id'], $_POST);
                } elseif ($action === 'delete') {
                    $listino->delete($_POST['id']);
                }
                header('Location: index.php?page=listino');
                exit();
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
    
        $listinoData = $listino->all();
        include 'views/listino.php';
        break;

    // Materiali
    case 'materiali':
        if (!$utenti->isAuthenticated()) {
            header('Location: index.php?page=login');
            exit();
        }
    
        require_once 'backend/materiali.php';
        $materiali = new Materiali($db);
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'];
            try {
                if ($action === 'create') {
                    $materiali->create($_POST);
                } elseif ($action === 'update') {
                    $materiali->update($_POST['id'], $_POST);
                } elseif ($action === 'delete') {
                    $materiali->delete($_POST['id']);
                }
                header('Location: index.php?page=materiali');
                exit();
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
    
        $materialiData = $materiali->all();
        include 'views/materiali.php';
        break;
        
    // Aziende
    case 'aziende':
        if (!$utenti->isAuthenticated()) {
            header('Location: index.php?page=login');
            exit();
        }

        //Motore di ricerca Comuni con AJAX
        if (isset($_GET['ajax']) && $_GET['ajax'] === 'get_comuni') {
            require_once 'backend/comuni.php';
            $comuni = new Comuni($db);
        
            try {
                if (isset($_GET['query']) && strlen($_GET['query']) > 1) {
                    $comuneQuery = $_GET['query'];
                    $comuniData = $comuni->getWithFilters(['comune' => $comuneQuery]);
                    header('Content-Type: application/json');
                    echo json_encode($comuniData);
                } else {
                    if (!headers_sent()) {
                        header('Content-Type: application/json');
                    }
                    echo json_encode(['error' => 'Query non valida o troppo breve']);
                }
            } catch (Exception $e) {
                if (!headers_sent()) {
                    header('Content-Type: application/json');
                }
                echo json_encode(['error' => $e->getMessage()]);
            }
            exit();
        }

        // Dettagli del comune con AJAX
        if (isset($_GET['ajax']) && $_GET['ajax'] === 'get_comune_details') {
            require_once 'backend/comuni.php';
            require_once 'backend/province.php';
            require_once 'backend/regioni.php';
        
            $comuni = new Comuni($db);
            $province = new Province($db);
            $regioni = new Regioni($db);
        
            $idComune = $_GET['id_comune'] ?? null;
        
            if ($idComune) {
                try {
                    $comune = $comuni->find($idComune);
        
                    if (!$comune) {
                        throw new Exception('Comune non trovato');
                    }
        
                    $provincia = $province->find($comune['id_provincia']);
                    $regione = $regioni->find($provincia['id_regione']);
        
                    echo json_encode([
                        'id_provincia' => $provincia['id'],
                        'provincia_nome' => $provincia['provincia'],
                        'id_regione' => $regione['id'],
                        'regione_nome' => $regione['regione']
                    ]);
                } catch (Exception $e) {
                    echo json_encode(['error' => $e->getMessage()]);
                }
            } else {
                echo json_encode(['error' => 'ID comune mancante']);
            }
            exit();
        }
    
        require_once 'backend/aziende.php';
        $aziende = new Aziende($db);
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'];
            try {
                if ($action === 'create') {
                    $aziende->create($_POST);
                } elseif ($action === 'update') {
                    $aziende->update($_POST['id'], $_POST);
                } elseif ($action === 'delete') {
                    $aziende->delete($_POST['id']);
                }
                header('Location: index.php?page=aziende');
                exit();
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
    
        $aziendeData = $aziende->all();
        include 'views/aziende.php';
        break;

    // Fornitori
    case 'fornitori':
        if (!$utenti->isAuthenticated()) {
            header('Location: index.php?page=login');
            exit();
        }

        //Motore di ricerca Comuni con AJAX
        if (isset($_GET['ajax']) && $_GET['ajax'] === 'get_comuni') {
            require_once 'backend/comuni.php';
            $comuni = new Comuni($db);
        
            try {
                if (isset($_GET['query']) && strlen($_GET['query']) > 1) {
                    $comuneQuery = $_GET['query'];
                    $comuniData = $comuni->getWithFilters(['comune' => $comuneQuery]);
                    header('Content-Type: application/json');
                    echo json_encode($comuniData);
                } else {
                    if (!headers_sent()) {
                        header('Content-Type: application/json');
                    }
                    echo json_encode(['error' => 'Query non valida o troppo breve']);
                }
            } catch (Exception $e) {
                if (!headers_sent()) {
                    header('Content-Type: application/json');
                }
                echo json_encode(['error' => $e->getMessage()]);
            }
            exit();
        }

        // Dettagli del comune con AJAX
        if (isset($_GET['ajax']) && $_GET['ajax'] === 'get_comune_details') {
            require_once 'backend/comuni.php';
            require_once 'backend/province.php';
            require_once 'backend/regioni.php';
        
            $comuni = new Comuni($db);
            $province = new Province($db);
            $regioni = new Regioni($db);
        
            $idComune = $_GET['id_comune'] ?? null;
        
            if ($idComune) {
                try {
                    $comune = $comuni->find($idComune);
        
                    if (!$comune) {
                        throw new Exception('Comune non trovato');
                    }
        
                    $provincia = $province->find($comune['id_provincia']);
                    $regione = $regioni->find($provincia['id_regione']);
        
                    echo json_encode([
                        'id_provincia' => $provincia['id'],
                        'provincia_nome' => $provincia['provincia'],
                        'id_regione' => $regione['id'],
                        'regione_nome' => $regione['regione']
                    ]);
                } catch (Exception $e) {
                    echo json_encode(['error' => $e->getMessage()]);
                }
            } else {
                echo json_encode(['error' => 'ID comune mancante']);
            }
            exit();
        }
    
        require_once 'backend/fornitori.php';
        $fornitori = new Fornitori($db);
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'];
            try {
                if ($action === 'create') {
                    $fornitori->create($_POST);
                } elseif ($action === 'update') {
                    $fornitori->update($_POST['id'], $_POST);
                } elseif ($action === 'delete') {
                    $fornitori->delete($_POST['id']);
                }
                header('Location: index.php?page=fornitori');
                exit();
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
    
        $fornitoriData = $fornitori->all();
        include 'views/fornitori.php';
        break;

    // Clienti
    case 'clienti':
        if (!$utenti->isAuthenticated()) {
            header('Location: index.php?page=login');
            exit();
        }


        //Motore di ricerca Comuni con AJAX
        if (isset($_GET['ajax']) && $_GET['ajax'] === 'get_comuni') {
            require_once 'backend/comuni.php';
            $comuni = new Comuni($db);
        
            try {
                if (isset($_GET['query']) && strlen($_GET['query']) > 1) {
                    $comuneQuery = $_GET['query'];
                    $comuniData = $comuni->getWithFilters(['comune' => $comuneQuery]);
                    header('Content-Type: application/json');
                    echo json_encode($comuniData);
                } else {
                    if (!headers_sent()) {
                        header('Content-Type: application/json');
                    }
                    echo json_encode(['error' => 'Query non valida o troppo breve']);
                }
            } catch (Exception $e) {
                if (!headers_sent()) {
                    header('Content-Type: application/json');
                }
                echo json_encode(['error' => $e->getMessage()]);
            }
            exit();
        }

        // Dettagli del comune con AJAX
        if (isset($_GET['ajax']) && $_GET['ajax'] === 'get_comune_details') {
            require_once 'backend/comuni.php';
            require_once 'backend/province.php';
            require_once 'backend/regioni.php';
        
            $comuni = new Comuni($db);
            $province = new Province($db);
            $regioni = new Regioni($db);
        
            $idComune = $_GET['id_comune'] ?? null;
        
            if ($idComune) {
                try {
                    $comune = $comuni->find($idComune);
        
                    if (!$comune) {
                        throw new Exception('Comune non trovato');
                    }
        
                    $provincia = $province->find($comune['id_provincia']);
                    $regione = $regioni->find($provincia['id_regione']);
        
                    echo json_encode([
                        'id_provincia' => $provincia['id'],
                        'provincia_nome' => $provincia['provincia'],
                        'id_regione' => $regione['id'],
                        'regione_nome' => $regione['regione']
                    ]);
                } catch (Exception $e) {
                    echo json_encode(['error' => $e->getMessage()]);
                }
            } else {
                echo json_encode(['error' => 'ID comune mancante']);
            }
            exit();
        }

    
        require_once 'backend/clienti.php';
        $clienti = new Clienti($db);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'];
            try {
                if ($action === 'create') {
                    $clienti->create($_POST);
                } elseif ($action === 'update') {
                    $clienti->update($_POST['id'], $_POST);
                } elseif ($action === 'delete') {
                    $clienti->delete($_POST['id']);
                }
                header('Location: index.php?page=clienti');
                exit();
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }

        $clientiData = $clienti->all();
        include 'views/clienti.php';
        break;    

    // Posatori
    case 'posatori':
        if (!$utenti->isAuthenticated()) {
            header('Location: index.php?page=login');
            exit();
        }

        require_once 'backend/posatori.php';
        $posatori = new Posatori($db);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'];
            try {
                if ($action === 'create') {
                    $posatori->create($_POST);
                } elseif ($action === 'update') {
                    $posatori->update($_POST['id'], $_POST);
                } elseif ($action === 'delete') {
                    $posatori->delete($_POST['id']);
                }
                header('Location: index.php?page=posatori');
                exit();
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }


        //Motore di ricerca Comuni con AJAX
        if (isset($_GET['ajax']) && $_GET['ajax'] === 'get_comuni') {
            require_once 'backend/comuni.php';
            $comuni = new Comuni($db);
        
            try {
                if (isset($_GET['query']) && strlen($_GET['query']) > 1) {
                    $comuneQuery = $_GET['query'];
                    $comuniData = $comuni->getWithFilters(['comune' => $comuneQuery]);
                    header('Content-Type: application/json');
                    echo json_encode($comuniData);
                } else {
                    if (!headers_sent()) {
                        header('Content-Type: application/json');
                    }
                    echo json_encode(['error' => 'Query non valida o troppo breve']);
                }
            } catch (Exception $e) {
                if (!headers_sent()) {
                    header('Content-Type: application/json');
                }
                echo json_encode(['error' => $e->getMessage()]);
            }
            exit();
        }

        // Dettagli del comune con AJAX
        if (isset($_GET['ajax']) && $_GET['ajax'] === 'get_comune_details') {
            require_once 'backend/comuni.php';
            require_once 'backend/province.php';
            require_once 'backend/regioni.php';
        
            $comuni = new Comuni($db);
            $province = new Province($db);
            $regioni = new Regioni($db);
        
            $idComune = $_GET['id_comune'] ?? null;
        
            if ($idComune) {
                try {
                    $comune = $comuni->find($idComune);
        
                    if (!$comune) {
                        throw new Exception('Comune non trovato');
                    }
        
                    $provincia = $province->find($comune['id_provincia']);
                    $regione = $regioni->find($provincia['id_regione']);
        
                    echo json_encode([
                        'id_provincia' => $provincia['id'],
                        'provincia_nome' => $provincia['provincia'],
                        'id_regione' => $regione['id'],
                        'regione_nome' => $regione['regione']
                    ]);
                } catch (Exception $e) {
                    echo json_encode(['error' => $e->getMessage()]);
                }
            } else {
                echo json_encode(['error' => 'ID comune mancante']);
            }
            exit();
        }


        $posatoriData = $posatori->all();
        include 'views/posatori.php';
        break;

    // Utenti
    case 'utenti':
        if (!$utenti->isAuthenticated()) {
            header('Location: index.php?page=login');
            exit();
        }

        require_once 'backend/utenti.php';
        $utenti = new Utenti($db);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'];
            try {
                if ($action === 'create') {
                    $utenti->create($_POST);
                } elseif ($action === 'update') {
                    $utenti->update($_POST['id'], $_POST);
                } elseif ($action === 'delete') {
                    $utenti->delete($_POST['id']);
                }
                header('Location: index.php?page=utenti');
                exit();
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }

        $utentiData = $utenti->all();
        include 'views/utenti.php';
        break;
    
    // Cantieri
    case 'cantieri':
        if (!$utenti->isAuthenticated()) {
            header('Location: index.php?page=login');
            exit();
        }

        // Salva nuovo cliente dalla modale
        if (isset($_POST['action']) && $_POST['action'] === 'create' && isset($_POST['nome'], $_POST['cognome'])) {
            require_once 'backend/clienti.php';
            $clienti = new Clienti($db);
            
            try {
                $newClienteId = $clienti->create([
                    'societa' => $_POST['societa'] ?? null,
                    'nome' => $_POST['nome'],
                    'cognome' => $_POST['cognome'],
                    'indirizzo' => $_POST['indirizzo'] ?? null,
                    'id_comune' => $_POST['id_comune'] ?? null,
                    'id_provincia' => $_POST['id_provincia'] ?? null,
                    'id_regione' => $_POST['id_regione'] ?? null,
                    'p_iva' => $_POST['p_iva'] ?? null,
                    'rea' => $_POST['rea'] ?? null,
                    'telefono' => $_POST['telefono'] ?? null,
                    'email' => $_POST['email'] ?? null,
                    'pec' => $_POST['pec'] ?? null,
                ]);

                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Cliente creato con successo',
                    'id_cliente' => $newClienteId,
                ]);
            } catch (Exception $e) {
                if (!headers_sent()) {
                    header('Content-Type: application/json');
                }
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
            exit();
        }

        
        require_once 'backend/cantieri.php';
        $cantieri = new Cantieri($db);

        require_once 'backend/province.php';
        $province = new Province($db);

        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json'); // Imposta l'header per JSON
    
            // Controlla se i dati sono stati inviati come JSON
            $inputData = json_decode(file_get_contents('php://input'), true);
    
            // Se non è JSON, usa $_POST
            $action = $inputData['action'] ?? ($_POST['action'] ?? null);

            try {
                // Inizia una transazione per garantire che tutte le operazioni siano atomiche
                $db->begin_transaction();
    
                if ($action === 'create') {
                    try {
                        // Creazione del cantiere
                        $idCantiere = $cantieri->create($_POST);
    
                        if (!$idCantiere) {
                            throw new Exception("Errore: l'ID del cantiere non è stato generato.");
                        }
    
                        if (!empty($_POST['id_listino'])) {
                            $vociData = [];
                            foreach ($_POST['id_listino'] as $index => $idListino) {
                                $vociData[] = [
                                    'id_voce_cantiere' => $_POST['id_voce_cantiere'][$index] ?? null, // Usa 'id_voce_cantiere' per ID voci esistenti o null per nuove voci
                                    'id_listino' => $idListino,
                                    'quantita' => $_POST['quantita'][$index],
                                    'prezzo' => $_POST['prezzo'][$index],
                                ];
                            }
    
                            // Aggiunta delle voci
                            $cantieri->addVociCantiere($idCantiere, $vociData);
                        }
    
                        $db->commit(); // Conferma le operazioni
                        ob_clean(); // Pulisci il buffer di output
                        echo json_encode(['success' => true, 'message' => 'Cantiere creato con successo.']);
                    } catch (Exception $e) {
                        $db->rollback(); // Annulla le modifiche in caso di errore
                        ob_clean(); // Pulisci il buffer di output
                        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                    }
                    exit(); // Termina l'esecuzione per evitare ulteriori output
                } elseif ($action === 'update') {
                    // Preparazione delle voci per l'aggiornamento
                    $vociData = [];
                    if (!empty($_POST['id_listino'])) {
                        foreach ($_POST['id_listino'] as $index => $idListino) {
                            $vociData[] = [
                                'id_voce_cantiere' => $_POST['id_voce_cantiere'][$index] ?? null, // ID per le voci esistenti
                                'id_listino' => $idListino,
                                'quantita' => $_POST['quantita'][$index],
                                'prezzo' => $_POST['prezzo'][$index],
                            ];
                        }
                    }
    
                    // Chiamata al metodo `updateWithVoci`
                    $cantieri->updateWithVoci($_POST['id'], $_POST, $vociData);
    
                    $db->commit(); // Conferma le operazioni
                    ob_clean(); // Pulisci il buffer di output
                    echo json_encode(['success' => true, 'message' => 'Cantiere aggiornato con successo.']);
                } elseif ($action === 'delete') {
                    try {
                        // Elimina il cantiere
                        $id = $inputData['id'] ?? ($_POST['id'] ?? null);
                        if (!$id) {
                            throw new Exception("ID del cantiere non specificato.");
                        }
    
                        $cantieri->delete($id);
    
                        $db->commit(); // Conferma le operazioni
                        ob_clean(); // Pulisci il buffer di output
                        echo json_encode(['success' => true, 'message' => 'Cantiere eliminato con successo.']);
                    } catch (Exception $e) {
                        $db->rollback(); // Annulla le modifiche in caso di errore
                        ob_clean(); // Pulisci il buffer di output
                        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                    }
                }
            } catch (Exception $e) {
                $db->rollback(); // Annulla tutte le operazioni in caso di errore
                ob_clean(); // Pulisci il buffer di output in caso di errore
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            exit(); // Termina l'esecuzione per evitare ulteriori output
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'getVoci') {
            header('Content-Type: application/json');
    
            try {
                $idCantiere = $_GET['id'] ?? null;
                if (!$idCantiere) {
                    echo json_encode(['success' => false, 'message' => 'ID cantiere mancante.']);
                    exit;
                }
    
                $voci = $cantieri->getVoci($idCantiere);
                echo json_encode(['success' => true, 'voci' => $voci]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            exit();
        }

        //Popola Province relazionate alla Regione
        if (isset($_GET['ajax']) && $_GET['ajax'] === 'get_province') {
            if (isset($_GET['regione_id'])) {
                $provinceData = $province->getWithFilters(['id_regione' => $_GET['regione_id']]);
                header('Content-Type: application/json');
                echo json_encode($provinceData);
            } else {
                header('HTTP/1.1 400 Bad Request');
                echo json_encode(['error' => 'ID regione mancante']);
            }
            exit();
        }

        //Dettagli cantiere
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'getDettagli') {
            header('Content-Type: application/json');
        
            try {
                $idCantiere = $_GET['id'] ?? null;
                if (!$idCantiere) {
                    echo json_encode(['success' => false, 'message' => 'ID cantiere mancante.']);
                    exit;
                }
        
                $dettagli = $cantieri->getDettagli($idCantiere);
                echo json_encode(['success' => true, 'dettagli' => $dettagli]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            exit();
        } 
        
        // Motore di ricerca Clienti con AJAX
        if (isset($_GET['ajax']) && $_GET['ajax'] === 'get_clienti') {
            require_once 'backend/clienti.php';
            $clienti = new Clienti($db);

            try {
                if (isset($_GET['query']) && strlen($_GET['query']) > 1) {
                    $clienteQuery = $_GET['query'];
                    $clientiData = $clienti->getWithFilters(['cliente' => $clienteQuery]);
                    header('Content-Type: application/json');
                    echo json_encode($clientiData);
                } else {
                    if (!headers_sent()) {
                        header('Content-Type: application/json');
                    }
                    echo json_encode(['error' => 'Query non valida o troppo breve']);
                }
            } catch (Exception $e) {
                if (!headers_sent()) {
                    header('Content-Type: application/json');
                }
                echo json_encode(['error' => $e->getMessage()]);
            }
            exit();
        }       
        
        //Motore di ricerca Comuni con AJAX
        if (isset($_GET['ajax']) && $_GET['ajax'] === 'get_comuni') {
            require_once 'backend/comuni.php';
            $comuni = new Comuni($db);
        
            try {
                if (isset($_GET['query']) && strlen($_GET['query']) > 1) {
                    $comuneQuery = $_GET['query'];
                    $comuniData = $comuni->getWithFilters(['comune' => $comuneQuery]);
                    header('Content-Type: application/json');
                    echo json_encode($comuniData);
                } else {
                    if (!headers_sent()) {
                        header('Content-Type: application/json');
                    }
                    echo json_encode(['error' => 'Query non valida o troppo breve']);
                }
            } catch (Exception $e) {
                if (!headers_sent()) {
                    header('Content-Type: application/json');
                }
                echo json_encode(['error' => $e->getMessage()]);
            }
            exit();
        }

        // Dettagli del comune con AJAX
        if (isset($_GET['ajax']) && $_GET['ajax'] === 'get_comune_details') {
            require_once 'backend/comuni.php';
            require_once 'backend/province.php';
            require_once 'backend/regioni.php';
        
            $comuni = new Comuni($db);
            $province = new Province($db);
            $regioni = new Regioni($db);
        
            $idComune = $_GET['id_comune'] ?? null;
        
            if ($idComune) {
                try {
                    $comune = $comuni->find($idComune);
        
                    if (!$comune) {
                        throw new Exception('Comune non trovato');
                    }
        
                    $provincia = $province->find($comune['id_provincia']);
                    $regione = $regioni->find($provincia['id_regione']);
        
                    echo json_encode([
                        'id_provincia' => $provincia['id'],
                        'provincia_nome' => $provincia['provincia'],
                        'id_regione' => $regione['id'],
                        'regione_nome' => $regione['regione']
                    ]);
                } catch (Exception $e) {
                    echo json_encode(['error' => $e->getMessage()]);
                }
            } else {
                echo json_encode(['error' => 'ID comune mancante']);
            }
            exit();
        }

        // Dettagli del cliente con AJAX
        if (isset($_GET['ajax']) && $_GET['ajax'] === 'get_cliente_data') {
            require_once 'backend/cantieri.php';

            $cantieri = new Cantieri($db);

            $idCliente = $_GET['id'] ?? null;

            if ($idCliente) {
                try {
                    $cliente = $cantieri->getClienteData($idCliente);

                    if (!$cliente) {
                        throw new Exception('Cliente non trovato');
                    }

                    echo json_encode([
                        'success' => true,
                        'cliente' => $cliente,
                    ]);
                } catch (Exception $e) {
                    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'ID cliente mancante']);
            }
            exit();
        }



        $cantieriData = $cantieri->all();
        include 'views/cantieri.php';
        break;          

    // Stato Cantiere
    case 'stato_cantieri':
        if (!$utenti->isAuthenticated()) {
            header('Location: index.php?page=login');
            exit();
        }

        require_once 'backend/stato_cantieri.php';
        $statoCantiere = new StatoCantiere($db);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'];
            try {
                if ($action === 'create') {
                    $statoCantiere->create($_POST);
                } elseif ($action === 'update') {
                    $statoCantiere->update($_POST['id'], $_POST);
                } elseif ($action === 'delete') {
                    $statoCantiere->delete($_POST['id']);
                }
                header('Location: index.php?page=stato_cantieri');
                exit();
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }

        $statiCantiereData = $statoCantiere->all();
        include 'views/stato_cantieri.php';
        break;

    // Motivo Spese
    case 'motivo_spese':
        if (!$utenti->isAuthenticated()) {
            header('Location: index.php?page=login');
            exit();
        }

        require_once 'backend/motivo_spese.php';
        $motivoSpesa = new MotivoSpese($db);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'];
            try {
                if ($action === 'create') {
                    $motivoSpesa->create($_POST);
                } elseif ($action === 'update') {
                    $motivoSpesa->update($_POST['id'], $_POST);
                } elseif ($action === 'delete') {
                    $motivoSpesa->delete($_POST['id']);
                }
                header('Location: index.php?page=motivo_spese');
                exit();
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }

        $motiviSpeseData = $motivoSpesa->all();
        include 'views/motivo_spese.php';
        break;

    // Spese
    case 'spese':
        if (!$utenti->isAuthenticated()) {
            header('Location: index.php?page=login');
            exit();
        }
    
        require_once 'backend/spese.php';
        $spese = new Spese($db);
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'];
            try {
                if ($action === 'create') {
                    $spese->create($_POST);
                } elseif ($action === 'update') {
                    $spese->update($_POST['id'], $_POST);
                } elseif ($action === 'delete') {
                    $spese->delete($_POST['id']);
                }
                header('Location: index.php?page=spese');
                exit();
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
    
        $speseData = $spese->all();
        include 'views/spese.php';
        break;

    // Scadenze
    case 'scadenze':
        if (!$utenti->isAuthenticated()) {
            header('Location: index.php?page=login');
            exit();
        }

        require_once 'backend/scadenze.php';
        $scadenze = new Scadenze($db);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'];
            try {
                // Prepara i dati con valori di default per i checkbox

                $data = [
                    'titolo' => $_POST['titolo'] ?? '',
                    'descrizione' => $_POST['descrizione'] ?? '',
                    'data_scadenza' => $_POST['data_scadenza'] ?? '',
                    'avviso_email' => isset($_POST['avviso_email']) ? 1 : 0, // Default 0 se non selezionato
                    'avviso_push' => isset($_POST['avviso_push']) ? 1 : 0    // Default 0 se non selezionato
                ];

                if ($action === 'create') {
                    $scadenze->create($data);
                } elseif ($action === 'update') {
                    $scadenze->update($_POST['id'], $data);
                } elseif ($action === 'delete') {
                    $scadenze->delete($_POST['id']);
                }
                header('Location: index.php?page=scadenze');
                exit();
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }

        $scadenzeData = $scadenze->all();
        include 'views/scadenze.php';
        break;

    // Google
    case 'google':

        require_once 'backend/cantieri.php';
        $cantieri = new Cantieri($db);


        if (isset($_GET['ajax']) && $_GET['ajax'] === 'get_google_results') {
            $cantiereId = $_GET['cantiere_id'] ?? null;
            $indirizzo = $_GET['indirizzo'] ?? '';
            $type = $_GET['type'] ?? '';
            $keyword = $_GET['keyword'] ?? '';
    
            if ($cantiereId && $indirizzo && $type) {
                require_once 'backend/GooglePlaces.php';
    
                $googlePlaces = new GooglePlaces('AIzaSyBd8edLXXMfYjUj2BjK5yAsT5Q506fIwfc', $db);
                $results = $googlePlaces->searchNearbyByAddress($indirizzo, $type, $keyword);
    
                header('Content-Type: application/json');
                if (!isset($results['error'])) {
                    echo json_encode(['success' => true, 'results' => $results]);
                } else {
                    echo json_encode(['success' => false, 'message' => $results['message']]);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'ID Cantiere, Indirizzo o Tipo non fornito.']);
            }
            exit();
        }

        // Salva o elimina indirizzo memorizzato tramite GET
        if (isset($_GET['ajax']) && ($_GET['ajax'] === 'save_address' || $_GET['ajax'] === 'delete_address')) {
            header('Content-Type: application/json');
            try {
                // Estrai i parametri dalla richiesta GET
                $idCantiere = $_GET['id_cantiere'] ?? null;
                $nome = $_GET['name'] ?? null;
                $indirizzo = $_GET['address'] ?? null;
                $distanza = $_GET['distance'] ?? null;
                $latitudine = $_GET['lat'] ?? null;
                $longitudine = $_GET['lng'] ?? null;

                // Validazione dati
                if (!$idCantiere || !$indirizzo) {
                    throw new Exception("ID cantiere o indirizzo mancante.");
                }

                if ($_GET['ajax'] === 'save_address') {
                    // Salva l'indirizzo
                    $cantieri->addIndirizzo($idCantiere, $nome, $indirizzo, $distanza, $latitudine, $longitudine);
                    echo json_encode(['success' => true, 'message' => 'Indirizzo memorizzato con successo!']);
                } elseif ($_GET['ajax'] === 'delete_address') {
                    // Elimina l'indirizzo
                    $cantieri->deleteIndirizzo($idCantiere, $indirizzo);
                    echo json_encode(['success' => true, 'message' => 'Indirizzo eliminato con successo!']);
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            exit();
        }
            
        // Recupera gli indirizzi memorizzati per un cantiere
        if (isset($_GET['ajax']) && $_GET['ajax'] === 'get_saved_addresses') {
            header('Content-Type: application/json');
            try {
                $idCantiere = $_GET['id_cantiere'] ?? null;

                if (!$idCantiere) {
                    throw new Exception("ID cantiere mancante.");
                }

                // Recupera gli indirizzi utilizzando il metodo della classe Cantieri
                $address = $cantieri->getIndirizzi($idCantiere);

                echo json_encode(['success' => true, 'addresses' => $address]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            exit();
        }
        break;    

    // Materiali Cantiere
    case 'materiali_cantiere':
        if (!$utenti->isAuthenticated()) {
            header('Location: index.php?page=login');
            exit();
        }

        require_once 'backend/materiali_cantiere.php';
        $materialiCantiere = new MaterialiCantiere($db);

        // Gestione delle richieste AJAX via GET
        $ajaxAction = $_GET['ajax'] ?? null;

        if ($ajaxAction === 'load') {
            $idCantiere = $_GET['id_cantiere'] ?? null;
            if (!$idCantiere) {
                echo json_encode(['error' => 'ID cantiere mancante.']);
                exit();
            }

            try {
                $materials = $materialiCantiere->getMaterialsByCantiere($idCantiere);
                header('Content-Type: application/json');
                echo json_encode($materials);
            } catch (Exception $e) {
                header('Content-Type: application/json');
                echo json_encode(['error' => $e->getMessage()]);
            }
            exit();
        }

        if ($ajaxAction === 'search') {
            try {
                $query = $_GET['query'] ?? '';

                if (strlen($query) < 2) {
                    throw new Exception('La query deve contenere almeno 2 caratteri.');
                }

                $results = $materialiCantiere->search($query);
                header('Content-Type: application/json');
                echo json_encode($results);
            } catch (Exception $e) {
                header('Content-Type: application/json');
                echo json_encode(['error' => $e->getMessage()]);
            }
            exit();
        }

        if ($ajaxAction === 'update') {
            $idMateriale = $_GET['id_materiale'] ?? null;
            $quantita = $_GET['quantita'] ?? null;
        
            if (!$idMateriale || !$quantita) {
                echo json_encode(['error' => 'ID materiale o quantità mancanti.']);
                exit();
            }
        
            try {
                $materialiCantiere->updateQuantity($idMateriale, $quantita);
                header('Content-Type: application/json');
                echo json_encode(['success' => true]);
            } catch (Exception $e) {
                header('Content-Type: application/json');
                echo json_encode(['error' => $e->getMessage()]);
            }
            exit();
        }        

        if ($ajaxAction === 'save') {

            $idCantiere = $_GET['id_cantiere'] ?? null;
            $materialsJson = $_GET['materiali'] ?? '[]'; // Materiali come JSON stringa

            $materials = json_decode($materialsJson, true);

            if (!$idCantiere || empty($materials)) {
                echo json_encode(['error' => 'ID cantiere o materiali mancanti.']);
                exit();
            }

            try {
                $materialiCantiere->saveMaterials($idCantiere, $materials);
                header('Content-Type: application/json');
                echo json_encode(['success' => true]);
            } catch (Exception $e) {
                header('Content-Type: application/json');
                echo json_encode(['error' => $e->getMessage()]);
            }
            exit();
        }

        if ($ajaxAction === 'delete') {
            $rawInput = file_get_contents('php://input');
            
            if (isset($_SERVER['CONTENT_TYPE']) && stripos($_SERVER['CONTENT_TYPE'], 'application/json') === 0) {
                $_POST = json_decode($rawInput, true);
            }
        
            $idMateriale = $_GET['id_materiale'] ?? null;
        
            if (!$idMateriale) {
                echo json_encode(['error' => 'ID materiale mancante.']);
                exit();
            }
        
            try {
                $materialiCantiere->deleteMaterial($idMateriale);
                header('Content-Type: application/json');
                echo json_encode(['success' => true]);
            } catch (Exception $e) {
                header('Content-Type: application/json');
                echo json_encode(['error' => $e->getMessage()]);
            }
            exit();
        }        

        break;

    

    // Stampa Preventivo PDF
    case 'stampa_preventivo':
        if (!$utenti->isAuthenticated()) {
            header('Location: index.php?page=login');
            exit();
        }

        require_once 'backend/preventivo.php';

        $preventivo = new Preventivo();
        
        $idCantiere = $_GET['id'] ?? null;
        if (!$idCantiere) {
            die("ID cantiere mancante.");
        }

        $preventivo->generaPDF($idCantiere);
        break;


    // Logout
    case 'logout':
        $utenti->logout();
        header('Location: index.php?page=login');
        exit();

    // Pagina 404
    default:
        include 'views/404.php';
        break;
}
