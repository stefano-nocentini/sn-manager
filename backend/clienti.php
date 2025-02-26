<?php
require_once 'base_model.php';

class Clienti extends BaseModel
{
    private $table = 'clienti';

    public function all()
    {
        $query = "SELECT clienti.*, comuni.comune AS comune, province.provincia AS provincia, regioni.regione AS regione
                  FROM clienti
                  LEFT JOIN comuni ON clienti.id_comune = comuni.id
                  LEFT JOIN province ON clienti.id_provincia = province.id
                  LEFT JOIN regioni ON clienti.id_regione = regioni.id";

        $result = $this->conn->query($query);
        if (!$result) {
            throw new Exception("Errore nella query: " . $this->conn->error);
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function find($id)
    {
        $stmt = $this->conn->prepare("SELECT clienti.*, comuni.comune AS comune, province.provincia AS provincia, regioni.regione AS regione
                                      FROM clienti
                                      LEFT JOIN comuni ON clienti.id_comune = comuni.id
                                      LEFT JOIN province ON clienti.id_provincia = province.id
                                      LEFT JOIN regioni ON clienti.id_regione = regioni.id
                                      WHERE clienti.id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function create($data)
    {
        $query = "INSERT INTO {$this->table} (societa, nome, cognome, indirizzo, id_comune, id_provincia, id_regione, p_iva, rea, telefono, email, pec) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                  
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param(
            'ssssiiisssss',
            $data['societa'],
            $data['nome'],
            $data['cognome'],
            $data['indirizzo'],
            $data['id_comune'], 
            $data['id_provincia'], 
            $data['id_regione'], 
            $data['p_iva'],
            $data['rea'],
            $data['telefono'],
            $data['email'],
            $data['pec']
        );

        if (!$stmt->execute()) {
            throw new Exception("Errore nell'inserimento: " . $stmt->error);
        }
        return true;
    }

    public function update($id, $data)
    {
        $query = "UPDATE {$this->table} SET societa = ?, nome = ?, cognome = ?, indirizzo = ?, id_comune = ?, id_provincia = ?, id_regione = ?, p_iva = ?, rea = ?, telefono = ?, email = ?, pec = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param(
            'ssssiiisssssi',
            $data['societa'],
            $data['nome'],
            $data['cognome'],
            $data['indirizzo'],
            $data['id_comune'], 
            $data['id_provincia'], 
            $data['id_regione'], 
            $data['p_iva'],
            $data['rea'],
            $data['telefono'],
            $data['email'],
            $data['pec'],
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
                clienti.id, 
                clienti.nome, 
                clienti.cognome, 
                IFNULL(comuni.comune, '') AS comune, 
                IFNULL(province.provincia, '') AS provincia, 
                IFNULL(regioni.regione, '') AS regione 
            FROM clienti
            LEFT JOIN comuni ON clienti.id_comune = comuni.id
            LEFT JOIN province ON clienti.id_provincia = province.id
            LEFT JOIN regioni ON clienti.id_regione = regioni.id
            WHERE 1";

        if (!empty($filters['cliente'])) {
        $searchTerm = $this->conn->real_escape_string($filters['cliente']);
        $query .= " AND (CONCAT(clienti.nome, ' ', clienti.cognome) LIKE '%$searchTerm%' 
                    OR IFNULL(comuni.comune, '') LIKE '%$searchTerm%' 
                    OR IFNULL(province.provincia, '') LIKE '%$searchTerm%' 
                    OR IFNULL(regioni.regione, '') LIKE '%$searchTerm%')";
        }

        $query .= " LIMIT 10";


        $result = $this->conn->query($query);

        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
}
?>