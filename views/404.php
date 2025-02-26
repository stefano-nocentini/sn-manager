<?php
// Messaggi personalizzati per la pagina 404
$errorMsg = "<div class='container text-center mt-5'>
    <h1 class='display-4 text-danger'>404 - Pagina non trovata</h1>
    <p class='lead'>La pagina che stai cercando non esiste o è stata rimossa.</p>
    <a href='index.php?page=login' class='btn btn-primary mt-3'>Torna al login</a>
</div>";

// Assegna il contenuto come variabile per il layout
$content = $errorMsg;

// Specifica il titolo per la pagina 404
$pageTitles['404'] = 'Pagina non trovata';

// Includi il layout
include 'layout.php';
?>

