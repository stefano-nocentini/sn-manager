<?php
// Recupera tutti i materiali per mostrarli nella tabella
$materialiData = $materiali->all();

// Messaggi di errore o conferma
$errorMsg = isset($error) ? "<div class='alert alert-danger'>{$error}</div>" : '';
$successMsg = isset($success) ? "<div class='alert alert-success'>{$success}</div>" : '';

// Costruzione del contenuto dinamico
$content = <<<HTML
<div class="container mt-4">
    <h2 class="mb-4">Materiali</h2>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <p class="mb-0">Benvenuto, <strong>
HTML;
$content .= htmlspecialchars($_SESSION['utente_nome'], ENT_QUOTES, 'UTF-8');
$content .= <<<HTML
        </strong>!</p>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuovoMateriale">
            Nuovo Materiale
        </button>
    </div>
    <hr style="border-color:#cccccc; border-width: 1px;">
    $errorMsg
    $successMsg

    <!-- Tabella dei Materiali -->
    <div class="table-responsive">
        <table id="materialiTable" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Descrizione</th>
                    <th>Prezzo</th>
                    <th>Azioni</th>
                </tr>
            </thead>
            <tbody>
HTML;

foreach ($materialiData as $materiale) {
    // Formatta il prezzo con number_format
    $prezzoFormattato = number_format($materiale['prezzo'], 2, ',', '.');

    $content .= <<<HTML
                <tr>
                    <td>{$materiale['nome']}</td>
                    <td>{$materiale['descrizione']}</td>
                    <td>€{$prezzoFormattato}</td>
                    <td>
                        <div class="d-flex justify-content-center">
                            <button type="button" class="btn btn-warning btn-sm me-2" data-bs-toggle="modal" data-bs-target="#modalModifica" data-id="{$materiale['id']}" data-nome="{$materiale['nome']}" data-descrizione="{$materiale['descrizione']}" data-prezzo="{$materiale['prezzo']}">
                                Modifica
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalElimina" data-id="{$materiale['id']}">
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

    <!-- Modal Nuovo Materiale -->
    <div class="modal fade" id="modalNuovoMateriale" tabindex="-1" aria-labelledby="modalNuovoMaterialeLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalNuovoMaterialeLabel">Nuovo Materiale</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="index.php?page=materiali">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="create">
                        <div class="mb-3">
                            <label for="nuovo-nome" class="form-label">Nome:</label>
                            <input type="text" id="nuovo-nome" name="nome" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="nuovo-descrizione" class="form-label">Descrizione:</label>
                            <textarea id="nuovo-descrizione" name="descrizione" class="form-control"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="nuovo-prezzo" class="form-label">Prezzo:</label>
                            <input type="number" id="nuovo-prezzo" name="prezzo" class="form-control" step="0.01" required>
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
                    <h5 class="modal-title" id="modalModificaLabel">Modifica Materiale</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="index.php?page=materiali">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" id="modifica-id" name="id">
                        <div class="mb-3">
                            <label for="modifica-nome" class="form-label">Nome:</label>
                            <input type="text" id="modifica-nome" name="nome" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="modifica-descrizione" class="form-label">Descrizione:</label>
                            <textarea id="modifica-descrizione" name="descrizione" class="form-control"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="modifica-prezzo" class="form-label">Prezzo:</label>
                            <input type="number" id="modifica-prezzo" name="prezzo" class="form-control" step="0.01" required>
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
                    <h5 class="modal-title" id="modalEliminaLabel">Elimina Materiale</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="index.php?page=materiali">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" id="elimina-id" name="id">
                        <p>Sei sicuro di voler eliminare questo materiale?</p>
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
        const table = $('#materialiTable').DataTable();

        // Modal riempimento campi per Modifica
        $('#modalModifica').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget);
            const id = button.data('id');
            const nome = button.data('nome');
            const descrizione = button.data('descrizione');
            const prezzo = button.data('prezzo');

            const modal = $(this);
            modal.find('#modifica-id').val(id);
            modal.find('#modifica-nome').val(nome);
            modal.find('#modifica-descrizione').val(descrizione);
            modal.find('#modifica-prezzo').val(prezzo);
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
$tableId = 'materialiTable'; // Specifica l'ID della tabella
include 'layout.php';
?>
