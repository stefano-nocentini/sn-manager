<?php
session_start();
session_unset();
session_destroy();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout</title>
    <meta http-equiv="refresh" content="3;url=index.php?page=login">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container text-center mt-5">
        <h1 class="text-danger">Sei stato disconnesso</h1>
        <p class="lead">Verrai reindirizzato al login tra <strong>3 secondi</strong>.</p>
        <p>Se non vieni reindirizzato, <a href="index.php?page=login" class="btn btn-primary">clicca qui</a>.</p>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
