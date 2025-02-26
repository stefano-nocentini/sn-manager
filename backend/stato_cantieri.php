<?php
require_once 'base_model.php';

class StatoCantiere extends BaseModel
{
    private $table = 'stato_cantiere';

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
        $stmt = $this->conn->prepare("INSERT INTO {$this->table} (stato_cantiere, colore) VALUES (?, ?)");
        $stmt->bind_param('ss', $data['stato_cantiere'], $data['colore']);
        if (!$stmt->execute()) {
            throw new Exception("Errore nell'inserimento: " . $stmt->error);
        }
        return true;
    }

    public function update($id, $data)
    {
        $stmt = $this->conn->prepare("UPDATE {$this->table} SET stato_cantiere = ?, colore = ? WHERE id = ?");
        $stmt->bind_param('ssi', $data['stato_cantiere'], $data['colore'], $id);
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

        if (!empty($filters['stato_cantiere'])) {
            $query .= " AND stato_cantiere LIKE ?";
            $params[] = '%' . $filters['stato_cantiere'] . '%';
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
