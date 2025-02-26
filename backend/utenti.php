<?php

require_once 'base_model.php';

class Utenti extends BaseModel
{
    private $table = 'utenti';


    public function find($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function all()
    {
        $result = $this->conn->query("SELECT * FROM {$this->table}");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function create($data)
    {
        // Validazione dei dati
        $this->validate($data);

        $passwordHash = password_hash($data['password'], PASSWORD_BCRYPT);

        $query = "INSERT INTO {$this->table} (nome, cognome, telefono, email, password_hash) 
                  VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param(
            'sssss',
            $data['nome'],
            $data['cognome'],
            $data['telefono'],
            $data['email'],
            $passwordHash
        );

        if (!$stmt->execute()) {
            throw new Exception("Errore nella creazione dell'utente: " . $this->conn->error);
        }

        return true;
    }

    public function update($id, $data)
    {
        // Validazione dei dati
        $this->validate($data, false);

        $query = "UPDATE {$this->table} SET nome = ?, cognome = ?, telefono = ?, email = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param(
            'ssssi',
            $data['nome'],
            $data['cognome'],
            $data['telefono'],
            $data['email'],
            $id
        );

        if (!$stmt->execute()) {
            throw new Exception("Errore nell'aggiornamento dell'utente: " . $this->conn->error);
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
    public function register($data)
    {
        $this->create($data);
    }

    public function findByEmail($email)
    {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function login($email, $password)
    {
        $utente = $this->findByEmail($email);
        if (!$utente) {
            throw new Exception("Credenziali non valide.");
        }

        if (!password_verify($password, $utente['password_hash'])) {
            throw new Exception("Credenziali non valide.");
        }

        $_SESSION['utente_id'] = $utente['id'];
        $_SESSION['utente_nome'] = $utente['nome'];

        return $utente;
    }

    public function isAuthenticated()
    {
        return isset($_SESSION['utente_id']);
    }

    public function logout()
    {
        session_unset();
        session_destroy();
        return true;
    }

    private function validate($data, $checkPassword = true)
    {
        if (empty($data['email']) || empty($data['nome']) || empty($data['cognome'])) {
            throw new Exception("I campi email, nome e cognome sono obbligatori.");
        }

        if ($checkPassword && empty($data['password'])) {
            throw new Exception("Il campo password è obbligatorio.");
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("L'email non è valida.");
        }
    }
}

?>
