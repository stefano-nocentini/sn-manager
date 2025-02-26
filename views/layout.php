<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SN-Manager - <?php echo isset($pageTitles[$page]) ? $pageTitles[$page] : 'Home'; ?></title>
    <link rel="icon" type="image/x-icon" href="https://manager.sninfissi.com/assets/icons/favicon/favicon.ico">

    <!-- Bootstrap CSS -->
    <link href="https://manager.sninfissi.com/assets/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://manager.sninfissi.com/assets/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://manager.sninfissi.com/assets/css/responsive.bootstrap5.min.css">

    <!-- jQuery -->
    <script src="https://manager.sninfissi.com/assets/js/jquery-3.7.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://manager.sninfissi.com/assets/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://manager.sninfissi.com/assets/js/jquery.dataTables.min.js"></script>
    <script src="https://manager.sninfissi.com/assets/js/dataTables.responsive.min.js"></script>
    <script src="https://manager.sninfissi.com/assets/js/dataTables.bootstrap5.min.js"></script>

    <!-- File Personalizzati -->
    <link rel="stylesheet" href="https://manager.sninfissi.com/assets/css/style.css">
    <script src="https://manager.sninfissi.com/assets/js/script.js"></script>

    <style>
        .dropdown-toggle::after {
            display: none;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="bg-dark text-white py-3">
        <div class="container">
            <h1>SN-Manager</h1>
        </div>
    </header>

    <!-- Menu di Navigazione -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <!-- Pulsante per dispositivi mobili -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <?php if (isset($_SESSION['utente_id'])): ?>
                    <ul class="navbar-nav">
                        <li class="nav-item"><a class="nav-link <?php echo $page === 'dashboard' ? 'active' : ''; ?>" href="index.php?page=dashboard">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link <?php echo $page === 'cantieri' ? 'active' : ''; ?>" href="index.php?page=cantieri">Cantieri</a></li>
                        <li class="nav-item"><a class="nav-link <?php echo $page === 'clienti' ? 'active' : ''; ?>" href="index.php?page=clienti">Clienti</a></li>
                        <li class="nav-item"><a class="nav-link <?php echo $page === 'scadenze' ? 'active' : ''; ?>" href="index.php?page=scadenze">Scadenze</a></li>
                        <li class="nav-item"><a class="nav-link <?php echo $page === 'spese' ? 'active' : ''; ?>" href="index.php?page=spese">Spese</a></li>
                        <li class="nav-item"><a class="nav-link <?php echo $page === 'listino' ? 'active' : ''; ?>" href="index.php?page=listino">Listino</a></li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle <?php echo $page === 'listino' ? 'active' : ''; ?>" href="#" id="listinoMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-list" style="font-size: 1.5rem;"></i>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="listinoMenu">
                                <li><a class="dropdown-item" href="index.php?page=aziende">Aziende</a></li>
                                <li><a class="dropdown-item" href="index.php?page=fornitori">Fornitori</a></li>
                                <li><a class="dropdown-item" href="index.php?page=materiali">Materiali</a></li>
                                <li><a class="dropdown-item" href="index.php?page=banche">Banche</a></li>
                                <li><a class="dropdown-item" href="index.php?page=stato_cantieri">Stato Cantieri</a></li>
                                <li><a class="dropdown-item" href="index.php?page=motivo_spese">Motivo Spese</a></li>
                                <li><a class="dropdown-item" href="index.php?page=posatori">Posatori</a></li>
                                <li><a class="dropdown-item" href="index.php?page=utenti">Utenti</a></li>
                                <li><a class="dropdown-item" href="index.php?page=logout">Logout</a></li>
                            </ul>
                        </li>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Contenuto Principale -->
    <main class="container my-4">
        <?php if (isset($content)) echo $content; ?>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3">
        <p>&copy; <?php echo date('Y'); ?> Gestione Ditta. Tutti i diritti riservati.</p>
    </footer>

    <!-- JavaScript -->
    <script>
    $(document).ready(function() {
        var tableId = '<?php echo isset($tableId) ? $tableId : ''; ?>';
        if (tableId && !$.fn.DataTable.isDataTable('#' + tableId)) {
            $('#' + tableId).DataTable({
                "responsive": true,
                "autoWidth": false
            });
        }
    });
    </script>
</body>
</html>

