<?php
require_once 'base_model.php';

class MotivoSpese extends BaseModel
{
    private $table = 'motivo_spese';

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
        $stmt = $this->conn->prepare("INSERT INTO {$this->table} (motivo_spesa) VALUES (?)");
        $stmt->bind_param('s', $data['motivo_spesa']);
        if (!$stmt->execute()) {
            throw new Exception("Errore nell'inserimento: " . $stmt->error);
        }
        return true;
    }

    public function update($id, $data)
    {
        $stmt = $this->conn->prepare("UPDATE {$this->table} SET motivo_spesa = ? WHERE id = ?");
        $stmt->bind_param('si', $data['motivo_spesa'], $id);
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

    public function count()
    {
        $result = $this->conn->query("SELECT COUNT(*) as total FROM {$this->table}");
        $data = $result->fetch_assoc();
        return $data['total'] ?? 0;
    }

    public function getWithFilters(array $filters = [])
    {
        $query = "SELECT * FROM {$this->table} WHERE 1=1";
        $params = [];

        if (!empty($filters['motivo_spesa'])) {
            $query .= " AND motivo_spesa LIKE ?";
            $params[] = '%' . $filters['motivo_spesa'] . '%';
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
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>
