<?php
require_once 'base_model.php';

class Cantieri extends BaseModel
{
    private $table = 'cantieri';


    public function all()
    {
        $query = "
            SELECT 
                cantieri.*,
                clienti.nome AS cliente_nome,
                clienti.cognome AS cliente_cognome,
                posatori.nome AS posatore_nome,
                posatori.cognome AS posatore_cognome,
                comuni.comune AS comune_nome,
                comuni.cap AS cap_nome,
                province.provincia AS provincia_nome,
                regioni.regione AS regione_nome,
                stato_cantiere.stato_cantiere AS stato_cantiere_nome,
                stato_cantiere.colore AS stato_cantiere_colore,

                -- Calcolo del fatturato (importo)
                COALESCE(SUM(voci_cantiere.prezzo * voci_cantiere.quantita), 0) AS importo,

                -- Calcolo delle spese totali (materiali cantiere)
                (
                    SELECT COALESCE(SUM(materiali_cantiere.prezzo * materiali_cantiere.quantita), 0)
                    FROM materiali_cantiere 
                    WHERE materiali_cantiere.id_cantiere = cantieri.id
                ) AS spese,

                -- Calcolo del guadagno (importo - spese)
                (
                    COALESCE(SUM(voci_cantiere.prezzo * voci_cantiere.quantita), 0) - 
                    (
                        SELECT COALESCE(SUM(materiali_cantiere.prezzo * materiali_cantiere.quantita), 0)
                        FROM materiali_cantiere 
                        WHERE materiali_cantiere.id_cantiere = cantieri.id
                    )
                ) AS guadagno

            FROM cantieri
            LEFT JOIN clienti ON cantieri.id_cliente = clienti.id
            LEFT JOIN posatori ON cantieri.id_posatore = posatori.id
            LEFT JOIN comuni ON cantieri.id_comune = comuni.id
            LEFT JOIN province ON cantieri.id_provincia = province.id
            LEFT JOIN regioni ON cantieri.id_regione = regioni.id
            LEFT JOIN stato_cantiere ON cantieri.id_stato_cantiere = stato_cantiere.id
            LEFT JOIN voci_cantiere ON cantieri.id = voci_cantiere.id_cantiere
            GROUP BY 
                cantieri.id, 
                clienti.nome, 
                clienti.cognome, 
                posatori.nome, 
                posatori.cognome, 
                comuni.comune, 
                comuni.cap,
                province.provincia, 
                regioni.regione, 
                stato_cantiere.stato_cantiere,
                stato_cantiere.colore 
            ORDER BY cantieri.id DESC
        ";

        $result = $this->conn->query($query);
        if (!$result) {
            throw new Exception("Errore nella query: " . $this->conn->error);
        }

        $data = $result->fetch_all(MYSQLI_ASSOC);

        // Sanitizza i dati
        foreach ($data as &$row) {
            foreach ($row as $key => $value) {
                $row[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            }
        }

        return $data;
    }


    public function find($id)
    {
        $stmt = $this->conn->prepare("SELECT cantieri.*, comuni.comune AS comune, province.provincia AS provincia, regioni.regione AS regione
                                      FROM cantieri
                                      LEFT JOIN comuni ON cantieri.id_comune = comuni.id
                                      LEFT JOIN province ON cantieri.id_provincia = province.id
                                      LEFT JOIN regioni ON cantieri.id_regione = regioni.id
                                      WHERE cantieri.id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    public function find2($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function create($data)
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO cantieri (id_cliente, id_posatore, id_comune, id_provincia, id_regione, id_stato_cantiere, indirizzo, note, data_inizio, data_fine) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        
        $stmt->bind_param(
            'iiiiiissss',
            $data['id_cliente'],
            $data['id_posatore'],
            $data['id_comune'],
            $data['id_provincia'],
            $data['id_regione'],
            $data['id_stato_cantiere'],
            $data['indirizzo'],
            $data['note'],
            $data['data_inizio'],
            $data['data_fine']
        );

        if (!$stmt->execute()) {
            throw new Exception("Errore nell'inserimento del cantiere: " . $stmt->error);
        }

        // Restituisce l'ID del cantiere appena creato
        return $this->conn->insert_id;
    }

    public function addVociCantiere($idCantiere, $vociData)
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO voci_cantiere (id_cantiere, id_listino, quantita, prezzo) 
            VALUES (?, ?, ?, ?)"
        );
    
        foreach ($vociData as $voce) {
            $stmt->bind_param(
                'iiid', 
                $idCantiere, 
                $voce['id_listino'], 
                $voce['quantita'], 
                $voce['prezzo']
            );
    
            if (!$stmt->execute()) {
                throw new Exception("Errore nell'inserimento delle voci cantiere: " . $stmt->error);
            }
        }
        return true;
    }
    
    public function update($id, $data)
    {
        $stmt = $this->conn->prepare(
            "UPDATE {$this->table} SET id_cliente = ?, id_posatore = ?, id_comune = ?, id_provincia = ?, id_regione = ?, id_stato_cantiere = ?, indirizzo = ?, note = ?, data_inizio = ?, data_fine = ? WHERE id = ?"
        );

        $stmt->bind_param(
            'iiiiiissssi',
            $data['id_cliente'],
            $data['id_posatore'],
            $data['id_comune'],
            $data['id_provincia'],
            $data['id_regione'],
            $data['id_stato_cantiere'],
            $data['indirizzo'],
            $data['note'],
            $data['data_inizio'],
            $data['data_fine'],
            $id
        );
        if (!$stmt->execute()) {
            throw new Exception("Errore nella modifica: " . $stmt->error);
        }
        return true;
    }

    public function updateWithVoci($id, $data, $vociData)
    {
        // Inizia una transazione
        $this->conn->begin_transaction();
    
        try {
            // Aggiorna i dati del cantiere
            $stmt = $this->conn->prepare(
                "UPDATE {$this->table} SET id_cliente = ?, id_posatore = ?, id_comune = ?, id_provincia = ?, id_regione = ?, id_stato_cantiere = ?, indirizzo = ?, note = ?, data_inizio = ?, data_fine = ? WHERE id = ?"
            );
            $stmt->bind_param(
                'iiiiiissssi',
                $data['id_cliente'],
                $data['id_posatore'],
                $data['id_comune'],
                $data['id_provincia'],
                $data['id_regione'],
                $data['id_stato_cantiere'],
                $data['indirizzo'],
                $data['note'],
                $data['data_inizio'],
                $data['data_fine'],
                $id
            );
            if (!$stmt->execute()) {
                throw new Exception("Errore nella modifica del cantiere: " . $stmt->error);
            }

            // Recupera le voci esistenti nel database
            $stmt = $this->conn->prepare("SELECT id FROM voci_cantiere WHERE id_cantiere = ?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $vociEsistenti = $result->fetch_all(MYSQLI_ASSOC);
    
            // Elenco degli ID delle voci esistenti
            $idVociEsistenti = array_column($vociEsistenti, 'id');

            // Gestione delle voci: aggiorna, inserisce nuove voci o elimina quelle non più necessarie
            foreach ($vociData as $voce) {
                if (!empty($voce['id_voce_cantiere'])) {
                    // Aggiorna le voci esistenti
                    $stmt = $this->conn->prepare("
                        UPDATE voci_cantiere 
                        SET id_listino = ?, quantita = ?, prezzo = ?
                        WHERE id = ? AND id_cantiere = ?
                    ");
                    $stmt->bind_param(
                        'iidii',
                        $voce['id_listino'],
                        $voce['quantita'],
                        $voce['prezzo'],
                        $voce['id_voce_cantiere'],
                        $id
                    );
                    if (!$stmt->execute()) {
                        throw new Exception("Errore nell'aggiornamento delle voci cantiere: " . $stmt->error);
                    }

                    // Rimuovi questa voce dall'elenco delle voci da eliminare
                    $idVociEsistenti = array_diff($idVociEsistenti, [$voce['id_voce_cantiere']]);
                } else {
                    // Inserisce nuove voci
                    $stmt = $this->conn->prepare("
                        INSERT INTO voci_cantiere (id_cantiere, id_listino, quantita, prezzo) 
                        VALUES (?, ?, ?, ?)
                    ");
                    $stmt->bind_param(
                        'iiid',
                        $id,
                        $voce['id_listino'],
                        $voce['quantita'],
                        $voce['prezzo']
                    );
                    if (!$stmt->execute()) {
                        throw new Exception("Errore nell'inserimento delle voci cantiere: " . $stmt->error);
                    }
                }
            }
    
            // Elimina le voci non più presenti
            if (!empty($idVociEsistenti)) {
                $idsToDelete = implode(',', $idVociEsistenti);
                $stmt = $this->conn->prepare("DELETE FROM voci_cantiere WHERE id IN ($idsToDelete)");
                if (!$stmt->execute()) {
                    throw new Exception("Errore nell'eliminazione delle voci cantiere: " . $stmt->error);
                }
            }
    
            // Conferma la transazione
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            // Rollback in caso di errore
            $this->conn->rollback();
            throw $e;
        }
    }
    
    public function delete($id)
    {
        $this->conn->begin_transaction(); // Avvia una transazione
        try {
            // Eliminazione del cantiere (le voci vengono eliminate automaticamente tramite ON DELETE CASCADE)
            $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id = ?");
            $stmt->bind_param('i', $id);
            if (!$stmt->execute()) {
                throw new Exception("Errore nell'eliminazione del cantiere: " . $stmt->error);
            }
    
            $this->conn->commit(); // Conferma la transazione
            return true;
        } catch (Exception $e) {
            $this->conn->rollback(); // Annulla la transazione in caso di errore
            throw $e;
        }
    }

    public function getWithFilters(array $filters = [])
    {
        $query = "SELECT * FROM {$this->table} WHERE 1=1";
        $params = [];

        if (!empty($filters['id_cliente'])) {
            $query .= " AND id_cliente = ?";
            $params[] = $filters['id_cliente'];
        }

        if (!empty($filters['id_comune'])) {
            $query .= " AND id_comune = ?";
            $params[] = $filters['id_comune'];
        }

        if (!empty($filters['id_provincia'])) {
            $query .= " AND id_provincia = ?";
            $params[] = $filters['id_provincia'];
        }

        if (!empty($filters['id_regione'])) {
            $query .= " AND id_regione = ?";
            $params[] = $filters['id_regione'];
        }

        
        $stmt = $this->conn->prepare($query);

        if (!empty($params)) {
            $types = '';
            foreach ($params as $param) {
                $types .= is_int($param) ? 'i' : 's';
            }
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        return $result;
    }

    public function getVoci($idCantiere)
    {
        $stmt = $this->conn->prepare("
            SELECT voci_cantiere.*, listino.voce AS nome_voce 
            FROM voci_cantiere
            LEFT JOIN listino ON voci_cantiere.id_listino = listino.id
            WHERE voci_cantiere.id_cantiere = ?
        ");
        $stmt->bind_param('i', $idCantiere);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if (!$result) {
            $error = $this->conn->error;
            throw new Exception("Errore nella query: $error");
        }
    
        $voci = $result->fetch_all(MYSQLI_ASSOC);

        return $voci;
    }

    public function getDettagli($id)
    {
        // Recupera i dettagli del cantiere
        $stmt = $this->conn->prepare("
            SELECT 
                cantieri.*,
                clienti.nome AS cliente_nome, clienti.cognome AS cliente_cognome,
                posatori.nome AS posatore_nome, posatori.cognome AS posatore_cognome,
                comuni.comune AS comune_nome,
                province.provincia AS provincia_nome,
                regioni.regione AS regione_nome,
                stato_cantiere.stato_cantiere AS stato_cantiere_nome
            FROM cantieri
            LEFT JOIN clienti ON cantieri.id_cliente = clienti.id
            LEFT JOIN posatori ON cantieri.id_posatore = posatori.id
            LEFT JOIN comuni ON cantieri.id_comune = comuni.id
            LEFT JOIN province ON cantieri.id_provincia = province.id
            LEFT JOIN regioni ON cantieri.id_regione = regioni.id
            LEFT JOIN stato_cantiere ON cantieri.id_stato_cantiere = stato_cantiere.id
            WHERE cantieri.id = ?
        ");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $cantiereDettagli = $stmt->get_result()->fetch_assoc();

        if (!$cantiereDettagli) {
            throw new Exception("Cantiere non trovato.");
        }

        // Recupera le voci associate al cantiere
        $stmt = $this->conn->prepare("
            SELECT 
                voci_cantiere.*, 
                listino.voce AS nome_voce
            FROM voci_cantiere
            LEFT JOIN listino ON voci_cantiere.id_listino = listino.id
            WHERE voci_cantiere.id_cantiere = ?
        ");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $vociDettagli = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Calcola il totale dell'importo sommando prezzo * quantita per ogni voce
        $stmt = $this->conn->prepare("
            SELECT SUM(voci_cantiere.prezzo * voci_cantiere.quantita) AS importo_totale
            FROM voci_cantiere
            WHERE voci_cantiere.id_cantiere = ?
        ");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $importo = $stmt->get_result()->fetch_assoc();
        
        // Aggiunge l'importo totale ai dettagli del cantiere
        $cantiereDettagli['importo_totale'] = $importo['importo_totale'] ?? 0;

        // Ritorna un array combinato
        return [
            'cantiere' => $cantiereDettagli,
            'voci' => $vociDettagli
        ];
    } 
    
    
    public function getTotali($id_cantiere = null)
    {
        $query = "
            SELECT 
                COUNT(DISTINCT c.id) AS totale_cantieri, 
                COALESCE(SUM(vc.prezzo), 0) AS totale_importo,
                COALESCE(SUM(sc.prezzo), 0) AS totale_spese,
                (COALESCE(SUM(vc.prezzo), 0) - COALESCE(SUM(sc.prezzo), 0)) AS totale_guadagno
            FROM cantieri c
            LEFT JOIN voci_cantiere vc ON c.id = vc.id_cantiere
            LEFT JOIN spese_cantiere sc ON c.id = sc.id_cantiere
        ";

        if ($id_cantiere !== null) {
            $query .= " WHERE c.id = ?";
        }

        $stmt = $this->conn->prepare($query);

        if ($id_cantiere !== null) {
            $stmt->bind_param('i', $id_cantiere);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        if (!$result) {
            throw new Exception("Errore nel recupero dei totali: " . $this->conn->error);
        }

        $row = $result->fetch_assoc();

        return [
            'totale_cantieri' => $row['totale_cantieri'] ?? 0,
            'totale_importo' => $row['totale_importo'] ?? 0,
            'totale_spese' => $row['totale_spese'] ?? 0,
            'totale_guadagno' => $row['totale_guadagno'] ?? 0
        ];
    }


    public function addIndirizzo($idCantiere, $nome, $indirizzo, $distanza, $latitudine, $longitudine)
    {
        $stmt = $this->conn->prepare("
            INSERT INTO cantiere_indirizzi (id_cantiere, nome, indirizzo, distanza, latitudine, longitudine)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param(
            'isssdd',
            $idCantiere,
            $nome,
            $indirizzo,
            $distanza,
            $latitudine,
            $longitudine
        );

        if (!$stmt->execute()) {
            throw new Exception("Errore nel salvataggio dell'indirizzo: " . $stmt->error);
        }

        return $this->conn->insert_id; // Restituisce l'ID dell'indirizzo appena creato
    }

    public function getIndirizzi($idCantiere)
    {
        $stmt = $this->conn->prepare("
            SELECT *
            FROM cantiere_indirizzi
            WHERE id_cantiere = ?
            ORDER BY id DESC
        ");
        $stmt->bind_param('i', $idCantiere);
        $stmt->execute();
        $result = $stmt->get_result();

        if (!$result) {
            throw new Exception("Errore nel recupero degli indirizzi: " . $this->conn->error);
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function deleteIndirizzo($idCantiere, $indirizzo)
    {
        $stmt = $this->conn->prepare("
            DELETE FROM cantiere_indirizzi
            WHERE id_cantiere = ? AND indirizzo = ?
        ");
        $stmt->bind_param('is', $idCantiere, $indirizzo);

        if (!$stmt->execute()) {
            throw new Exception("Errore durante l'eliminazione dell'indirizzo: " . $this->conn->error);
        }

        return true;
    }

    public function getClienteData($idCliente)
    {
        $stmt = $this->conn->prepare("
            SELECT 
                clienti.id,
                clienti.indirizzo,
                comuni.comune AS comune_nome, comuni.id AS id_comune,
                province.provincia AS provincia_nome, province.id AS id_provincia,
                regioni.regione AS regione_nome, regioni.id AS id_regione
            FROM clienti
            LEFT JOIN comuni ON clienti.id_comune = comuni.id
            LEFT JOIN province ON comuni.id_provincia = province.id
            LEFT JOIN regioni ON province.id_regione = regioni.id
            WHERE clienti.id = ?
        ");

        $stmt->bind_param('i', $idCliente); // Associa il parametro come intero
        $stmt->execute();
        $result = $stmt->get_result(); // Ottieni il risultato della query

        if (!$result) {
            throw new Exception("Errore nel recupero dei dati del cliente: " . $this->conn->error);
        }

        return $result->fetch_assoc(); // Restituisce una riga come array associativo
    }

    public function getPreventivo($idCantiere)
    {
        $stmt = $this->conn->prepare("
            SELECT 
                cantieri.id, cantieri.data_inizio AS data, 
                clienti.nome AS cliente_nome, clienti.cognome AS cliente_cognome,
                comuni.comune AS comune, province.provincia AS provincia
            FROM cantieri
            LEFT JOIN clienti ON cantieri.id_cliente = clienti.id
            LEFT JOIN comuni ON cantieri.id_comune = comuni.id
            LEFT JOIN province ON cantieri.id_provincia = province.id
            WHERE cantieri.id = ?
        ");
        $stmt->bind_param('i', $idCantiere);
        $stmt->execute();
        $cantiereDettagli = $stmt->get_result()->fetch_assoc();

        if (!$cantiereDettagli) {
            throw new Exception("Cantiere non trovato.");
        }

        // Recupera le voci associate al cantiere
        $stmt = $this->conn->prepare("
            SELECT 
                voci_cantiere.quantita, 
                listino.voce AS descrizione, 
                voci_cantiere.prezzo
            FROM voci_cantiere
            LEFT JOIN listino ON voci_cantiere.id_listino = listino.id
            WHERE voci_cantiere.id_cantiere = ?
        ");
        $stmt->bind_param('i', $idCantiere);
        $stmt->execute();
        $vociDettagli = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Calcolo del totale
        $subtotale = 0;
        foreach ($vociDettagli as $voce) {
            $subtotale += $voce['quantita'] * $voce['prezzo'];
        }

        return [
            'cantiere' => $cantiereDettagli,
            'voci' => $vociDettagli,
            'subtotale' => $subtotale,
            'iva' => 0, // IVA sempre 0%
            'totale' => $subtotale
        ];
    }

}
?>
