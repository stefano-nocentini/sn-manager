<?php
// Recupera tutti gli utenti per mostrarli nella tabella
$utentiData = $utenti->all();

// Messaggi di errore o conferma
$errorMsg = isset($error) ? "<div class='alert alert-danger'>{$error}</div>" : '';
$successMsg = isset($success) ? "<div class='alert alert-success'>{$success}</div>" : '';

// Costruzione del contenuto dinamico
$content = <<<HTML
<div class="container mt-4">
    <h2 class="mb-4">Utenti</h2>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <p class="mb-0">Benvenuto, <strong>
HTML;
$content .= htmlspecialchars($_SESSION['utente_nome'] ?? 'Utente', ENT_QUOTES, 'UTF-8');
$content .= <<<HTML
        </strong>!</p>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuovoUtente">
            Nuovo Utente
        </button>
    </div>
    <hr style="border-color:#cccccc; border-width: 1px;">
    $errorMsg
    $successMsg

    <!-- Tabella degli Utenti -->
    <div class="table-responsive">
        <table id="utentiTable" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Cognome</th>
                    <th>Telefono</th>
                    <th>Email</th>
                    <th>Azioni</th>
                </tr>
            </thead>
            <tbody>
HTML;

foreach ($utentiData as $utente) {
    $content .= <<<HTML
                <tr>
                    <td>{$utente['nome']}</td>
                    <td>{$utente['cognome']}</td>
                    <td>{$utente['telefono']}</td>
                    <td>{$utente['email']}</td>
                    <td>
                        <div class="d-flex justify-content-center">
                            <button type="button" class="btn btn-warning btn-sm me-2" data-bs-toggle="modal" data-bs-target="#modalModifica" 
                                data-id="{$utente['id']}" 
                                data-nome="{$utente['nome']}" 
                                data-cognome="{$utente['cognome']}" 
                                data-telefono="{$utente['telefono']}" 
                                data-email="{$utente['email']}">
                                Modifica
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalElimina" data-id="{$utente['id']}">
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

    <!-- Modal Nuovo Utente -->
    <div class="modal fade" id="modalNuovoUtente" tabindex="-1" aria-labelledby="modalNuovoUtenteLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalNuovoUtenteLabel">Nuovo Utente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="index.php?page=utenti">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="create">
                        <div class="mb-3">
                            <label for="nuovo-nome" class="form-label">Nome:</label>
                            <input type="text" id="nuovo-nome" name="nome" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="nuovo-cognome" class="form-label">Cognome:</label>
                            <input type="text" id="nuovo-cognome" name="cognome" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="nuovo-telefono" class="form-label">Telefono:</label>
                            <input type="text" id="nuovo-telefono" name="telefono" class="form-control">
                        </div>
                        <br>
                        <h6>Dati di Login</h6>
                        <hr style="border-color:#cccccc; border-width: 1px;">
                        <div class="mb-3">
                            <label for="nuovo-email" class="form-label">Email:</label>
                            <input type="email" id="nuovo-email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="nuovo-password" class="form-label">Password:</label>
                            <input type="password" id="nuovo-password" name="password" class="form-control" required>
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
                    <h5 class="modal-title" id="modalModificaLabel">Modifica Utente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="index.php?page=utenti">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" id="modifica-id" name="id">
                        <div class="mb-3">
                            <label for="modifica-nome" class="form-label">Nome:</label>
                            <input type="text" id="modifica-nome" name="nome" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="modifica-cognome" class="form-label">Cognome:</label>
                            <input type="text" id="modifica-cognome" name="cognome" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="modifica-telefono" class="form-label">Telefono:</label>
                            <input type="text" id="modifica-telefono" name="telefono" class="form-control">
                        </div>
                        <br>
                        <h6>Dati di Login</h6>
                        <hr style="border-color:#cccccc; border-width: 1px;">
                        <div class="mb-3">
                            <label for="modifica-email" class="form-label">Email:</label>
                            <input type="email" id="modifica-email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="modifica-password" class="form-label">Password:</label>
                            <input type="password" id="modifica-password" name="password" class="form-control">
                            <small class="form-text text-muted">Lascia vuoto per mantenere la password attuale.</small>
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
                    <h5 class="modal-title" id="modalEliminaLabel">Elimina Utente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="index.php?page=utenti">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" id="elimina-id" name="id">
                        <p>Sei sicuro di voler eliminare questo utente?</p>
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
        const table = $('#utentiTable').DataTable();

        // Modal riempimento campi per Modifica
        $('#modalModifica').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget);
            const id = button.data('id');
            const nome = button.data('nome');
            const cognome = button.data('cognome');
            const telefono = button.data('telefono');
            const email = button.data('email');

            const modal = $(this);
            modal.find('#modifica-id').val(id);
            modal.find('#modifica-nome').val(nome);
            modal.find('#modifica-cognome').val(cognome);
            modal.find('#modifica-telefono').val(telefono);
            modal.find('#modifica-email').val(email);
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
$tableId = 'utentiTable'; // Specifica l'ID della tabella
include 'layout.php';
?>
