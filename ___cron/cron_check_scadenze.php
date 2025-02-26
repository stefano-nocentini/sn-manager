<?php
require_once '/home/aksninfi/manager.sninfissi.com/backend/scadenze.php';

$db_host = 'localhost';
$db_user = 'aksninfi_user';
$db_pass = '$lFolKA9p#el';
$db_name = 'aksninfi_db';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {

    die("Errore di connessione al database: " . $conn->connect_error);

}

$scadenze = new Scadenze($conn);
$scadenze->checkAndSendNotifications();

$conn->close();
?>