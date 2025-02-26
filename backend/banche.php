<?php
require_once 'base_model.php';

class Banche extends BaseModel
{
    private $table = 'banche';

    public function all()
    {
        $query = "SELECT banche.*, comuni.comune AS comune, province.provincia AS provincia, regioni.regione AS regione
                  FROM banche
                  LEFT JOIN comuni ON banche.id_comune = comuni.id
                  LEFT JOIN province ON banche.id_provincia = province.id
                  LEFT JOIN regioni ON banche.id_regione = regioni.id";

        $result = $this->conn->query($query);
        if (!$result) {
            throw new Exception("Errore nella query: " . $this->conn->error);
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function find($id)
    {
        $stmt = $this->conn->prepare("SELECT banche.*, comuni.comune AS comune, province.provincia AS provincia, regioni.regione AS regione
                                      FROM banche
                                      LEFT JOIN comuni ON banche.id_comune = comuni.id
                                      LEFT JOIN province ON banche.id_provincia = province.id
                                      LEFT JOIN regioni ON banche.id_regione = regioni.id
                                      WHERE banche.id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function create($data)
    {
        $query = "INSERT INTO {$this->table} (agenzia, iban, bic, swift, telefono, indirizzo, id_comune, id_provincia, id_regione) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param(
            'ssssssiii',
            $data['agenzia'],
            $data['iban'],
            $data['bic'],
            $data['swift'],
            $data['telefono'], 
            $data['indirizzo'], 
            $data['id_comune'], 
            $data['id_provincia'],
            $data['id_regione']
        );

        if (!$stmt->execute()) {
            throw new Exception("Errore nell'inserimento: " . $stmt->error);
        }
        return true;
    }

    public function update($id, $data)
    {
        $query = "UPDATE {$this->table} SET agenzia = ?, iban = ?, bic = ?, swift = ?, telefono = ?, indirizzo = ?, id_comune = ?, id_provincia = ?, id_regione = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param(
            'ssssssiiii',
            $data['agenzia'],
            $data['iban'],
            $data['bic'],
            $data['swift'],
            $data['telefono'], 
            $data['indirizzo'], 
            $data['id_comune'], 
            $data['id_provincia'],
            $data['id_regione'],
            $id
        );

        if (!$stmt->execute()) {
            throw new Exception("Errore nell'aggiornamento del posatore: " . $this->conn->error);
        }

        return true;
    }

    public function delete($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id = ?");
        $stmt->bind_param('i', $id);
        if (!$stmt->execute()) {
            throw new Exception("Errore nell'eliminazione: " . $stmt->error);
        }
        return true;
    }

    public function getWithFilters($filters = [])
    {
        $query = "SELECT 
                    banche.id, 
                    banche.nome, 
                    banche.cognome, 
                    comuni.comune AS comune, 
                    province.provincia AS provincia, 
                    regioni.regione AS regione 
                FROM banche
                JOIN comuni ON banche.id_comune = comuni.id
                JOIN province ON banche.id_provincia = province.id
                JOIN regioni ON banche.id_regione = regioni.id
                WHERE 1";

        if (!empty($filters['cliente'])) {
            $searchTerm = $this->conn->real_escape_string($filters['cliente']);
            $query .= " AND (CONCAT(banche.nome, ' ', banche.cognome) LIKE '%$searchTerm%' 
                        OR comuni.comune LIKE '%$searchTerm%' 
                        OR province.provincia LIKE '%$searchTerm%' 
                        OR regioni.regione LIKE '%$searchTerm%')";
        }

        $query .= " LIMIT 10";

        $result = $this->conn->query($query);

        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
}
?>
