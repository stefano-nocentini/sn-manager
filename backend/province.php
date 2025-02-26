<?php
require_once 'base_model.php';

class Province extends BaseModel
{
    private $table = 'province';

    public function all()
    {
        $result = $this->conn->query("SELECT * FROM {$this->table}");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getAll() {

        $result = $this->conn->query("SELECT id, provincia FROM province ORDER BY provincia ASC");
        $province = [];
        while ($row = $result->fetch_assoc()) {
            $province[] = $row;
        }
        return $province;
    }

    public function find($idProvincia) {
        $stmt = $this->conn->prepare("SELECT * FROM province WHERE id = ?");
        $stmt->bind_param("i", $idProvincia);
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
        $query = "SELECT id, provincia FROM {$this->table} WHERE 1=1";
        $params = [];

        if (!empty($filters['provincia'])) {
            $query .= " AND provincia LIKE ?";
            $params[] = '%' . $filters['provincia'] . '%';
        }

        if (!empty($filters['id_regione'])) {
            $query .= " AND id_regione = ?";
            $params[] = $filters['id_regione'];
        }

        $query .= " ORDER BY provincia ASC";

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
