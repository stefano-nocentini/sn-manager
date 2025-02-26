<?php
require_once 'base_model.php';

class Comuni extends BaseModel
{
    private $table = 'comuni';

    public function all()
    {
        $result = $this->conn->query("SELECT * FROM {$this->table}");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function find($idComune) {
        $stmt = $this->conn->prepare("SELECT * FROM comuni WHERE id = ?");
        $stmt->bind_param("i", $idComune);
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

    public function getWithFilters($filters = [])
    {
        $query = "SELECT id, comune FROM comuni WHERE 1";

        if (!empty($filters['comune'])) {
            $searchTerm = $this->conn->real_escape_string($filters['comune']);
            $query .= " AND comune LIKE '%$searchTerm%'";
        }

        $query .= " ORDER BY comune ASC LIMIT 10";

        $result = $this->conn->query($query);
        if (!$result) {
            throw new Exception("Errore nella query: " . $this->conn->error);
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>