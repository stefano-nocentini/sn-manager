<?php
// Recupera tutte le banche per mostrarle nella tabella
$bancheData = $banche->all();

// Messaggi di errore o conferma
$errorMsg = isset($error) ? "<div class='alert alert-danger'>{$error}</div>" : '';
$successMsg = isset($success) ? "<div class='alert alert-success'>{$success}</div>" : '';

// Costruzione del contenuto dinamico
$content = <<<HTML
<div class="container mt-4">
    <h2 class="mb-4">Banche</h2>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <p class="mb-0">Benvenuto, <strong>
HTML;
$content .= htmlspecialchars($_SESSION['utente_nome'], ENT_QUOTES, 'UTF-8');
$content .= <<<HTML
        </strong>!</p>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuovaBanca">
            Nuova Banca
        </button>
    </div>
    <hr style="border-color:#cccccc; border-width: 1px;">
    $errorMsg
    $successMsg

    <!-- Tabella delle Banche -->
    <div class="table-responsive">
        <table id="bancheTable" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Agenzia</th>
                    <th>IBAN</th>
                    <th>BIC</th>
                    <th>SWIFT</th>
                    <th>Telefono</th>
                    <th>Indirizzo</th>
                    <th>Comune</th>
                    <th>Provincia</th>
                    <th>Regione</th>
                    <th>Azioni</th>
                </tr>
            </thead>
            <tbody>
HTML;

