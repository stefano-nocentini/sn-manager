<?php

require_once 'base_model.php';

use Pushbullet\Pushbullet;


class Scadenze extends BaseModel
{
    private $table = 'scadenze';

    public function find($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function all($orderBy = "data_scadenza ASC", $limit = null, $offset = 0)
    {
        // Lista di colonne valide per il sort
        $validColumns = ['titolo', 'data_scadenza', 'id', 'avviso_email', 'avviso_push'];
    
        // Estrai la colonna e la direzione
        $orderParts = explode(' ', $orderBy);
        $column = $orderParts[0];
        $direction = isset($orderParts[1]) ? strtoupper($orderParts[1]) : 'ASC';
    
        // Verifica se la colonna è valida
        if (!in_array($column, $validColumns) || !in_array($direction, ['ASC', 'DESC'])) {
            $column = 'data_scadenza';
            $direction = 'ASC';
        }
    
        // Ricostruisci l'ORDER BY sicuro
        $safeOrderBy = "$column $direction";
    
        $query = "SELECT * FROM {$this->table} ORDER BY $safeOrderBy";
    
        if ($limit) {
            $query .= " LIMIT ?, ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param('ii', $offset, $limit);
            $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        } else {
            $result = $this->conn->query($query);
            return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        }
    }

    public function getUpcoming($limit = 5)
    {
        $query = "SELECT * FROM {$this->table} WHERE data_scadenza >= CURDATE() ORDER BY data_scadenza ASC LIMIT ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function create($data)
    {
        $data['avviso_email'] = isset($data['avviso_email']) ? $data['avviso_email'] : 0;
        $data['avviso_push'] = isset($data['avviso_push']) ? $data['avviso_push'] : 0;

        $stmt = $this->conn->prepare("INSERT INTO {$this->table} (titolo, descrizione, data_scadenza, avviso_email, avviso_push) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param(
            'sssii',
            $data['titolo'],
            $data['descrizione'],
            $data['data_scadenza'],
            $data['avviso_email'],
            $data['avviso_push']
        );
        return $stmt->execute();
    }

    public function update($id, $data)
    {
        $data['avviso_email'] = isset($data['avviso_email']) ? $data['avviso_email'] : 0;
        $data['avviso_push'] = isset($data['avviso_push']) ? $data['avviso_push'] : 0;

        $stmt = $this->conn->prepare("UPDATE {$this->table} SET titolo = ?, descrizione = ?, data_scadenza = ?, avviso_email = ?, avviso_push = ? WHERE id = ?");
        $stmt->bind_param(
            'sssiii',
            $data['titolo'],
            $data['descrizione'],
            $data['data_scadenza'],
            $data['avviso_email'],
            $data['avviso_push'],
            $id
        );
        return $stmt->execute();
    }

    public function delete($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id = ?");
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }

    public function filterByDate($startDate, $endDate = null)
    {
        $query = "SELECT * FROM {$this->table} WHERE data_scadenza >= ?";
        if ($endDate) {
            $query .= " AND data_scadenza <= ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param('ss', $startDate, $endDate);
        } else {
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param('s', $startDate);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function checkAndSendNotifications() 
    {
        $currentDateTime = date('Y-m-d H:i:00');
        $query = "SELECT * FROM {$this->table} WHERE data_scadenza = ? AND (avviso_email = 1 OR avviso_push = 1)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('s', $currentDateTime);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $subject = "Promemoria Scadenza: {$row['titolo']}";
                $body = "
                    <h1>Scadenza SN Manager</h1>
                    <p><strong>Titolo:</strong> {$row['titolo']}</p>
                    <p><strong>Descrizione:</strong> {$row['descrizione']}</p>
                    <p><strong>Data e ora:</strong> {$row['data_scadenza']}</p>
                ";

                // Invia email
                if ($row['avviso_email'] == 1) {
                    if ($this->sendEmail($subject, $body)) {
                        echo "Email inviata con successo per la scadenza: {$row['titolo']}\n";
                    } else {
                        echo "Errore nell'invio dell'email per la scadenza: {$row['titolo']}\n";
                    }
                }

                // Invia notifica push
                if ($row['avviso_push'] == 1) {
                    if ($this->sendPushNotification($subject, $row['descrizione'])) {
                        echo "Notifica push inviata con successo per la scadenza: {$row['titolo']}\n";
                    } else {
                        echo "Errore nell'invio della notifica push per la scadenza: {$row['titolo']}\n";
                    }
                }
            }
        } else {
            echo "Nessuna scadenza trovata per {$currentDateTime}.\n";
        }

        $stmt->close();
    }

    private function sendEmail($subject, $body) 
    {
        $to = "stefano.nocentini@gmail.com";
        $headers = "From: info@manager.sninfissi.com\r\n";
        $headers .= "Reply-To: info@manager.sninfissi.com\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

        if (mail($to, $subject, $body, $headers)) {
            return true;
        } else {
            error_log("Errore nell'invio dell'email a {$to}");
            return false;
        }
    }

    private function sendPushNotification($title, $body)
    {
        $accessToken = "o.KeAo5Bo1K0l4SZe9tM6HBhl8ZK4UML3g"; // Sostituisci con il tuo token Pushbullet
    
        $data = [
            "type" => "note",
            "title" => $title,
            "body" => $body
        ];
    
        $ch = curl_init("https://api.pushbullet.com/v2/pushes");
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Access-Token: $accessToken",
            "Content-Type: application/json"
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
        $response = curl_exec($ch);
    
        if (curl_errno($ch)) {
            error_log("Errore cURL: " . curl_error($ch));
            curl_close($ch);
            return false;
        }
    
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
    
        if ($httpCode === 200) {
            return true; // Notifica inviata con successo
        } else {
            error_log("Errore nell'invio della notifica Pushbullet: $response");
            return false;
        }
    }    
    
}

?>
