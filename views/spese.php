<?php
if (!isset($motivoSpese)) {
    require_once 'backend/motivo_spese.php';
    $motivoSpese = new MotivoSpese($db);
}
$motivoSpeseData = $motivoSpese->all();


// Recupera tutte le spese per mostrarle nella tabella
$speseData = $spese->all(isset($_GET['order']) ? str_replace('_', ' ', $_GET['order']) : "descrizione ASC, importo ASC, id ASC");

// Messaggi di errore o conferma
$errorMsg = isset($error) ? "<div class='alert alert-danger'>{$error}</div>" : '';
$successMsg = isset($success) ? "<div class='alert alert-success'>{$success}</div>" : '';

// Definizione delle opzioni per l'ordinamento
$ordinamenti = [
    'descrizione ASC' => 'Descrizione',
    'importo ASC' => 'Importo',
    'id ASC' => 'ID'
];

// Costruzione del contenuto dinamico
$content = <<<HTML
<div class="container mt-4">
    <h2 class="mb-4">Gestione Spese</h2>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <p class="mb-0">Benvenuto, <strong>
HTML;
$content .= htmlspecialchars($_SESSION['utente_nome'], ENT_QUOTES, 'UTF-8');
$content .= <<<HTML
        </strong>!</p>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuovaSpesa">
            Nuova Spesa
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
    $content .= "<option value=\"index.php?page=spese&order=$key\" $selected>$label</option>";
}

$content .= <<<HTML
        </select>
    </div>

    <!-- Tabella delle Spese -->
    <div class="table-responsive">
        <table id="speseTable" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Descrizione</th>
                    <th>Importo (€)</th>
                    <th>Data Spesa</th>
                    <th>Motivo spesa</th>
                    <th>Azioni</th>
                </tr>
            </thead>
            <tbody>
HTML;

foreach ($speseData as $spesa) {
    $content .= <<<HTML
                <tr>
                    <td>{$spesa['descrizione']}</td>
                    <td>{$spesa['importo']}</td>
                    <td>{$spesa['data_spesa']}</td>
                    <td>{$spesa['motivo_spesa']}</td>
                    <td>
                        <div class="d-flex justify-content-center">
                            <button type="button" class="btn btn-warning btn-sm me-2" 
                                data-bs-toggle="modal" 
                                data-bs-target="#modalModifica" 
                                data-id="{$spesa['id']}" 
                                data-descrizione="{$spesa['descrizione']}" 
                                data-importo="{$spesa['importo']}" 
                                data-data_spesa="{$spesa['data_spesa']}" 
                                data-id_motivo_spesa="{$spesa['id_motivo_spesa']}">
                                Modifica
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" 
                                data-bs-toggle="modal" 
                                data-bs-target="#modalElimina" 
                                data-id="{$spesa['id']}">
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

    <!-- Modal Nuova Spesa -->
    <div class="modal fade" id="modalNuovaSpesa" tabindex="-1" aria-labelledby="modalNuovaSpesaLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalNuovaSpesaLabel">Nuova Spesa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="index.php?page=spese">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="create">
                        <div class="mb-3">
                            <label for="nuova-descrizione" class="form-label">Descrizione:</label>
                            <input type="text" id="nuova-descrizione" name="descrizione" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="nuovo-importo" class="form-label">Importo:</label>
                            <input type="number" id="nuovo-importo" name="importo" class="form-control" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="nuova-data_spesa" class="form-label">Data Spesa:</label>
                            <input type="date" id="nuova-data_spesa" name="data_spesa" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="id-motivo" class="form-label">Motivo Spesa:</label>
                            <select id="id-motivo" name="id_motivo_spesa" class="form-select" required>
                                <option value="" disabled selected>Seleziona un motivo</option>
HTML;      
                                foreach ($motivoSpeseData as $motivo) {
                                    $content .= '<option value="' . htmlspecialchars($motivo['id'], ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($motivo['motivo_spesa'], ENT_QUOTES, 'UTF-8') . '</option>';
                                }
$content .= <<<HTML
                            </select>
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
                    <h5 class="modal-title" id="modalModificaLabel">Modifica Spesa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="index.php?page=spese">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" id="modifica-id" name="id">
                        <div class="mb-3">
                            <label for="modifica-descrizione" class="form-label">Descrizione:</label>
                            <input type="text" id="modifica-descrizione" name="descrizione" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="modifica-importo" class="form-label">Importo:</label>
                            <input type="number" id="modifica-importo" name="importo" class="form-control" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="modifica-data_spesa" class="form-label">Data Spesa:</label>
                            <input type="date" id="modifica-data_spesa" name="data_spesa" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="id-motivo" class="form-label">Motivo Spesa:</label>
                            <select id="id-motivo" name="id_motivo_spesa" class="form-select" required>
                                <option value="" disabled selected>Seleziona un motivo</option>
HTML;      
                                foreach ($motivoSpeseData as $motivo) {
                                    $content .= '<option value="' . htmlspecialchars($motivo['id'], ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($motivo['motivo_spesa'], ENT_QUOTES, 'UTF-8') . '</option>';
                                }
$content .= <<<HTML
                            </select>
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
                    <h5 class="modal-title" id="modalEliminaLabel">Elimina Spesa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="index.php?page=spese">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" id="elimina-id" name="id">
                        <p>Sei sicuro di voler eliminare questa spesa?</p>
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
        const table = $('#speseTable').DataTable();

        // Modal riempimento campi per Modifica
        $('#modalModifica').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget);
            const id = button.data('id');
            const descrizione = button.data('descrizione');
            const importo = button.data('importo');
            const data_spesa = button.data('data_spesa');
            const id_motivo_spesa = button.data('id_motivo_spesa');

            const modal = $(this);
            modal.find('#modifica-id').val(id);
            modal.find('#modifica-descrizione').val(descrizione);
            modal.find('#modifica-importo').val(importo);
            modal.find('#modifica-data_spesa').val(data_spesa);
            modal.find('#modifica-id_motivo_spesa').val(id_motivo_spesa);

            // Seleziona il valore corretto nella combobox
            modal.find('#id-motivo').val(id_motivo_spesa);
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
