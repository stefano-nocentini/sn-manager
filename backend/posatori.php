<?php

require_once 'base_model.php';

class Posatori extends BaseModel
{
    private $table = 'posatori';

    // Implementazione dei metodi astratti
    public function find($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function all()
    {
        $query = "SELECT posatori.*, comuni.comune AS comune, province.provincia AS provincia, regioni.regione AS regione
                  FROM posatori
                  LEFT JOIN comuni ON posatori.id_comune = comuni.id
                  LEFT JOIN province ON posatori.id_provincia = province.id
                  LEFT JOIN regioni ON posatori.id_regione = regioni.id";

        $result = $this->conn->query($query);
        if (!$result) {
            throw new Exception("Errore nella query: " . $this->conn->error);
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function all2()
    {
        $result = $this->conn->query("SELECT * FROM {$this->table}");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function create($data)
    {
        // Validazione dei dati
        $this->validate($data);

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
            throw new Exception("Errore nella creazione del posatore: " . $this->conn->error);
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
            throw new Exception("Errore nella cancellazione: " . $this->conn->error);
        }

        return true;
    }

    // Metodi personalizzati
    public function findByEmail($email)
    {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    private function validate($data, $checkEmail = true)
    {
        if (empty($data['nome']) || empty($data['cognome']) || empty($data['email'])) {
            throw new Exception("I campi nome, cognome ed email sono obbligatori.");
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
