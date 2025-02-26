<?php
// Recupera tutte le aziende per mostrarle nella tabella
$aziendeData = $aziende->all();

// Messaggi di errore o conferma
$errorMsg = isset($error) ? "<div class='alert alert-danger'>{$error}</div>" : '';
$successMsg = isset($success) ? "<div class='alert alert-success'>{$success}</div>" : '';

// Costruzione del contenuto dinamico
$content = <<<HTML
<div class="container mt-4">
    <h2 class="mb-4">Aziende</h2>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <p class="mb-0">Benvenuto, <strong>
HTML;
$content .= htmlspecialchars($_SESSION['utente_nome'], ENT_QUOTES, 'UTF-8');
$content .= <<<HTML
        </strong>!</p>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuovaAzienda">
            Nuova Azienda
        </button>
    </div>
    <hr style="border-color:#cccccc; border-width: 1px;">
    $errorMsg
    $successMsg

    <!-- Tabella delle Aziende -->
    <div class="table-responsive">
        <table id="aziendeTable" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Ragione Sociale</th>
                    <th>Nome</th>
                    <th>Cognome</th>
                    <th>Indirizzo</th>
                    <th>Comune</th>
                    <th>Provincia</th>
                    <th>Regione</th>
                    <th>Partita IVA</th>
                    <th>N. Rea</th>
                    <th>Telefono</th>
                    <th>Email</th>
                    <th>Pec</th>
                    <th>Azioni</th>
                </tr>
            </thead>
            <tbody>
HTML;

