<?php
require_once 'base_model.php';

class Regioni extends BaseModel
{
    private $table = 'regioni';

    public function all()
    {
        $result = $this->conn->query("SELECT * FROM {$this->table}");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getAll() {
        $result = $this->conn->query("SELECT id, regione FROM regioni ORDER BY regione ASC");
        $regioni = [];
        while ($row = $result->fetch_assoc()) {
            $regioni[] = $row;
        }
        return $regioni;
    }

    public function find($idRegione) {
        $stmt = $this->conn->prepare("SELECT * FROM regioni WHERE id = ?");
        $stmt->bind_param("i", $idRegione);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    

    public function create($data)
    {}

    public function update($id, $data)
    {}

    public function delete($id)
    {}

    public function getWithFilters(array $filters = [])
    {
        $query = "SELECT id, regione FROM {$this->table} WHERE 1=1";
        $params = [];

        if (!empty($filters['regione'])) {
            $query .= " AND regione LIKE ?";
            $params[] = '%' . $filters['regione'] . '%';
        }

        $query .= " ORDER BY regione ASC";

        $stmt = $this->conn->prepare($query);

        if (!empty($params)) {
            $types = str_repeat('s', count($params)); // Imposta il tipo dei parametri
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>
