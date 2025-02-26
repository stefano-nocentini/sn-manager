<?php
$errorMsg = isset($error) ? "<div class='alert alert-danger'>{$error}</div>" : '';
$successMsg = isset($success) ? "<div class='alert alert-success'>{$success}</div>" : '';

$totaleCantieri = $totaliData['totale_cantieri'];
$totalePreventivi = number_format($totaliData['totale_importo'], 2, ",", ".");
$totaleSpese = number_format($totaliData['totale_spese'], 2, ",", ".");
$totaleGuadagno = number_format($totaliData['totale_guadagno'], 2, ",", ".");


// Costruzione del contenuto dinamico
$content = <<<HTML
<div class="container mt-4">
    <h2 class="mb-4">Dashboard</h2>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <p class="mb-0">Benvenuto, <strong>{$_SESSION['utente_nome']}</strong>!</p>
    </div>
    <hr style="border-color:#cccccc; border-width: 1px;">
    $errorMsg
    $successMsg


    
    <!-- Panoramica -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0">Panoramica</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="card text-center border-info">
                        <div class="card-body">
                            <h5 class="card-title">Totale Cantieri</h5>
                            <p class="card-text display-6">{$totaleCantieri}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center border-warning">
                        <div class="card-body">
                            <h5 class="card-title">Totale Preventivi</h5>
                            <p class="card-text display-6">{$totalePreventivi} €</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center border-danger">
                        <div class="card-body">
                            <h5 class="card-title">Totale Spese</h5>
                            <p class="card-text display-6">{$totaleSpese} €</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center border-success">
                        <div class="card-body">
                            <h5 class="card-title">Totale Guadagno</h5>
                            <p class="card-text display-6">{$totaleGuadagno} €</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Prossime Scadenze -->
    <div class="card">
        <div class="card-header bg-warning text-dark">
            <h3 class="mb-0">Prossime Scadenze</h3>
        </div>
        <div class="card-body">
            <ul class="list-group">
HTML;

// Itera le prossime scadenze per creare l'elenco
if (!empty($prossimeScadenze)) {
    foreach ($prossimeScadenze as $scadenza) {
        $content .= <<<HTML
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    {$scadenza['descrizione']}
                    <span class="badge bg-danger text-white">{$scadenza['data_scadenza']}</span>
                </li>
HTML;
    }
} else {
    $content .= '<li class="list-group-item text-center">Nessuna scadenza prossima.</li>';
}

$content .= <<<HTML
            </ul>
        </div>
    </div>
</div>






HTML;

// Includi il layout principale
$tableId = ''; // Nessuna tabella in questa pagina
include 'layout.php';
?>
