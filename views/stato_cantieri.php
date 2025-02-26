<?php
// Recupera tutti gli stati cantiere per mostrarli nella tabella
$statiCantiereData = $statoCantiere->all();

// Messaggi di errore o conferma
$errorMsg = isset($error) ? "<div class='alert alert-danger'>{$error}</div>" : '';
$successMsg = isset($success) ? "<div class='alert alert-success'>{$success}</div>" : '';

// Costruzione del contenuto dinamico
$content = <<<HTML
<div class="container mt-4">
    <h2 class="mb-4">Stato Cantieri</h2>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <p class="mb-0">Benvenuto, <strong>
HTML;
$content .= htmlspecialchars($_SESSION['utente_nome'], ENT_QUOTES, 'UTF-8');
$content .= <<<HTML
        </strong>!</p>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuovoStatoCantiere">
            Nuovo Stato
        </button>
    </div>
    <hr style="border-color:#cccccc; border-width: 1px;">
    $errorMsg
    $successMsg

    <!-- Tabella degli Stati Cantieri -->
    <div class="table-responsive">
        <table id="statiCantiereTable" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Stato</th>
                    <th>Colore</th>
                    <th>Azioni</th>
                </tr>
            </thead>
            <tbody>
HTML;

foreach ($statiCantiereData as $stato) {
    $content .= <<<HTML
                <tr>
                    <td>{$stato['stato_cantiere']}</td>
                    <td>
                        <div style="width: 20px; height: 20px; background-color:{$stato['colore']}; border: 1px solid #000;"></div>
                    </td>
                    <td>
                        <div class="d-flex justify-content-center">
                            <button type="button" class="btn btn-warning btn-sm me-2" 
                                data-bs-toggle="modal" 
                                data-bs-target="#modalModifica" 
                                data-id="{$stato['id']}" 
                                data-stato_cantiere="{$stato['stato_cantiere']}" 
                                data-colore="{$stato['colore']}">
                                Modifica
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" 
                                data-bs-toggle="modal" 
                                data-bs-target="#modalElimina" 
                                data-id="{$stato['id']}">
                                Elimina
                            </button>
                        </div>
                    </td>
                </tr>
HTML;
}

$content .= <<<HTML
            </tbody>
        </table>
    </div>

    <!-- Modal Nuovo Stato Cantiere -->
    <div class="modal fade" id="modalNuovoStatoCantiere" tabindex="-1" aria-labelledby="modalNuovoStatoCantiereLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalNuovoStatoCantiereLabel">Nuovo Stato</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="index.php?page=stato_cantieri">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="create">
                        <div class="mb-3">
                            <label for="nuovo-stato-cantiere" class="form-label">Stato Cantiere:</label>
                            <input type="text" id="nuovo-stato-cantiere" name="stato_cantiere" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="nuovo-colore" class="form-label">Colore:</label>
                            <input type="color" id="nuovo-colore" name="colore" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                        <button type="submit" class="btn btn-primary">Aggiungi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Modifica -->
    <div class="modal fade" id="modalModifica" tabindex="-1" aria-labelledby="modalModificaLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalModificaLabel">Modifica Stato</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="index.php?page=stato_cantieri">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" id="modifica-id" name="id">
                        <div class="mb-3">
                            <label for="modifica-stato-cantiere" class="form-label">Stato Cantiere:</label>
                            <input type="text" id="modifica-stato-cantiere" name="stato_cantiere" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="modifica-colore" class="form-label">Colore:</label>
                            <input type="color" id="modifica-colore" name="colore" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                        <button type="submit" class="btn btn-primary">Salva</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Elimina -->
    <div class="modal fade" id="modalElimina" tabindex="-1" aria-labelledby="modalEliminaLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEliminaLabel">Elimina Stato</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="index.php?page=stato_cantieri">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" id="elimina-id" name="id">
                        <p>Sei sicuro di voler eliminare questo stato cantiere?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                        <button type="submit" class="btn btn-danger">Elimina</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const table = $('#statiCantiereTable').DataTable();

        // Modal riempimento campi per Modifica
        $('#modalModifica').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget);
            const id = button.data('id');
            const statoCantiere = button.data('stato_cantiere');
            const colore = button.data('colore');

            const modal = $(this);
            modal.find('#modifica-id').val(id);
            modal.find('#modifica-stato-cantiere').val(statoCantiere);
            modal.find('#modifica-colore').val(colore);
        });

        // Modal riempimento campi per Elimina
        $('#modalElimina').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget);
            const id = button.data('id');

            const modal = $(this);
            modal.find('#elimina-id').val(id);
        });
    });
</script>

HTML;

// Include il layout
$tableId = 'statiCantiereTable'; // Specifica l'ID della tabella
include 'layout.php';
?>