foreach ($bancheData as $banca) {
    $content .= <<<HTML
                <tr>
                    <td>{$banca['agenzia']}</td>
                    <td>{$banca['iban']}</td>
                    <td>{$banca['bic']}</td>
                    <td>{$banca['swift']}</td>
                    <td>{$banca['telefono']}</td>
                    <td>{$banca['indirizzo']}</td>
                    <td>{$banca['comune']}</td>
                    <td>{$banca['provincia']}</td>
                    <td>{$banca['regione']}</td>
                    <td>
                        <div class="d-flex justify-content-center">
                            <button type="button" class="btn btn-warning btn-sm me-2" 
                            data-bs-toggle="modal" 
                            data-bs-target="#modalModifica" 
                            data-id="{$banca['id']}" 
                            data-agenzia="{$banca['agenzia']}" 
                            data-iban="{$banca['iban']}" 
                            data-bic="{$banca['bic']}" 
                            data-swift="{$banca['swift']}" 
                            data-telefono="{$banca['telefono']}" 
                            data-indirizzo="{$banca['indirizzo']}" 
                            data-comune="{$banca['comune']}"
                            data-provincia="{$banca['provincia']}"
                            data-regione="{$banca['regione']}"
                            data-id_comune="{$banca['id_comune']}"
                            data-id_provincia="{$banca['id_provincia']}"
                            data-id_regione="{$banca['id_regione']}">
                                Modifica
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalElimina" data-id="{$banca['id']}">
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

    <!-- Modal Nuova Banca -->
    <div class="modal fade" id="modalNuovaBanca" tabindex="-1" aria-labelledby="modalNuovaBancaLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalNuovaBancaLabel">Nuova Banca</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="index.php?page=banche">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="create">
                        <div class="mb-3">
                            <label for="nuova-agenzia" class="form-label">Agenzia:</label>
                            <input type="text" id="nuova-agenzia" name="agenzia" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="nuova-iban" class="form-label">IBAN:</label>
                            <input type="text" id="nuova-iban" name="iban" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="nuova-bic" class="form-label">BIC:</label>
                            <input type="text" id="nuova-bic" name="bic" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="nuova-swift" class="form-label">SWIFT:</label>
                            <input type="text" id="nuova-swift" name="swift" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="nuova-telefono" class="form-label">Telefono:</label>
                            <input type="text" id="nuova-telefono" name="telefono" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="nuova-indirizzo" class="form-label">Indirizzo:</label>
                            <input type="text" id="nuova-indirizzo" name="indirizzo" class="form-control">
                        </div>
                        <div class="mb-3 position-relative">
                            <label for="nuovo-comune" class="form-label">Comune:</label>
                            <input type="text" id="nuovo-comune" name="comune" class="form-control" placeholder="Inizia a digitare il comune" autocomplete="off">
                            <input type="hidden" id="nuovo-id_comune" name="id_comune">
                            <ul id="nuovo-comune-list" class="dropdown-menu" style="position: absolute; width: 100%; max-height: 200px; overflow-y: auto; display: none;"></ul>
                        </div>
                        <div class="mb-3">
                            <label for="nuovo-provincia" class="form-label">Provincia:</label>
                            <input type="text" id="nuovo-provincia" name="provincia" class="form-control" readonly>
                            <input type="hidden" id="nuovo-id_provincia" name="id_provincia">
                        </div>
                        <div class="mb-3">
                            <label for="nuovo-regione" class="form-label">Regione:</label>
                            <input type="text" id="nuovo-regione" name="regione" class="form-control" readonly>
                            <input type="hidden" id="nuovo-id_regione" name="id_regione">
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
                    <h5 class="modal-title" id="modalModificaLabel">Modifica Banca</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="index.php?page=banche">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" id="modifica-id" name="id">
                        <div class="mb-3">
                            <label for="modifica-agenzia" class="form-label">Agenzia:</label>
                            <input type="text" id="modifica-agenzia" name="agenzia" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="modifica-iban" class="form-label">IBAN:</label>
                            <input type="text" id="modifica-iban" name="iban" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="modifica-bic" class="form-label">BIC:</label>
                            <input type="text" id="modifica-bic" name="bic" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="modifica-swift" class="form-label">SWIFT:</label>
                            <input type="text" id="modifica-swift" name="swift" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="modifica-telefono" class="form-label">Telefono:</label>
                            <input type="text" id="modifica-telefono" name="telefono" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="modifica-indirizzo" class="form-label">Indirizzo:</label>
                            <input type="text" id="modifica-indirizzo" name="indirizzo" class="form-control">
                        </div>
                        <div class="mb-3 position-relative">
                            <label for="modifica-comune" class="form-label">Comune:</label>
                            <input type="text" id="modifica-comune" name="comune" class="form-control" placeholder="Inizia a digitare il comune" autocomplete="off">
                            <input type="hidden" id="modifica-id_comune" name="id_comune">
                            <ul id="modifica-comune-list" class="dropdown-menu" style="position: absolute; width: 100%; max-height: 200px; overflow-y: auto; display: none;"></ul>
                        </div>
                        <div class="mb-3">
                            <label for="modifica-provincia" class="form-label">Provincia:</label>
                            <input type="text" id="modifica-provincia" name="provincia" class="form-control" readonly>
                            <input type="hidden" id="modifica-id_provincia" name="id_provincia">
                        </div>
                        <div class="mb-3">
                            <label for="modifica-regione" class="form-label">Regione:</label>
                            <input type="text" id="modifica-regione" name="regione" class="form-control" readonly>
                            <input type="hidden" id="modifica-id_regione" name="id_regione">
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
                    <h5 class="modal-title" id="modalEliminaLabel">Elimina Banca</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="index.php?page=banche">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" id="elimina-id" name="id">
                        <p>Sei sicuro di voler eliminare questa banca?</p>
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
        const table = $('#bancheTable').DataTable();

        // Motore di ricerca Comuni
        function initializeComuneSearch(comuneInputId, comuneListId, provinciaInputId, regioneInputId, idComuneInputId, idProvinciaInputId, idRegioneInputId) 
        {
            const comuneInput = document.getElementById(comuneInputId);
            const comuneList = document.getElementById(comuneListId);
            const provinciaInput = document.getElementById(provinciaInputId);
            const regioneInput = document.getElementById(regioneInputId);
            const idComuneInput = document.getElementById(idComuneInputId);
            const idProvinciaInput = document.getElementById(idProvinciaInputId);
            const idRegioneInput = document.getElementById(idRegioneInputId);

            // Gestore per l'input del campo Comune
            comuneInput.addEventListener('input', function () {
                const query = comuneInput.value.trim();

                if (query.length >= 2) {
                    fetch('index.php?page=banche&ajax=get_comuni&query=' + query)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Errore nella risposta del server');
                            }
                            return response.json();
                        })
                        .then(data => {
                            comuneList.innerHTML = '';
                            comuneList.style.display = 'block';

                            data.forEach(comune => {
                                const li = document.createElement('li');
                                li.className = 'dropdown-item';
                                li.textContent = comune.comune;
                                li.dataset.idComune = comune.id;

                                li.addEventListener('click', function () {
                                    comuneInput.value = comune.comune; // Mostra il nome del comune
                                    idComuneInput.value = comune.id; // Salva l'ID del comune selezionato
                                    comuneList.style.display = 'none'; // Nascondi la lista

                                    // Fai una richiesta per ottenere dettagli su provincia e regione
                                    fetch('index.php?page=banche&ajax=get_comune_details&id_comune=' + comune.id)
                                        .then(response => {
                                            if (!response.ok) {
                                                throw new Error('Errore nella risposta del server');
                                            }
                                            return response.json();
                                        })
                                        .then(details => {
                                            provinciaInput.value = details.provincia_nome || ''; // Imposta la provincia
                                            idProvinciaInput.value = details.id_provincia || ''; // Imposta l'ID della provincia
                                            regioneInput.value = details.regione_nome || ''; // Imposta la regione
                                            idRegioneInput.value = details.id_regione || ''; // Imposta l'ID della regione
                                        })
                                        .catch(error => console.error('Errore nel caricamento dei dettagli:', error));
                                });

                                comuneList.appendChild(li);
                            });
                        })
                        .catch(error => console.error('Errore nella richiesta AJAX:', error));
                } else {
                    comuneList.style.display = 'none';
                }
            });

            // Nascondi il menu a tendina quando clicchi fuori
            document.addEventListener('click', function (event) {
                if (!comuneList.contains(event.target) && event.target !== comuneInput) {
                    comuneList.style.display = 'none';
                }
            });
        }
        // Inizializza la ricerca per il modulo Nuovo Cliente
        initializeComuneSearch(
            'nuovo-comune', // ID del campo Comune
            'nuovo-comune-list', // ID della lista a tendina
            'nuovo-provincia', // ID del campo Provincia
            'nuovo-regione', // ID del campo Regione
            'nuovo-id_comune', // ID del campo nascosto Comune
            'nuovo-id_provincia', // ID del campo nascosto Provincia
            'nuovo-id_regione' // ID del campo nascosto Regione
        );
        // Inizializza la ricerca per il modulo Modifica Cliente
        initializeComuneSearch(
            'modifica-comune', // ID del campo Comune
            'modifica-comune-list', // ID della lista a tendina
            'modifica-provincia', // ID del campo Provincia
            'modifica-regione', // ID del campo Regione
            'modifica-id_comune', // ID del campo nascosto Comune
            'modifica-id_provincia', // ID del campo nascosto Provincia
            'modifica-id_regione' // ID del campo nascosto Regione
        );

        // Modal riempimento campi per Modifica
        $('#modalModifica').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget);
            const id = button.data('id');
            const agenzia = button.data('agenzia');
            const iban = button.data('iban');
            const bic = button.data('bic');
            const swift = button.data('swift');
            const telefono = button.data('telefono');
            const indirizzo = button.data('indirizzo');
            const comune = button.data('comune');
            const provincia = button.data('provincia');
            const regione = button.data('regione');
            const id_comune = button.data('id_comune');
            const id_provincia = button.data('id_provincia');
            const id_regione = button.data('id_regione');

            const modal = $(this);
            modal.find('#modifica-id').val(id);
            modal.find('#modifica-agenzia').val(agenzia);
            modal.find('#modifica-iban').val(iban);
            modal.find('#modifica-bic').val(bic);
            modal.find('#modifica-swift').val(swift);
            modal.find('#modifica-telefono').val(telefono);
            modal.find('#modifica-indirizzo').val(indirizzo);
            modal.find('#modifica-comune').val(comune);
            modal.find('#modifica-provincia').val(provincia);
            modal.find('#modifica-regione').val(regione);
            modal.find('#modifica-id_comune').val(id_comune);
            modal.find('#modifica-id_provincia').val(id_provincia);
            modal.find('#modifica-id_regione').val(id_regione);
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
$tableId = 'bancheTable'; // Specifica l'ID della tabella
include 'layout.php';
?>
