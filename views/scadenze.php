<?php
// Recupera tutte le scadenze per mostrarle nella tabella
$scadenzeData = $scadenze->all(isset($_GET['order']) ? str_replace('_', ' ', $_GET['order']) : "titolo ASC, data_scadenza ASC");

// Messaggi di errore o conferma
$errorMsg = isset($error) ? "<div class='alert alert-danger'>{$error}</div>" : '';
$successMsg = isset($success) ? "<div class='alert alert-success'>{$success}</div>" : '';

// Definizione delle opzioni per l'ordinamento
$ordinamenti = [
    'titolo ASC' => 'Titolo',
    'data_scadenza ASC' => 'Data Scadenza'
];

// Costruzione del contenuto dinamico
$content = <<<HTML
<div class="container mt-4">
    <h2 class="mb-4">Gestione Scadenze</h2>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <p class="mb-0">Benvenuto, <strong>
HTML;
$content .= htmlspecialchars($_SESSION['utente_nome'], ENT_QUOTES, 'UTF-8');
$content .= <<<HTML
        </strong>!</p>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuovaScadenza">
            Nuova Scadenza
        </button>
    </div>
    <hr style="border-color:#cccccc; border-width: 1px;">
    $errorMsg
    $successMsg

    <!-- Ordinamento -->
    <div class="mb-4">
        <label for="ordinamento" class="form-label">Ordina per:</label>
        <select id="ordinamento" name="ordinamento" class="form-control" onchange="location = this.value;">
HTML;

foreach ($ordinamenti as $key => $label) {
    $selected = (isset($_GET['order']) && $_GET['order'] === $key) ? 'selected' : '';
    $content .= "<option value=\"index.php?page=scadenze&order=$key\" $selected>$label</option>";
}

$content .= <<<HTML
        </select>
    </div>

    <!-- Tabella delle Scadenze -->
    <div class="table-responsive">
        <table id="dataTable" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Titolo</th>
                    <th>Descrizione</th>
                    <th>Data - Ora</th>
                    <th>Avviso Email</th>
                    <th>Avviso Push</th>
                    <th>Azioni</th>
                </tr>
            </thead>
            <tbody>
HTML;

foreach ($scadenzeData as $scadenza) {
    $avviso_email = $scadenza['avviso_email'] ? 'Sì' : 'No';
    $avviso_push = $scadenza['avviso_push'] ? 'Sì' : 'No';
    $content .= <<<HTML
                <tr>
                    <td>{$scadenza['titolo']}</td>
                    <td>{$scadenza['descrizione']}</td>
                    <td>{$scadenza['data_scadenza']}</td>
                    <td>{$avviso_email}</td>
                    <td>{$avviso_push}</td>
                    <td>
                        <div class="d-flex justify-content-center">
                            <button type="button" class="btn btn-warning btn-sm me-2" 
                                data-bs-toggle="modal" 
                                data-bs-target="#modalModifica" 
                                data-id="{$scadenza['id']}" 
                                data-titolo="{$scadenza['titolo']}" 
                                data-descrizione="{$scadenza['descrizione']}" 
                                data-data_scadenza="{$scadenza['data_scadenza']}" 
                                data-avviso_email="{$scadenza['avviso_email']}" 
                                data-avviso_push="{$scadenza['avviso_push']}">
                                Modifica
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" 
                                data-bs-toggle="modal" 
                                data-bs-target="#modalElimina" 
                                data-id="{$scadenza['id']}">
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

    <!-- Modal Nuova Scadenza -->
    <div class="modal fade" id="modalNuovaScadenza" tabindex="-1" aria-labelledby="modalNuovaScadenzaLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalNuovaScadenzaLabel">Nuova Scadenza</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="index.php?page=scadenze">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="create">
                        <div class="mb-3">
                            <label for="nuova-titolo" class="form-label">Titolo:</label>
                            <input type="text" id="nuova-titolo" name="titolo" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="nuova-descrizione" class="form-label">Descrizione:</label>
                            <textarea id="nuova-descrizione" name="descrizione" class="form-control"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="nuova-data_scadenza" class="form-label">Data e Ora Scadenza:</label>
                            <input type="datetime-local" id="nuova-data_scadenza" name="data_scadenza" class="form-control" required>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="nuova-avviso-email" name="avviso_email" value="1">
                            <label class="form-check-label" for="nuova-avviso-email">
                                Invia avviso Email
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="nuova-avviso-push" name="avviso_push" value="1">
                            <label class="form-check-label" for="nuova-avviso-push">
                                Invia avviso Push
                            </label>
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
                    <h5 class="modal-title" id="modalModificaLabel">Modifica Scadenza</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="index.php?page=scadenze">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" id="modifica-id" name="id">
                        <div class="mb-3">
                            <label for="modifica-titolo" class="form-label">Titolo:</label>
                            <input type="text" id="modifica-titolo" name="titolo" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="modifica-descrizione" class="form-label">Descrizione:</label>
                            <textarea id="modifica-descrizione" name="descrizione" class="form-control"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="modifica-data_scadenza" class="form-label">Data e Ora Scadenza:</label>
                            <input type="datetime-local" id="modifica-data_scadenza" name="data_scadenza" class="form-control" required>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="modifica-avviso-email" name="avviso_email" value="1">
                            <label class="form-check-label" for="modifica-avviso-email">
                                Invia avviso Email
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="modifica-avviso-push" name="avviso_push" value="1">
                            <label class="form-check-label" for="modifica-avviso-push">
                                Invia avviso Push
                            </label>
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
                    <h5 class="modal-title" id="modalEliminaLabel">Elimina Scadenza</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="index.php?page=scadenze">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" id="elimina-id" name="id">
                        <p>Sei sicuro di voler eliminare questa scadenza?</p>
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
        const table = $('#dataTable').DataTable();

        // Modal riempimento campi per Modifica
        $('#modalModifica').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget);
            const id = button.data('id');
            const titolo = button.data('titolo');
            const descrizione = button.data('descrizione');
            const data_scadenza = button.data('data_scadenza');
            const avviso_email = button.data('avviso_email');
            const avviso_push = button.data('avviso_push');

            console.log("--> "+avviso_email+" - "+avviso_push+" - "+data_scadenza);

            const modal = $(this);
            modal.find('#modifica-id').val(id);
            modal.find('#modifica-titolo').val(titolo);
            modal.find('#modifica-descrizione').val(descrizione);
            modal.find('#modifica-data_scadenza').val(data_scadenza);
            modal.find('#modifica-avviso-email').prop('checked', avviso_email == 1);
            modal.find('#modifica-avviso-push').prop('checked', avviso_push == 1);
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
include 'layout.php';
?>