foreach ($aziendeData as $azienda) {
    $content .= <<<HTML
                <tr>
                    <td>{$azienda['societa']}</td>
                    <td>{$azienda['nome']}</td>
                    <td>{$azienda['cognome']}</td>
                    <td>{$azienda['indirizzo']}</td>
                    <td>{$azienda['comune']}</td>
                    <td>{$azienda['provincia']}</td>
                    <td>{$azienda['regione']}</td>
                    <td>{$azienda['p_iva']}</td>
                    <td>{$azienda['rea']}</td>
                    <td>{$azienda['telefono']}</td>
                    <td>{$azienda['email']}</td>
                    <td>{$azienda['pec']}</td>
                    <td>
                        <div class="d-flex justify-content-center">
                            <button type="button" class="btn btn-warning btn-sm me-2" 
                                data-bs-toggle="modal" 
                                data-bs-target="#modalModifica" 
                                data-id="{$azienda['id']}" 
                                data-ragione_sociale="{$azienda['societa']}" 
                                data-nome="{$azienda['nome']}" 
                                data-cognome="{$azienda['cognome']}" 
                                data-indirizzo="{$azienda['indirizzo']}" 
                                data-comune="{$azienda['comune']}"
                                data-provincia="{$azienda['provincia']}"
                                data-regione="{$azienda['regione']}"
                                data-id_comune="{$azienda['id_comune']}"
                                data-id_provincia="{$azienda['id_provincia']}"
                                data-id_regione="{$azienda['id_regione']}"
                                data-p_iva="{$azienda['p_iva']}"
                                data-rea="{$azienda['rea']}"
                                data-telefono="{$azienda['telefono']}" 
                                data-email="{$azienda['email']}" 
                                data-pec="{$azienda['pec']}">
                                Modifica
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalElimina" data-id="{$azienda['id']}">
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
</div>


<!-- Modal Nuova Azienda -->
<div class="modal fade" id="modalNuovaAzienda" tabindex="-1" aria-labelledby="modalNuovaAziendaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalNuovaAziendaLabel">Nuova Azienda</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="index.php?page=aziende">
                <div class="modal-body">
                    <input type="hidden" name="action" value="create">
                    <div class="mb-3">
                        <label for="nuova-ragione-sociale" class="form-label">Ragione Sociale:</label>
                        <input type="text" id="nuova-ragione-sociale" name="ragione_sociale" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="nuova-indirizzo" class="form-label">Indirizzo:</label>
                        <input type="text" id="nuova-indirizzo" name="indirizzo" class="form-control" required>
                    </div>
                    <div class="mb-3 position-relative">
                        <label for="nuovo-comune" class="form-label">Comune:</label>
                        <input type="text" id="nuovo-comune" name="comune" class="form-control" placeholder="Inizia a digitare il comune" autocomplete="off" required>
                        <input type="hidden" id="nuovo-id_comune" name="id_comune">
                        <ul id="nuovo-comune-list" class="dropdown-menu" style="position: absolute; width: 100%; max-height: 200px; overflow-y: auto; display: none;"></ul>
                    </div>
                    <div class="mb-3">
                        <label for="nuova-provincia" class="form-label">Provincia:</label>
                        <input type="text" id="nuova-provincia" name="provincia" class="form-control" readonly>
                        <input type="hidden" id="nuova-id_provincia" name="id_provincia">
                    </div>
                    <div class="mb-3">
                        <label for="nuova-regione" class="form-label">Regione:</label>
                        <input type="text" id="nuova-regione" name="regione" class="form-control" readonly>
                        <input type="hidden" id="nuova-id_regione" name="id_regione">
                    </div>
                    <div class="mb-3">
                        <label for="nuova-p_iva" class="form-label">Partita IVA:</label>
                        <input type="text" id="nuova-p_iva" name="p_iva" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="nuova-telefono" class="form-label">Telefono:</label>
                        <input type="text" id="nuova-telefono" name="telefono" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="nuova-email" class="form-label">Email:</label>
                        <input type="email" id="nuova-email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="nuova-pec" class="form-label">PEC:</label>
                        <input type="text" id="nuova-pec" name="pec" class="form-control">
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

<!-- Modal Modifica Azienda -->
<div class="modal fade" id="modalModificaAzienda" tabindex="-1" aria-labelledby="modalModificaAziendaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalModificaAziendaLabel">Modifica Azienda</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="index.php?page=aziende">
                <div class="modal-body">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" id="modifica-id" name="id">
                    <div class="mb-3">
                        <label for="modifica-ragione-sociale" class="form-label">Ragione Sociale:</label>
                        <input type="text" id="modifica-ragione-sociale" name="ragione_sociale" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="modifica-indirizzo" class="form-label">Indirizzo:</label>
                        <input type="text" id="modifica-indirizzo" name="indirizzo" class="form-control" required>
                    </div>
                    <div class="mb-3 position-relative">
                        <label for="modifica-comune" class="form-label">Comune:</label>
                        <input type="text" id="modifica-comune" name="comune" class="form-control" placeholder="Inizia a digitare il comune" autocomplete="off" required>
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
                    <div class="mb-3">
                        <label for="modifica-p_iva" class="form-label">Partita IVA:</label>
                        <input type="text" id="modifica-p_iva" name="p_iva" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="modifica-telefono" class="form-label">Telefono:</label>
                        <input type="text" id="modifica-telefono" name="telefono" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="modifica-email" class="form-label">Email:</label>
                        <input type="email" id="modifica-email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="modifica-pec" class="form-label">PEC:</label>
                        <input type="text" id="modifica-pec" name="pec" class="form-control">
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

<!-- Modal Elimina Azienda -->
<div class="modal fade" id="modalEliminaAzienda" tabindex="-1" aria-labelledby="modalEliminaAziendaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEliminaAziendaLabel">Elimina Azienda</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="index.php?page=aziende">
                <div class="modal-body">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" id="elimina-id" name="id">
                    <p>Sei sicuro di voler eliminare questa azienda?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-danger">Elimina</button>
                </div>
            </form>
        </div>
    </div>
</div>



<script>
    document.addEventListener('DOMContentLoaded', () => {
        const table = $('#aziendeTable').DataTable();

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
                    fetch('index.php?page=aziende&ajax=get_comuni&query=' + query)
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
                                    fetch('index.php?page=aziende&ajax=get_comune_details&id_comune=' + comune.id)
                                        .then(response => response.json())
                                        .then(details => {
                                            console.log("Dettagli ricevuti:", details); // Debug per vedere i dati
                                        
                                            // Controllo se gli input esistono prima di assegnare i valori
                                            let provinciaInput = document.getElementById("nuova-provincia");
                                            let idProvinciaInput = document.getElementById("nuova-id_provincia");
                                            let regioneInput = document.getElementById("nuova-regione");
                                            let idRegioneInput = document.getElementById("nuova-id_regione");

                                            if (!provinciaInput || !idProvinciaInput || !regioneInput || !idRegioneInput) {
                                                console.error("Uno o più campi non esistono nel DOM!");
                                                return;
                                            }

                                            provinciaInput.value = details.provincia_nome || ''; 
                                            idProvinciaInput.value = details.id_provincia || ''; 
                                            regioneInput.value = details.regione_nome || ''; 
                                            idRegioneInput.value = details.id_regione || ''; 
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
        // Inizializza la ricerca per il modulo Nuova Azienda
        initializeComuneSearch(
            'nuovo-comune', // ID del campo Comune
            'nuovo-comune-list', // ID della lista a tendina
            'nuovo-provincia', // ID del campo Provincia
            'nuovo-regione', // ID del campo Regione
            'nuovo-id_comune', // ID del campo nascosto Comune
            'nuovo-id_provincia', // ID del campo nascosto Provincia
            'nuovo-id_regione' // ID del campo nascosto Regione
        );
        // Inizializza la ricerca per il modulo Modifica Azienda
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
            const societa = button.data('societa');
            const nome = button.data('nome');
            const cognome = button.data('cognome');
            const indirizzo = button.data('indirizzo');
            const comune = button.data('comune');
            const provincia = button.data('provincia');
            const regione = button.data('regione');
            const id_comune = button.data('id_comune');
            const id_provincia = button.data('id_provincia');
            const id_regione = button.data('id_regione');
            const p_iva = button.data('p_iva');
            const rea = button.data('rea');
            const telefono = button.data('telefono');
            const email = button.data('email');
            const pec = button.data('pec');

            const modal = $(this);
            modal.find('#modifica-id').val(id);
            modal.find('#modifica-societa').val(societa);
            modal.find('#modifica-nome').val(nome);
            modal.find('#modifica-cognome').val(cognome);
            modal.find('#modifica-indirizzo').val(indirizzo);
            modal.find('#modifica-comune').val(comune);
            modal.find('#modifica-provincia').val(provincia);
            modal.find('#modifica-regione').val(regione);
            modal.find('#modifica-id_comune').val(id_comune);
            modal.find('#modifica-id_provincia').val(id_provincia);
            modal.find('#modifica-id_regione').val(id_regione);
            modal.find('#modifica-p_iva').val(p_iva);
            modal.find('#modifica-rea').val(rea);
            modal.find('#modifica-telefono').val(telefono);
            modal.find('#modifica-email').val(email);
            modal.find('#modifica-pec').val(pec);
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
$tableId = 'aziendeTable'; // Specifica l'ID della tabella
include 'layout.php';
?>
