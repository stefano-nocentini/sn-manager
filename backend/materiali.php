<?php

require_once 'base_model.php';

class Materiali extends BaseModel
{
    private $table = 'materiali';

    // Trova un materiale per ID
    public function find($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Ritorna tutti i materiali
    public function all()
    {
        $result = $this->conn->query("SELECT * FROM {$this->table}");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Crea un nuovo materiale
    public function create($data)
    {
        $stmt = $this->conn->prepare("INSERT INTO {$this->table} (nome, descrizione, prezzo) VALUES (?, ?, ?)");
        $stmt->bind_param('ssd', $data['nome'], $data['descrizione'], $data['prezzo']);
        return $stmt->execute();
    }

    // Aggiorna un materiale
    public function update($id, $data)
    {
        $stmt = $this->conn->prepare("UPDATE {$this->table} SET nome = ?, descrizione = ?, prezzo = ? WHERE id = ?");
        $stmt->bind_param('ssdi', $data['nome'], $data['descrizione'], $data['prezzo'], $id);
        return $stmt->execute();
    }

    // Elimina un materiale
    public function delete($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id = ?");
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }
}

?>
