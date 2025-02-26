<?php

require_once 'base_model.php';

class Fornitori extends BaseModel
{
    private $table = 'fornitori';


    public function find($id)
    {
        $stmt = $this->conn->prepare("SELECT fornitori.*, comuni.comune AS comune, province.provincia AS provincia, regioni.regione AS regione
                                      FROM fornitori
                                      LEFT JOIN comuni ON fornitori.id_comune = comuni.id
                                      LEFT JOIN province ON fornitori.id_provincia = province.id
                                      LEFT JOIN regioni ON fornitori.id_regione = regioni.id
                                      WHERE fornitori.id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function all()
    {
        $query = "SELECT fornitori.*, comuni.comune AS comune, province.provincia AS provincia, regioni.regione AS regione
                  FROM fornitori
                  LEFT JOIN comuni ON fornitori.id_comune = comuni.id
                  LEFT JOIN province ON fornitori.id_provincia = province.id
                  LEFT JOIN regioni ON fornitori.id_regione = regioni.id";

        $result = $this->conn->query($query);
        if (!$result) {
            throw new Exception("Errore nella query: " . $this->conn->error);
        }

        return $result->fetch_all(MYSQLI_ASSOC);
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
        $this->validate($data, false);

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
                    fornitori.id, 
                    fornitori.nome, 
                    fornitori.cognome, 
                    comuni.comune AS comune, 
                    province.provincia AS provincia, 
                    regioni.regione AS regione 
                FROM fornitori
                JOIN comuni ON fornitori.id_comune = comuni.id
                JOIN province ON fornitori.id_provincia = province.id
                JOIN regioni ON fornitori.id_regione = regioni.id
                WHERE 1";

        if (!empty($filters['cliente'])) {
            $searchTerm = $this->conn->real_escape_string($filters['cliente']);
            $query .= " AND (CONCAT(fornitori.nome, ' ', fornitori.cognome) LIKE '%$searchTerm%' 
                        OR comuni.comune LIKE '%$searchTerm%' 
                        OR province.provincia LIKE '%$searchTerm%' 
                        OR regioni.regione LIKE '%$searchTerm%')";
        }

        $query .= " LIMIT 10";

        $result = $this->conn->query($query);

        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    private function validate($data, $checkEmail = true)
    {
        if (empty($data['societa']) || empty($data['email'])) {
            throw new Exception("I campi Società ed email sono obbligatori.");
        }

        if ($checkEmail && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("L'email non è valida.");
        }

        if (!empty($data['p_iva']) && !preg_match('/^[0-9]{11}$/', $data['p_iva'])) {
            throw new Exception("La partita IVA deve essere composta da 11 cifre.");
        }
    }
}
?>
