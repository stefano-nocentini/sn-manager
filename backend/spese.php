<?php

require_once 'base_model.php';

class Spese extends BaseModel
{
    private $table = 'spese';


    public function find($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function all()
    {
        $query = "SELECT spese.*, motivo_spese.motivo_spesa 
                  FROM spese
                  LEFT JOIN motivo_spese ON spese.id_motivo_spesa = motivo_spese.id";

        $result = $this->conn->query($query);
        if (!$result) {
            throw new Exception("Errore nella query: " . $this->conn->error);
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getTotal()
    {
        $query = "SELECT SUM(importo) as totale FROM {$this->table}";
        $result = $this->conn->query($query);
        $row = $result->fetch_assoc();
        return $row['totale'] ?? 0; // Ritorna 0 se non ci sono risultati
    }


    public function create($data)
    {
        if (empty($data['descrizione']) || empty($data['importo']) || empty($data['data_spesa']) || empty($data['id_motivo_spesa'])) {
            throw new Exception("Tutti i campi sono obbligatori.");
        }

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['data_spesa'])) {
            throw new Exception("Il formato della data è errato. Usa il formato YYYY-MM-DD.");
        }

        $query = "INSERT INTO {$this->table} (descrizione, importo, data_spesa, id_motivo_spesa)
                  VALUES ('{$data['descrizione']}', {$data['importo']}, '{$data['data_spesa']}', {$data['id_motivo_spesa']})";

        if (!$this->conn->query($query)) {
            throw new Exception("Errore nell'inserimento: " . $this->conn->error);
        }

        return true;
    }


    public function update($id, $data)
    {
        if (empty($data['descrizione']) || empty($data['importo']) || empty($data['data_spesa']) || empty($data['id_motivo_spesa'])) {
            throw new Exception("Tutti i campi sono obbligatori.");
        }

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['data_spesa'])) {
            throw new Exception("Il formato della data è errato. Usa il formato YYYY-MM-DD.");
        }

        $query = "UPDATE {$this->table}
                  SET descrizione = '{$data['descrizione']}', 
                      importo = {$data['importo']}, 
                      data_spesa = '{$data['data_spesa']}', 
                      id_motivo_spesa = {$data['id_motivo_spesa']}
                  WHERE id = {$id}";

        // Esecuzione della query
        if (!$this->conn->query($query)) {
            throw new Exception("Errore nell'aggiornamento: " . $this->conn->error);
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
}

?>
