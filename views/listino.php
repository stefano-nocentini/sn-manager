<?php
// Recupera tutti i prodotti del listino per mostrarli nella tabella
$listinoData = $listino->all();

// Messaggi di errore o conferma
$errorMsg = isset($error) ? "<div class='alert alert-danger'>{$error}</div>" : '';
$successMsg = isset($success) ? "<div class='alert alert-success'>{$success}</div>" : '';

// Recupera le tipologie dalla tabella
require_once 'backend/tipologia.php';
$tipologia = new Tipologia($db);
$tipologiaData = $tipologia->all();

// Costruzione del contenuto dinamico
$content = <<<HTML
<div class="container mt-4">
    <h2 class="mb-4">Gestione Listino</h2>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <p class="mb-0">Benvenuto, <strong>
HTML;
$content .= htmlspecialchars($_SESSION['utente_nome'], ENT_QUOTES, 'UTF-8');
$content .= <<<HTML
        </strong>!</p>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuovoItem">
            Nuova Voce
        </button>
    </div>
    <hr style="border-color:#cccccc; border-width: 1px;">
    $errorMsg
    $successMsg


    <!-- Tabella del Listino -->
    <div class="table-responsive">
        <table id="listinoTable" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Voce</th>
                    <th>Prezzo</th>
                    <th>Azioni</th>
                </tr>
            </thead>
            <tbody>
HTML;


$lastTipologia = null;

foreach ($listinoData as $prodotto) {
    $idTipologia = isset($prodotto['id_tipologia']) ? $prodotto['id_tipologia'] : '0';
    // Se la tipologia è diversa dalla precedente, mostra il titolo
    if ($lastTipologia !== $prodotto['id_tipologia']) {
        $content .= <<<HTML
            <tr class="group-row">
                <td class="table-secondary">
                    <strong>{$prodotto['tipologia_nome']}</strong>
                </td>
                <td class="table-secondary"></td>
                <td class="table-secondary"></td>
            </tr>
    HTML;
        $lastTipologia = $prodotto['id_tipologia'];
    }
    

    // Aggiungi il record del prodotto
    $content .= <<<HTML
            <tr>
                <td>{$prodotto['voce']}</td>
                <td>{$prodotto['prezzo']}</td>
                <td>
                    <div class="d-flex justify-content-center">
                        <button type="button" 
                                class="btn btn-warning btn-sm me-2" 
                                data-bs-toggle="modal" 
                                data-bs-target="#modalModifica" 
                                data-id="{$prodotto['id']}" 
                                data-voce="{$prodotto['voce']}" 
                                data-prezzo="{$prodotto['prezzo']}" 
                                data-id-tipologia="{$prodotto['id_tipologia']}">
                            Modifica
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalElimina" data-id="{$prodotto['id']}">
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

    <!-- Modal Nuova voce -->
    <div class="modal fade" id="modalNuovoItem" tabindex="-1" aria-labelledby="modalNuovoItemLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalNuovoItemLabel">Nuova Voce</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="index.php?page=listino">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="create">
                        <div class="mb-3">
                            <label for="nuova-voce" class="form-label">Voce:</label>
                            <input type="text" id="nuova-voce" name="voce" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="nuovo-prezzo" class="form-label">Prezzo:</label>
                            <input type="number" id="nuovo-prezzo" name="prezzo" class="form-control" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="nuova-tipologia" class="form-label">Tipologia:</label>
                            <select id="nuova-tipologia" name="id_tipologia" class="form-control" required>
HTML;

foreach ($tipologiaData as $tipologia) {
    $content .= "<option value=\"{$tipologia['id']}\">{$tipologia['nome']}</option>";
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
HTML;

$content .= <<<HTML
    <!-- Modal Modifica -->
    <div class="modal fade" id="modalModifica" tabindex="-1" aria-labelledby="modalModificaLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalModificaLabel">Modifica Voce</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="index.php?page=listino">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" id="modifica-id" name="id">

                        <div class="mb-3">
                            <label for="modifica-voce" class="form-label">Voce:</label>
                            <input type="text" id="modifica-voce" name="voce" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="modifica-prezzo" class="form-label">Prezzo:</label>
                            <input type="number" id="modifica-prezzo" name="prezzo" class="form-control" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="modifica-tipologia" class="form-label">Tipologia:</label>
                            <select id="modifica-tipologia" name="id_tipologia" class="form-control" required>
HTML;

    foreach ($tipologiaData as $tipologia) {
    $content .= "<option value=\"{$tipologia['id']}\">{$tipologia['nome']}</option>";
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
                    <h5 class="modal-title" id="modalEliminaLabel">Elimina Voce</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="index.php?page=listino">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" id="elimina-id" name="id">
                        <p>Sei sicuro di voler eliminare questa voce</p>
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

        const table = $('#listinoTable').DataTable({
            pageLength: 100,
            order: [], // Disabilita il riordino automatico
            lengthMenu: [[5, 10, 25, 50, 100, 200, 500], [5, 10, 25, 50, 100, 200, 500]],
            language: {
                lengthMenu: "Mostra _MENU_ voci",
                info: "Mostra da _START_ a _END_ di _TOTAL_ voci",
                infoEmpty: "Nessuna voce disponibile",
                infoFiltered: "(filtrato da _MAX_ voci totali)",
                search: "Cerca:",
                paginate: {
                    first: "Prima",
                    last: "Ultima",
                    next: "Successiva",
                    previous: "Precedente"
                },
                zeroRecords: "Nessun risultato trovato",
                emptyTable: "Nessun dato disponibile nella tabella"
            }
        });

        // Modal riempimento campi per Modifica
        $('#modalModifica').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget); // Il pulsante che ha attivato la modale
            const id = button.data('id'); // ID della voce
            const voce = button.data('voce'); // Nome della voce
            const prezzo = button.data('prezzo'); // Prezzo della voce
            const idTipologia = button.data('id-tipologia'); // ID della tipologia

            const modal = $(this);
            modal.find('#modifica-id').val(id); // Imposta l'ID
            modal.find('#modifica-voce').val(voce); // Imposta la voce
            modal.find('#modifica-prezzo').val(prezzo); // Imposta il prezzo
            
            // Seleziona la tipologia corretta
            const tipologiaSelect = modal.find('#modifica-tipologia');
            tipologiaSelect.val(idTipologia);

            // Se il valore non viene trovato, mostra un messaggio nella console
            if (!tipologiaSelect.find(`option[value="${idTipologia}"]`).length) {
                console.error(`Tipologia con ID ${idTipologia} non trovata nel select.`);
            }
        });

        // Modal riempimento campi per Elimina
        $('#modalElimina').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget); // Il pulsante che ha attivato la modale
            const id = button.data('id'); // ID della voce

            const modal = $(this);
            modal.find('#elimina-id').val(id); // Imposta l'ID
        });

    });
</script>


HTML;

// Include il layout
include 'layout.php';
?>
