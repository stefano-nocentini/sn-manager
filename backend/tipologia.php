<?php
require_once 'base_model.php';

class Tipologia extends BaseModel
{
    private $table = 'tipologia';

    public function all()
    {
        $result = $this->conn->query("SELECT * FROM {$this->table}");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function find($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function create($data)
    {
        $stmt = $this->conn->prepare("INSERT INTO {$this->table} (nome) VALUES (?)");
        $stmt->bind_param('s', $data['nome']);
        if (!$stmt->execute()) {
            throw new Exception("Errore nell'inserimento: " . $stmt->error);
        }
        return true;
    }

    public function update($id, $data)
    {
        $stmt = $this->conn->prepare("UPDATE {$this->table} SET nome = ? WHERE id = ?");
        $stmt->bind_param('si', $data['nome'], $id);
        if (!$stmt->execute()) {
            throw new Exception("Errore nella modifica: " . $stmt->error);
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
}
?>
