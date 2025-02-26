<?php
require_once 'base_model.php';

class Listino extends BaseModel
{
    private $table = 'listino';


    public function all()
    {
        $query = "SELECT listino.*, tipologia.nome AS tipologia_nome 
                FROM {$this->table} 
                LEFT JOIN tipologia ON listino.id_tipologia = tipologia.id 
                ORDER BY listino.id_tipologia ASC, listino.voce ASC";

        $result = $this->conn->query($query);
        if (!$result) {
            throw new Exception("Errore nell'esecuzione della query: " . $this->conn->error);
        }
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function find($id)
    {
        $stmt = $this->conn->prepare("SELECT listino.*, tipologia.nome AS tipologia_nome FROM {$this->table} LEFT JOIN tipologia ON listino.id_tipologia = tipologia.id WHERE listino.id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function create($data)
    {
        $stmt = $this->conn->prepare("INSERT INTO {$this->table} (voce, prezzo, id_tipologia) VALUES (?, ?, ?)");
        $stmt->bind_param('ssi', $data['voce'], $data['prezzo'], $data['id_tipologia']);
        if (!$stmt->execute()) {
            throw new Exception("Errore nell'inserimento: " . $stmt->error);
        }
        return true;
    }

    public function update($id, $data)
    {
        $stmt = $this->conn->prepare("UPDATE {$this->table} SET voce = ?, prezzo = ?, id_tipologia = ? WHERE id = ?");
        $stmt->bind_param('ssii', $data['voce'], $data['prezzo'], $data['id_tipologia'], $id);
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
