<?php
// Recupera i dati per i cantieri
if (!isset($cantieri)) {
    $cantieri = new Cantieri($db);
}
if (!isset($clienti)) {
    require_once 'backend/clienti.php';
    $clienti = new Clienti($db);
}
if (!isset($posatori)) {
    require_once 'backend/posatori.php';
    $posatori = new Posatori($db);
}
if (!isset($utenti)) {
    require_once 'backend/utenti.php';
    $utenti = new Utenti($db);
}
if (!isset($regioni)) {
    require_once 'backend/regioni.php';
    $regioni = new Regioni($db);
}
if (!isset($province)) {
    require_once 'backend/province.php';
    $province = new Province($db);
}
if (!isset($comuni)) {
    require_once 'backend/comuni.php';
    $comuni = new Comuni($db);
}
if (!isset($statoCantiere)) {
    require_once 'backend/stato_cantieri.php';
    $statoCantiere = new StatoCantiere($db);
}
if (!isset($listino)) {
    require_once 'backend/listino.php';
    $listino = new Listino($db);
}

$cantieriData = $cantieri->all();
$clientiData = $clienti->all();
$posatoriData = $posatori->all();
$utentiData = $utenti->all();
$regioniData = $regioni->all();
$provinceData = $province->all();
$comuniData = $comuni->all();
$statiCantiereData = $statoCantiere->all();
$listinoData = $listino->all();
$totaliData = $cantieri->getTotali();

// Messaggi di errore o conferma
$errorMsg = isset($error) ? "<div class='alert alert-danger'>{$error}</div>" : '';
$successMsg = isset($success) ? "<div class='alert alert-success'>{$success}</div>" : '';

// Contenuto dinamico della pagina
ob_start();
?>
<div class="container mt-4">
    <h2 class="mb-4">Gestione Cantieri</h2>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <p class="mb-0">Benvenuto, <strong><?= htmlspecialchars($_SESSION['utente_nome'] ?? 'Ospite', ENT_QUOTES, 'UTF-8') ?></strong>!</p>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCantiere" data-action="create">
            Nuovo Cantiere
        </button>
    </div>
    <hr style="border-color:#cccccc; border-width: 1px;">
    <?= $errorMsg ?>
    <?= $successMsg ?>

    <!-- Tabella dei Cantieri -->
    <div class="table-responsive">
        <table id="cantieriTable" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Comune</th>
                    <th>Provincia</th>
                    <th>Stato cantiere</th>
                    <th>Importo</th>
                    <th class="d-none">Spese</th> <!-- Campo nascosto -->
                    <th class="d-none">Guadagno</th> <!-- Campo nascosto -->
                    <th>Inizio</th>
                    <th>Fine</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($cantieriData)): ?>
                    <?php foreach ($cantieriData as $cantiere): ?>
                        <tr>
                            <td><?= htmlspecialchars($cantiere['cliente_nome'] . ' ' . $cantiere['cliente_cognome'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($cantiere['comune_nome'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($cantiere['provincia_nome'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                                <div style="display: flex; align-items: center;">
                                    <div style="width: 20px; height: 20px; background-color: <?= htmlspecialchars($cantiere['stato_cantiere_colore'], ENT_QUOTES, 'UTF-8') ?>; border: 1px solid #000; margin-right: 8px;"></div>
                                    <?= htmlspecialchars($cantiere['stato_cantiere_nome'], ENT_QUOTES, 'UTF-8') ?>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($cantiere['importo'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="spese d-none" data-value="<?= $cantiere['spese'] ?>">€ <?= number_format($cantiere['spese'], 2, ',', '.') ?></td>
                            <td class="guadagno d-none" data-value="<?= $cantiere['guadagno'] ?>">€ <?= number_format($cantiere['guadagno'], 2, ',', '.') ?></td>
                            <td><?= htmlspecialchars($cantiere['data_inizio'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($cantiere['data_fine'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                                <div class="d-flex justify-content-center">

                                    <!-- Pulsante Dettagli -->
                                    <button type="button" class="btn btn-info btn-sm me-2 btn-dettagli" data-id="<?= $cantiere['id'] ?>">
                                        Dettagli
                                    </button>

                                    <!-- Pulsante Allegati -->
                                    <button type="button" class="btn btn-dark btn-sm me-2 btn-allegati" 
                                            data-id="<?= $cantiere['id'] ?>" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalAllegatiFotogallery">
                                        Allegati
                                    </button>

                                    <!-- Pulsante Materiali -->
                                    <button type="button" 
                                            class="btn btn-secondary btn-sm me-2 btn-materiali" 
                                            data-id="<?= $cantiere['id'] ?>" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalMateriali">
                                        Materiali
                                    </button>

                                    <!-- Pulsante Google -->
                                    <button type="button" class="btn btn-primary btn-sm me-2 btn-google" 
                                            data-id="<?= $cantiere['id'] ?>" 
                                            data-indirizzo="<?= htmlspecialchars($cantiere['indirizzo'], ENT_QUOTES, 'UTF-8') ?> - <?=$cantiere['cap_nome'] ?> <?=$cantiere['comune_nome'] ?> <?=$cantiere['provincia_nome'] ?> <?=$cantiere['regione_nome'] ?>"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalGoogle">
                                        Google
                                    </button>

                                    <!-- Pulsante Modifica -->
                                    <button type="button" 
                                        class="btn btn-warning btn-sm me-2" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalCantiere"
                                        data-action="update"
                                        data-id="<?= $cantiere['id'] ?>" 
                                        data-id_cliente="<?= $cantiere['id_cliente'] ?>" 
                                        data-id_posatore="<?= $cantiere['id_posatore'] ?>" 
                                        data-id_comune="<?= $cantiere['id_comune'] ?>" 
                                        data-id_provincia="<?= $cantiere['id_provincia'] ?>" 
                                        data-id_regione="<?= $cantiere['id_regione'] ?>" 
                                        data-comune_nome="<?= htmlspecialchars($cantiere['comune_nome'], ENT_QUOTES, 'UTF-8') ?>" 
                                        data-provincia_nome="<?= htmlspecialchars($cantiere['provincia_nome'], ENT_QUOTES, 'UTF-8') ?>"
                                        data-regione_nome="<?= htmlspecialchars($cantiere['regione_nome'], ENT_QUOTES, 'UTF-8') ?>" 
                                        data-id_stato="<?= $cantiere['id_stato_cantiere'] ?>" 
                                        data-indirizzo="<?= htmlspecialchars($cantiere['indirizzo'], ENT_QUOTES, 'UTF-8') ?>" 
                                        data-note="<?= htmlspecialchars($cantiere['note'], ENT_QUOTES, 'UTF-8') ?>" 
                                        data-data_inizio="<?= htmlspecialchars($cantiere['data_inizio'], ENT_QUOTES, 'UTF-8') ?>" 
                                        data-data_fine="<?= htmlspecialchars($cantiere['data_fine'], ENT_QUOTES, 'UTF-8') ?>"
                                        data-cliente_nome="<?= htmlspecialchars($cantiere['cliente_nome'], ENT_QUOTES, 'UTF-8') ?>">
                                        Modifica
                                    </button>

                                    <!-- Pulsante Elimina -->
                                    <button type="button" class="btn btn-danger btn-sm  me-2" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalElimina" 
                                        data-id="<?= $cantiere['id'] ?>" 
                                        data-action="delete">
                                        Elimina
                                    </button>

                                    <!-- Pulsante Stampa Preventivo -->
                                    <button type="button" class="btn btn-success btn-sm btn-stampa-preventivo"
                                            data-id="<?= $cantiere['id'] ?>">
                                            Preventivo
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">Nessun cantiere disponibile.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="7" class="text-end"><strong>Totale Cantieri:</strong></td>
                    <td id="totaleFatturato"><?= $totaliData['totale_cantieri'] ?></td>
                </tr>
                <tr>
                    <td colspan="7" class="text-end"><strong>Totale Fatturato:</strong></td>
                    <td id="totaleFatturato">€ <?= number_format($totaliData['totale_importo'], 2, ",", ".") ?></td>
                </tr>
                <tr>
                    <td colspan="7" class="text-end"><strong>Totale Spese:</strong></td>
                    <td id="totaleSpeseVisibile">€ <?= number_format($totaliData['totale_spese'], 2, ",", ".") ?></td>
                </tr>
                <tr>
                    <td colspan="7" class="text-end"><strong>Totale Guadagno:</strong></td>
                    <td id="totaleGuadagnoVisibile">€ <?= number_format($totaliData['totale_guadagno'], 2, ",", ".") ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>


<!-- Modale Nuovo Cantiere -->
<div class="modal fade" id="modalCantiere" tabindex="-1" aria-labelledby="modalCantiereLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCantiereLabel">Nuovo Cantiere</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formCantiere" method="POST" action="index.php?page=cantieri">
                <div class="modal-body">
                    <input type="hidden" name="action" id="modal-action" value="create">
                    <input type="hidden" name="id" id="cantiere-id">
                    <input type="hidden" id="id-comune" name="id_comune">
                    <input type="hidden" id="id-provincia" name="id_provincia">
                    <input type="hidden" id="id-regione" name="id_regione">


                    <!-- Campi esistenti della modale -->
                    <div class="mb-3 position-relative">
                        <label for="cliente" class="form-label">Cliente:</label>
                        <div class="d-flex align-items-center">
                            <input type="text" id="cliente" name="cliente" class="form-control" placeholder="Inizia a digitare il cliente" autocomplete="off">
                            <input type="hidden" id="id-cliente" name="id_cliente"> <!-- Campo nascosto per l'ID -->
                            <button type="button" class="btn btn-success ms-2" data-bs-toggle="modal" data-bs-target="#modalNuovoCliente">
                                <i class="bi bi-plus-circle"></i> <!-- Icona Bootstrap -->
                            </button>
                        </div>
                        <ul id="cliente-list" class="dropdown-menu" style="position: absolute; width: 100%; max-height: 200px; overflow-y: auto; display: none;"></ul>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="use-client-address" name="use_client_address">
                        <label class="form-check-label" for="use-client-address">
                            I dati del cliente sono uguali a quelli del cantiere
                        </label>
                    </div>
                    <div class="mb-3 position-relative">
                        <label for="comune" class="form-label">Comune:</label>
                        <input type="text" id="comune" name="comune" class="form-control" placeholder="Inizia a digitare il comune" autocomplete="off">
                        <ul id="comune-list" class="dropdown-menu" style="position: absolute; width: 100%; max-height: 200px; overflow-y: auto; display: none;"></ul>
                    </div>
                    <div class="mb-3">
                        <label for="provincia" class="form-label">Provincia:</label>
                        <input type="text" id="provincia" name="provincia" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="regione" class="form-label">Regione:</label>
                        <input type="text" id="regione" name="regione" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="indirizzo" class="form-label">Indirizzo:</label>
                        <input type="text" id="indirizzo" name="indirizzo" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="id-posatore" class="form-label">Posatore:</label>
                        <select id="id-posatore" name="id_posatore" class="form-select" required>
                            <?php foreach ($posatoriData as $posatore): ?>
                                <option value="<?= $posatore['id'] ?>"><?= htmlspecialchars($posatore['nome'].' '.$posatore['cognome'], ENT_QUOTES, 'UTF-8') ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="id-stato" class="form-label">Stato Cantiere:</label>
                        <select id="id-stato" name="id_stato_cantiere" class="form-select" required>
                            <?php foreach ($statiCantiereData as $stato): ?>
                                <option value="<?= $stato['id'] ?>"><?= htmlspecialchars($stato['stato_cantiere'], ENT_QUOTES, 'UTF-8') ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="note" class="form-label">Note:</label>
                        <textarea id="note" name="note" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="data-inizio" class="form-label">Data Inizio:</label>
                        <input type="date" id="data-inizio" name="data_inizio" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="data-fine" class="form-label">Data Fine:</label>
                        <input type="date" id="data-fine" name="data_fine" class="form-control" required>
                    </div>

                    <!-- Parte dinamica per le voci del listino -->
                    <br><h6>Aggiungi Voci Cantiere</h6><br>

                    <div id="azioni-container">
                        <!-- Righe dinamiche vengono aggiunte qui -->
                    </div>

                    <button type="button" class="btn btn-success btn-add">Aggiungi Voce</button>

                    <div id="totale-generale-container" class="d-flex justify-content-end mt-3">
                        <span class="form-control me-2 fw-bold text-end" id="totale-generale">Totale Generale: €0.00</span>
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


<!-- Modale per confermare l'eliminazione -->
<div class="modal fade" id="modalElimina" tabindex="-1" aria-labelledby="modalEliminaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEliminaLabel">Conferma Eliminazione</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Sei sicuro di voler eliminare questo cantiere e tutte le voci associate?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                <button type="button" id="confirmDelete" class="btn btn-danger">Conferma</button>
            </div>
        </div>
    </div>
</div>


<!-- Modale Allegati e Fotogallery -->
<div class="modal fade" id="modalAllegatiFotogallery" tabindex="-1" aria-labelledby="modalAllegatiFotogalleryLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAllegatiFotogalleryLabel">Gestione Allegati e Fotogallery</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <!-- Tab per separare Allegati e Fotogallery -->
                <ul class="nav nav-tabs" id="allegatiFotogalleryTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="allegati-tab" data-bs-toggle="tab" data-bs-target="#allegati" type="button" role="tab" aria-controls="allegati" aria-selected="true">
                            Allegati
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="fotogallery-tab" data-bs-toggle="tab" data-bs-target="#fotogallery" type="button" role="tab" aria-controls="fotogallery" aria-selected="false">
                            Fotogallery
                        </button>
                    </li>
                </ul>

                <div class="tab-content mt-3" id="allegatiFotogalleryTabContent">

                    <!-- Sezione Allegati -->
                    <div class="tab-pane fade show active" id="allegati" role="tabpanel" aria-labelledby="allegati-tab">
                        <form id="allegatiForm" enctype="multipart/form-data">
                            <input type="hidden" name="id_cantiere" id="allegati-id-cantiere">
                            <div class="mb-3">
                                <label for="allegatiFile" class="form-label">Seleziona file:</label>
                                <input type="file" name="allegati[]" id="allegatiFile" class="form-control" multiple>
                            </div>
                            <button type="button" class="btn btn-primary" id="caricaAllegati">Carica Allegati</button>
                        </form>
                        <hr>
                        <h6>Allegati esistenti:</h6>
                        <table class="table table-striped" id="allegatiTable">
                            <thead>
                                <tr>
                                    <th>File</th>
                                    <th>Azione</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Dati caricati dinamicamente -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Sezione Fotogallery -->
                    <div class="tab-pane fade" id="fotogallery" role="tabpanel" aria-labelledby="fotogallery-tab">
                        <form id="fotogalleryForm" enctype="multipart/form-data">
                            <input type="hidden" name="id_cantiere" id="fotogallery-id-cantiere">
                            <div class="mb-3">
                                <label for="fotogalleryFile" class="form-label">Seleziona immagini:</label>
                                <input type="file" name="fotogallery[]" id="fotogalleryFile" class="form-control" multiple accept="image/*">
                            </div>
                            <button type="button" class="btn btn-primary" id="caricaFotogallery">Carica Fotogallery</button>
                        </form>
                        <hr>
                        <h6>Fotogallery esistente:</h6>
                        <div id="fotogalleryGrid" class="row g-3">
                            <!-- Immagini caricate dinamicamente -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
            </div>
        </div>
    </div>
</div>


<!-- Modale per l'immagine grande -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Anteprima Immagine</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="imagePreview" src="" alt="Immagine Grande" class="img-fluid">
            </div>
        </div>
    </div>
</div>


<!-- Modale Visualizza Dettagli Cantiere-->
<div class="modal fade" id="modalDettagli" tabindex="-1" aria-labelledby="modalDettagliLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDettagliLabel">Dettagli Cantiere</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <div class="row">
                    <!-- Colonna 1 -->
                    <div class="col-md-6">
                        <p><strong>Cliente:</strong> <span id="dettagli-cliente"></span></p>
                        <p><strong>Posatore:</strong> <span id="dettagli-posatore"></span></p>
                        <p><strong>Comune:</strong> <span id="dettagli-comune"></span></p>
                        <p><strong>Provincia:</strong> <span id="dettagli-provincia"></span></p>
                        <p><strong>Regione:</strong> <span id="dettagli-regione"></span></p>
                    </div>
                    <!-- Colonna 2 -->
                    <div class="col-md-6">
                        <p><strong>Indirizzo:</strong> <span id="dettagli-indirizzo"></span></p>
                        <p><strong>Stato cantiere:</strong> <span id="dettagli-stato"></span></p>
                        <p><strong>Data Inizio:</strong> <span id="dettagli-data-inizio"></span></p>
                        <p><strong>Data Fine:</strong> <span id="dettagli-data-fine"></span></p>
                        <p><strong>Note:</strong> <span id="dettagli-note"></span></p>
                    </div>
                </div>

                <hr>
                
                <table class="table">
                    <thead>
                        <tr>
                            <th>Voce</th>
                            <th>Quantità</th>
                            <th>Prezzo</th>
                            <th>Totale</th>
                        </tr>
                    </thead>
                    <tbody id="dettagli-voci">
                        <!-- Righe generate dinamicamente -->
                    </tbody>
                </table>
                <br>
                <p><strong>Prezzo Totale:</strong> <span id="dettagli-totale"></span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
                <button type="button" class="btn btn-primary" id="btnStampa">Stampa</button>
                <button type="button" class="btn btn-success" id="btnSalvaPDF">Salva come PDF</button>
            </div>
        </div>
    </div>
</div>


<!-- Modale Google -->
<div class="modal fade" id="modalGoogle" tabindex="-1" aria-labelledby="modalGoogleLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalGoogleLabel">Cerca Esercizi Commerciali</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <!-- Combobox per scegliere il tipo -->
                <div class="mb-3">
                    <label for="types" class="form-label">Seleziona il tipo di esercizio:</label>
                    <select id="types" class="form-select">
                        <option value=""></option>
                        <option value="restaurant-trattoria">Trattoria</option>
                        <option value="restaurant-pizzeria">Pizzeria</option>
                        <option value="meal_takeaway">Kebab</option>
                        <option value="bar">Bar</option>
                        <option value="hardware_store">Ferramenta</option>
                        <option value="gas_station">Distributore</option>
                        <option value="shopping_mall">Centro commerciale</option>
                        <option value="car_repair">Meccanico</option>
                    </select>
                </div>

                <button id="startSearch" class="btn btn-primary">Avvia Ricerca</button>

                <!-- Messaggio di caricamento -->
                <p id="googleModalMessage" class="mt-3"></p>

                <!-- Tabella per mostrare i risultati -->
                <table id="googleResultsTable" class="table table-striped table-bordered d-none">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Indirizzo</th>
                            <th>Distanza</th>
                            <th>Salva</th>
                            <th>Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Risultati caricati dinamicamente -->
                    </tbody>
                </table>

                <!-- Divider -->
                <hr class="my-4">

                <!-- Sezione per gli indirizzi salvati -->
                <h5>Indirizzi Salvati</h5>
                <table id="savedAddressesTable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Indirizzo</th>
                            <th>Distanza</th>
                            <th>Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Indirizzi salvati caricati dinamicamente -->
                        <tr>
                            <td colspan="4" class="text-center">Nessun indirizzo salvato.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
            </div>
        </div>
    </div>
</div>



<!-- Modale Materiali Cantiere -->
<div class="modal fade" id="modalMateriali" tabindex="-1" aria-labelledby="modalMaterialiLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalMaterialiLabel">Gestione Materiali</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Contenitore per le righe dinamiche dei materiali -->
                <div id="materiali-container" class="mb-3">
                    <!-- Righe dinamiche generate con JS verranno inserite qui -->
                </div>
                <!-- Pulsante per aggiungere un nuovo materiale -->
                <button type="button" id="add-material-row" class="btn btn-success mb-3">Aggiungi Materiale</button>

                <!-- Datagrid per mostrare i materiali del cantiere -->
                <div class="table-responsive mb-3">
                    <table class="table table-striped table-bordered" id="materiali-datagrid">
                        <thead>
                            <tr>
                                <th>Nome Materiale</th>
                                <th>Quantità</th>
                                <th>Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Righe dinamiche verranno generate tramite JS -->
                        </tbody>
                    </table>
                </div>
                
                <!-- Totale generale per i materiali -->
                <div class="mt-3 text-end">
                    <span id="totale-materiali" class="fw-bold">Totale Materiali: €0.00</span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
                <button type="button" class="btn btn-primary" id="salva-materiali">Salva Materiali</button>
            </div>
        </div>
    </div>
</div>



<!-- Modale Nuovo Cliente -->
<div class="modal fade" id="modalNuovoCliente" tabindex="-1" aria-labelledby="modalNuovoClienteLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalNuovoClienteLabel">Nuovo Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="index.php?page=clienti">
                <div class="modal-body">
                    <input type="hidden" name="action" value="create">
                    <!-- Campi cliente -->
                    <div class="mb-3">
                        <label for="nuovo-societa" class="form-label">Società:</label>
                        <input type="text" id="nuovo-societa" name="societa" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="nuovo-nome" class="form-label">Nome:</label>
                        <input type="text" id="nuovo-nome" name="nome" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="nuovo-cognome" class="form-label">Cognome:</label>
                        <input type="text" id="nuovo-cognome" name="cognome" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="nuovo-indirizzo" class="form-label">Indirizzo:</label>
                        <input type="text" id="nuovo-indirizzo" name="indirizzo" class="form-control" required>
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
                    <div class="mb-3">
                        <label for="nuovo-p_iva" class="form-label">Partita IVA:</label>
                        <input type="text" id="nuovo-p_iva" name="p_iva" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="nuovo-rea" class="form-label">N. Rea:</label>
                        <input type="text" id="nuovo-rea" name="rea" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="nuovo-telefono" class="form-label">Telefono:</label>
                        <input type="text" id="nuovo-telefono" name="telefono" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="nuovo-email" class="form-label">Email:</label>
                        <input type="email" id="nuovo-email" name="email" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="nuovo-pec" class="form-label">PEC:</label>
                        <input type="text" id="nuovo-pec" name="pec" class="form-control">
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






<script>
document.addEventListener('DOMContentLoaded', function () {
    const addButton = document.querySelector('.btn-add'); // Pulsante "Aggiungi Voce"
    const container = document.getElementById('azioni-container'); // Contenitore delle righe
    const modalCantiere = document.getElementById('modalCantiere'); // Modale generica per Inserimento/Modifica
    const formCantiere = modalCantiere ? modalCantiere.querySelector('form') : null;
    const modalTitle = document.getElementById('modalCantiereLabel'); // Titolo dinamico della modale

    //Regione & Provincia
    const selectRegione = document.getElementById('id-regione'); // Select Regione
    const selectProvincia = document.getElementById('id-provincia'); // Select Provincia

    //Elimina cantiere
    const modalElimina = document.getElementById('modalElimina');

    //Dettagli cantiere
    const modalDettagli = document.getElementById('modalDettagli');
    const btnStampa = document.getElementById('btnStampa');
    const btnSalvaPDF = document.getElementById('btnSalvaPDF');
    

    // Aggiungi una nuova riga al container
    if (addButton && container) {
        addButton.addEventListener('click', function () {
            const newRow = document.createElement('div');
            newRow.classList.add('d-flex', 'align-items-center', 'mb-2', 'voce-row');
            newRow.innerHTML = `
                <input type="text" name="quantita[]" class="form-control me-2 quantita" placeholder="Quantità" required>
                <select name="id_listino[]" class="form-select me-2 listino-select" required>
                    <option value="" selected>Seleziona voce</option>
                    
                    <?php 
                    $idTipologia = 0; // Variabile per tenere traccia dell'ultima tipologia vista
                    foreach ($listinoData as $voce): 
                        if ($idTipologia != $voce['id_tipologia']): // Controlla se cambia la tipologia
                    ?>
                            <option value="" disabled></option>
                            <option value="" disabled style="font-weight: bold;"><?= htmlspecialchars($voce['tipologia_nome'], ENT_QUOTES, 'UTF-8') ?></option>
                    <?php 
                            $idTipologia = $voce['id_tipologia']; // Aggiorna la tipologia corrente
                        endif; 
                    ?>
                        <option value="<?= $voce['id'] ?>" data-prezzo="<?= $voce['prezzo'] ?>">
                            <?= htmlspecialchars($voce['voce'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>

                </select>
                <input type="text" name="prezzo[]" class="form-control me-2 prezzo" placeholder="Prezzo">
                <span class="form-control me-2 totale">Totale: 0,00</span>
                <button type="button" class="btn btn-danger btn-remove ms-2">-</button>
            `;
            container.appendChild(newRow);
            attachEventsToRow(newRow); // Aggiungi gli eventi alla nuova riga
        });

    } else {
        console.error('Errore: Il pulsante "Aggiungi Voce" o il contenitore non sono stati trovati.');
    }

    // Aggiungi eventi a una nuova riga
    function attachEventsToRow(row) {
        const quantitaInput = row.querySelector('.quantita');
        const listinoSelect = row.querySelector('.listino-select');
        const prezzoInput = row.querySelector('.prezzo');
        const totaleSpan = row.querySelector('.totale');
        const removeButton = row.querySelector('.btn-remove');

        if (!quantitaInput || !listinoSelect || !prezzoInput || !totaleSpan || !removeButton) {
            console.error('Errore: Alcuni elementi nella riga non sono stati trovati.');
            return;
        }

        // Aggiorna il prezzo e il totale quando si seleziona una voce dal listino
        listinoSelect.addEventListener('change', function () {
            const selectedOption = listinoSelect.options[listinoSelect.selectedIndex];
            const prezzo = parseFloat(selectedOption.dataset.prezzo) || 0;
            prezzoInput.value = prezzo.toFixed(2);
            updateTotale();
        });
        
        quantitaInput.addEventListener('input', updateTotale);// Aggiorna il totale quando si modifica la quantità
        prezzoInput.addEventListener('input', updateTotale);// Aggiorna il totale quando si modifica il prezzo

        function updateTotale() {
            const quantita = parseFloat(quantitaInput.value) || 0;
            const prezzo = parseFloat(prezzoInput.value) || 0;
            const totale = quantita * prezzo;
            totaleSpan.textContent = `Totale: ${totale.toFixed(2)}`;
            aggiornaTotaleGenerale(); // Aggiorna il totale generale
        }

        // Rimuovi la riga quando si clicca sul pulsante "-"
        removeButton.addEventListener('click', function () {
            row.remove();
            aggiornaTotaleGenerale(); // Aggiorna il totale generale
        });
    }



    //=== APERTURA MODALE- INSERIMENTO / MODIFICA ====================================================================
    if (modalCantiere && formCantiere) { 
        modalCantiere.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget; // Bottone che ha aperto la modale

            if (!button) {
                //console.error("Errore: il pulsante che ha attivato la modale non è definito.");
                return;
            }

            const action = button.getAttribute('data-action'); // "create" o "update"
            const cantiereId = button.getAttribute('data-id') || '';

            // Configura la modale in base all'azione
            if (action === 'update') {
                modalTitle.textContent = 'Modifica Cantiere';
                formCantiere.querySelector('[name="action"]').value = 'update';
                formCantiere.querySelector('[name="id"]').value = cantiereId;

                // Assegna i valori ai campi hidden
                formCantiere.querySelector('[name="id_cliente"]').value = button.getAttribute('data-id_cliente');
                formCantiere.querySelector('[name="id_comune"]').value = button.getAttribute('data-id_comune');
                formCantiere.querySelector('[name="id_provincia"]').value = button.getAttribute('data-id_provincia');
                formCantiere.querySelector('[name="id_regione"]').value = button.getAttribute('data-id_regione');
                formCantiere.querySelector('[name="id_posatore"]').value = button.getAttribute('data-id_posatore');
                formCantiere.querySelector('[name="id_stato_cantiere"]').value = button.getAttribute('data-id_stato');

                // Popola i campi visibili con i nomi
                document.getElementById('cliente').value = button.getAttribute('data-cliente_nome');
                document.getElementById('comune').value = button.getAttribute('data-comune_nome');
                document.getElementById('provincia').value = button.getAttribute('data-provincia_nome');
                document.getElementById('regione').value = button.getAttribute('data-regione_nome');

                // Popola altri campi
                formCantiere.querySelector('[name="indirizzo"]').value = button.getAttribute('data-indirizzo');
                formCantiere.querySelector('[name="note"]').value = button.getAttribute('data-note');
                formCantiere.querySelector('[name="data_inizio"]').value = button.getAttribute('data-data_inizio');
                formCantiere.querySelector('[name="data_fine"]').value = button.getAttribute('data-data_fine');


                // Popola anche le voci del cantiere
                fetch(`index.php?page=cantieri&action=getVoci&id=${cantiereId}`)
                    .then(response => response.json())
                    .then(data => {
                        const container = document.getElementById('azioni-container');
                        container.innerHTML = ''; // Svuota il contenitore

                        if (data.success) {
                            const voci = data.voci;
                            if (voci.length > 0) {
                                voci.forEach(voce => {
                                    const newRow = document.createElement('div');
                                    newRow.classList.add('d-flex', 'align-items-center', 'mb-2', 'voce-row');
                                    newRow.innerHTML = `
                                        <input type="hidden" name="id_voce_cantiere[]" value="${voce.id}"> <!-- ID voce esistente -->
                                        <input type="text" name="quantita[]" class="form-control me-2 quantita" value="${voce.quantita}" placeholder="Quantità" required>
                                        <select name="id_listino[]" class="form-select me-2 listino-select" required>
                                            <option value="" disabled>Seleziona voce</option>
                                            <?php 
                                            $idTipologiaM = 0; // Variabile per tenere traccia dell'ultima tipologia vista
                                            foreach ($listinoData as $listino): 
                                                if ($idTipologiaM != $listino['id_tipologia']): // Controlla se cambia la tipologia
                                            ?>
                                                <option value="" disabled></option>
                                                <option value="" disabled style="font-weight: bold;"><?= htmlspecialchars($listino['tipologia_nome'], ENT_QUOTES, 'UTF-8') ?></option>
                                            <?php 
                                                    $idTipologiaM = $listino['id_tipologia']; // Aggiorna la tipologia corrente
                                                endif; 
                                            ?>
                                                <option value="<?= $listino['id'] ?>" data-prezzo="<?= $listino['prezzo'] ?>" ${voce.id_listino == <?= $listino['id'] ?> ? 'selected' : ''}>
                                                    <?= htmlspecialchars($listino['voce'], ENT_QUOTES, 'UTF-8') ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <input type="text" name="prezzo[]" class="form-control me-2 prezzo" value="${voce.prezzo}" placeholder="Prezzo">
                                        <span class="form-control me-2 totale">Totale: ${(voce.quantita * voce.prezzo).toFixed(2)}</span>
                                        <button type="button" class="btn btn-danger btn-remove ms-2">-</button>
                                    `;
                                    container.appendChild(newRow);
                                    attachEventsToRow(newRow); // Aggiungi eventi alla riga
                                });

                                aggiornaTotaleGenerale(); // Aggiorna il totale generale
                                
                            } else {
                                // Nessuna voce esistente
                                const message = document.createElement('p');
                                message.textContent = 'Nessuna voce trovata. Puoi aggiungerne una.';
                                message.style.color = 'gray';
                                container.appendChild(message);
                            }
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(error => console.error('Errore:', error));

            } else {
                modalTitle.textContent = 'Nuovo Cantiere';
                formCantiere.querySelector('[name="action"]').value = 'create';
                formCantiere.reset(); // Svuota tutti i campi
                const container = document.getElementById('azioni-container');
                container.innerHTML = ''; // Svuota il contenitore delle voci

                // Imposta "Seleziona una Regione" come valore predefinito
                const selectRegione = formCantiere.querySelector('[name="id_regione"]');
                if (selectRegione) {
                    selectRegione.value = ''; // Seleziona l'opzione vuota
                }

                // Resetta anche le province
                const selectProvincia = formCantiere.querySelector('[name="id_provincia"]');
                if (selectProvincia) {
                    selectProvincia.innerHTML = '<option value="">Seleziona una Provincia</option>';
                }
            }
        });



        // Gestione invio del form
        formCantiere.addEventListener('submit', function (event) {
            event.preventDefault();

            const formData = new FormData(formCantiere);

            console.log('Dati inviati:', Object.fromEntries(formData.entries())); // Log per verificare i dati inviati

            fetch('index.php?page=cantieri&action=' + formData.get('action'), {
                method: 'POST',
                body: formData
            })
                .then(response => {
                    if (!response.ok) throw new Error('Errore nella risposta del server');
                    return response.json();
                })
                .then(data => {
                    if (data.success) {

                        //alert(`${formData.get('action') === 'create' ? 'Cantiere creato' : 'Cantiere modificato'} con successo!`);

                        // Chiudi la modale
                        const bootstrapModal = bootstrap.Modal.getInstance(modalCantiere);
                        bootstrapModal.hide();

                        location.reload();
                    } else {
                        alert('Errore: ' + data.message);
                    }
                })
                .catch(error => console.error('Errore:', error));
        });
    }



    //aggiorna il totale dei totali campi dinamici
    function aggiornaTotaleGenerale() {
        const totaleGeneraleElement = document.getElementById('totale-generale');
        const righe = document.querySelectorAll('#azioni-container .voce-row');
        let totaleGenerale = 0;

        righe.forEach(row => {
            const totaleSpan = row.querySelector('.totale');
            if (totaleSpan) {
                const totaleRiga = parseFloat(totaleSpan.textContent.replace('Totale: ', '').replace(',', '.')) || 0;
                totaleGenerale += totaleRiga;
            }
        });

        totaleGeneraleElement.textContent = `Totale Generale: €${totaleGenerale.toFixed(2)}`;
    }



    //=== MOTORE DI RICERCA CLIENTI ====================================================================
    const clienteInput = document.getElementById('cliente'); //viene usato anche sotto da: --> "CHECKBOX DATI UGUALI A QUELLI DEL CLIENTE"
    const clienteList = document.getElementById('cliente-list');
    clienteInput.addEventListener('input', function () {
        const query = clienteInput.value.trim();

        if (query.length >= 2) {
            fetch(`index.php?page=cantieri&ajax=get_clienti&query=${query}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Errore nella risposta del server');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Dati ricevuti:', data); // <-- Aggiungi questo per vedere cosa arriva
                    clienteList.innerHTML = '';
                    clienteList.style.display = 'block';

                    // Controlla che `data` sia un array
                    if (Array.isArray(data)) {
                        data.forEach(cliente => {
                            const li = document.createElement('li');
                            li.className = 'dropdown-item';
                            li.textContent = `${cliente.nome} ${cliente.cognome} - ${cliente.comune} (${cliente.provincia})`;
                            li.dataset.idCliente = cliente.id;

                            li.addEventListener('click', function () {
                                clienteInput.value = `${cliente.nome} ${cliente.cognome}`; // Imposta il valore del campo visibile
                                document.getElementById('id-cliente').value = cliente.id; // Imposta l'ID nel campo nascosto
                                clienteList.style.display = 'none'; // Nasconde il dropdown
                            });

                            clienteList.appendChild(li);
                        });
                    } else {
                        console.error('Errore: La risposta non è un array:', data);
                    }
                })
                .catch(error => {
                    console.error('Errore nella richiesta AJAX:', error);
                });
        } else {
            clienteList.style.display = 'none';
        }
    });
    // Chiudi il dropdown quando si clicca fuori
    document.addEventListener('click', function (event) {
        if (!clienteList.contains(event.target) && event.target !== clienteInput) {
            clienteList.style.display = 'none';
        }
    });



    //=== CHECKBOX DATI UGUALI A QUELLI DEL CLIENTE ==========================================================
    //const clienteInput = document.getElementById('cliente');
    const clienteIdInput = document.getElementById('id-cliente');
    const useClientAddressCheckbox = document.getElementById('use-client-address');

    // Event listener per il checkbox
    useClientAddressCheckbox.addEventListener('change', function () {
        if (this.checked) {
            // Assicurati che un cliente sia selezionato
            const clienteId = clienteIdInput.value;
            if (!clienteId) {
                alert('Seleziona un cliente prima di usare questa opzione.');
                this.checked = false;
                return;
            }

            // Effettua una richiesta per recuperare i dati del cliente
            fetch(`index.php?page=cantieri&ajax=get_cliente_data&id=${clienteId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Popola i campi con i dati del cliente
                        document.getElementById('comune').value = data.cliente.comune_nome || '';
                        document.getElementById('id-comune').value = data.cliente.id_comune || '';
                        document.getElementById('provincia').value = data.cliente.provincia_nome || '';
                        document.getElementById('id-provincia').value = data.cliente.id_provincia || '';
                        document.getElementById('regione').value = data.cliente.regione_nome || '';
                        document.getElementById('id-regione').value = data.cliente.id_regione || '';
                        document.getElementById('indirizzo').value = data.cliente.indirizzo || '';
                    } else {
                        alert('Errore nel recupero dei dati del cliente.');
                        this.checked = false;
                    }
                })
                .catch(error => {
                    console.error('Errore durante il recupero dei dati del cliente:', error);
                    alert('Errore durante il recupero dei dati del cliente.');
                    this.checked = false;
                });
        } else {
            // Se deselezionato, svuota i campi
            document.getElementById('comune').value = '';
            document.getElementById('id-comune').value = '';
            document.getElementById('provincia').value = '';
            document.getElementById('id-provincia').value = '';
            document.getElementById('regione').value = '';
            document.getElementById('id-regione').value = '';
            document.getElementById('indirizzo').value = '';
        }
    });




    //=== MOTORE DI RICERCA COMUNI ====================================================================
    const comuneInput = document.getElementById('comune');
    const comuneList = document.getElementById('comune-list');
    comuneInput.addEventListener('input', function () {
        const query = comuneInput.value.trim();

        if (query.length >= 2) {
            fetch(`index.php?page=cantieri&ajax=get_comuni&query=${query}`)
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
                        li.dataset.idProvincia = comune.id_provincia;
                        li.dataset.idRegione = comune.id_regione;

                        li.addEventListener('click', function () {
                            comuneInput.value = comune.comune; // Imposta il valore del campo comune
                            document.getElementById('id-comune').value = comune.id; // ID comune nascosto
                            comuneList.style.display = 'none';

                            // Invia una richiesta al backend per popolare le province e le regioni
                            fetch(`index.php?page=cantieri&ajax=get_comune_details&id_comune=${comune.id}`)
                            .then(response => response.json())
                            .then(data => {
                                console.log("Dati ricevuti per comune selezionato:", data);

                                // Imposta i valori nei campi nascosti e di testo
                                document.getElementById('id-provincia').value = data.id_provincia; // ID provincia nascosto
                                document.getElementById('id-regione').value = data.id_regione; // ID regione nascosto

                                const provinciaInput = document.getElementById('provincia');
                                const regioneInput = document.getElementById('regione');

                                provinciaInput.value = data.provincia_nome || `Provincia ${data.id_provincia}`; // Fallback in caso di dati mancanti
                                regioneInput.value = data.regione_nome || `Regione ${data.id_regione}`; // Fallback in caso di dati mancanti
                            })
                            .catch(error => console.error('Errore nel caricamento dei dettagli del comune:', error));
                        });


                        comuneList.appendChild(li);
                    });
                })
                .catch(error => {
                    console.error('Errore nella richiesta AJAX:', error);
                });
        } else {
            comuneList.style.display = 'none';
        }
    });
    // Chiudi il dropdown quando si clicca fuori
    document.addEventListener('click', function (event) {
        if (!comuneList.contains(event.target) && event.target !== comuneInput) {
            comuneList.style.display = 'none';
        }
    });

    //=== MOTORE DI RICERCA COMUNI sulla modale Cliente-------
    const nuovoComuneInput = document.getElementById('nuovo-comune');
    const nuovoComuneList = document.getElementById('nuovo-comune-list');
    if (nuovoComuneInput && nuovoComuneList) {
        nuovoComuneInput.addEventListener('input', function () {
            const query = nuovoComuneInput.value.trim();

            if (query.length >= 2) {
                fetch(`index.php?page=clienti&ajax=get_comuni&query=${query}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Errore nella risposta del server');
                        }
                        return response.json();
                    })
                    .then(data => {
                        nuovoComuneList.innerHTML = '';
                        nuovoComuneList.style.display = 'block';

                        data.forEach(comune => {
                            const li = document.createElement('li');
                            li.className = 'dropdown-item';
                            li.textContent = comune.comune;
                            li.dataset.idComune = comune.id;
                            li.dataset.idProvincia = comune.id_provincia;
                            li.dataset.idRegione = comune.id_regione;

                            li.addEventListener('click', function () {
                                nuovoComuneInput.value = comune.comune; // Imposta il valore del campo comune
                                document.getElementById('nuovo-id_comune').value = comune.id; // ID comune nascosto
                                nuovoComuneList.style.display = 'none';

                                // Invia una richiesta al backend per popolare le province e le regioni
                                fetch(`index.php?page=clienti&ajax=get_comune_details&id_comune=${comune.id}`)
                                    .then(response => response.json())
                                    .then(data => {
                                        console.log("Dati ricevuti per comune selezionato:", data);

                                        // Imposta i valori nei campi nascosti e di testo
                                        document.getElementById('nuovo-id_provincia').value = data.id_provincia; // ID provincia nascosto
                                        document.getElementById('nuovo-id_regione').value = data.id_regione; // ID regione nascosto

                                        const provinciaInput = document.getElementById('nuovo-provincia');
                                        const regioneInput = document.getElementById('nuovo-regione');

                                        provinciaInput.value = data.provincia_nome || `Provincia ${data.id_provincia}`; // Fallback in caso di dati mancanti
                                        regioneInput.value = data.regione_nome || `Regione ${data.id_regione}`; // Fallback in caso di dati mancanti
                                    })
                                    .catch(error => console.error('Errore nel caricamento dei dettagli del comune:', error));
                            });

                            nuovoComuneList.appendChild(li);
                        });
                    })
                    .catch(error => console.error('Errore nella richiesta AJAX:', error));
            } else {
                nuovoComuneList.style.display = 'none';
            }
        });

        // Chiudi il dropdown quando si clicca fuori
        document.addEventListener('click', function (event) {
            if (!nuovoComuneList.contains(event.target) && event.target !== nuovoComuneInput) {
                nuovoComuneList.style.display = 'none';
            }
        });
    } else {
        console.error('Elemento #nuovo-comune o #nuovo-comune-list non trovato.');
    }



    //=== GOOGLE ====================================================================
    const modalGoogle = document.getElementById('modalGoogle');
    const googleResultsTable = document.getElementById('googleResultsTable');
    const googleModalMessage = document.getElementById('googleModalMessage');
    const startSearchButton = document.getElementById('startSearch');
    const typesSelect = document.getElementById('types');
    let cantiereId = null;
    let indirizzo = null;

    // Evento di apertura della modale
    modalGoogle.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget; // Bottone che ha aperto la modale
        cantiereId = button.getAttribute('data-id');
        indirizzo = button.getAttribute('data-indirizzo');

        // Assegna l'ID del cantiere al pulsante "Avvia Ricerca"
        const startSearchButton = document.getElementById('startSearch');
        startSearchButton.setAttribute('data-id-cantiere', cantiereId);

        // Reset modale
        googleModalMessage.textContent = '';
        googleResultsTable.classList.add('d-none');
        const tbody = googleResultsTable.querySelector('tbody');
        tbody.innerHTML = ''; // Svuota eventuali risultati precedenti

        // Recupera gli indirizzi salvati
        fetch(`index.php?page=google&ajax=get_saved_addresses&id_cantiere=${cantiereId}`)
            .then(response => response.json())
            .then(data => {
                const savedTbody = document.querySelector('#savedAddressesTable tbody');
                savedTbody.innerHTML = ''; // Svuota la tabella degli indirizzi salvati

                if (data.success && data.addresses.length > 0) {
                    data.addresses.forEach(address => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${address.nome}</td>
                            <td>${address.indirizzo}</td>
                            <td>${address.distanza}</td>
                            <td>
                                <button class="btn btn-danger btn-sm delete-saved-address" data-id-cantiere="${cantiereId}" data-address="${address.indirizzo}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        `;
                        savedTbody.appendChild(row);
                    });
                } else {
                    savedTbody.innerHTML = `<tr><td colspan="4" class="text-center">Nessun indirizzo salvato.</td></tr>`;
                }
            })
            .catch(error => {
                console.error('Errore nel recupero degli indirizzi salvati:', error);
            });
    });


    // Evento delegato per il pulsante di eliminazione
    $(document).on('click', '.delete-saved-address', function () {
        const idCantiere = $(this).data('id-cantiere');
        const address = $(this).data('address');

        if (!idCantiere || !address) {
            console.error('Dati mancanti: idCantiere o address');
            alert('Errore: dati mancanti per l\'eliminazione.');
            return;
        }

        // Conferma eliminazione
        if (!confirm(`Sei sicuro di voler eliminare l'indirizzo: "${address}"?`)) {
            return;
        }

        // Effettua la richiesta AJAX per eliminare l'indirizzo
        $.ajax({
            url: 'index.php',
            type: 'GET',
            data: {
                page: 'google',
                ajax: 'delete_address',
                id_cantiere: idCantiere,
                address: address
            },
            success: function (response) {
                if (response.success) {
                    //console.log('Indirizzo eliminato:', response.message);
                    //alert(response.message);
                    // Rimuovi la riga dalla tabella
                    $(this).closest('tr').remove();
                } else {
                    console.error('Errore:', response.message);
                    alert('Errore: ' + response.message);
                }
            }.bind(this), // Assicura il contesto corretto per `this`
            error: function (xhr, status, error) {
                console.error('Errore AJAX:', error);
                console.error('Dettagli risposta:', xhr.responseText);
                alert('Errore nella comunicazione con il server.');
            }
        });
    });



    // Funzione per formattare la distanza
    /*
    function formatDistance(distance) {
        if (!distance || distance === 'N/A') return 'N/A';
        const distKm = parseFloat(distance);
        return distKm < 1 ? `${Math.round(distKm * 1000)} m` : `${distKm.toFixed(2)} km`;
    }
    */

    // Evento di clic sul pulsante "Avvia Ricerca"
    startSearchButton.addEventListener('click', function () {
        const selectedOption = typesSelect.value; // Ottieni l'opzione selezionata
        let type = '';
        let keyword = '';

        if (!selectedOption) {
            googleModalMessage.textContent = 'Per favore, seleziona un tipo di esercizio.';
            return;
        }

        if (selectedOption.includes('-')) {
            // Se contiene un trattino, separa type e keyword
            [type, keyword] = selectedOption.split('-');
        } else {
            // Altrimenti è solo un type
            type = selectedOption;
        }

        // Recupera l'ID del cantiere dal pulsante
        const cantiereId = startSearchButton.getAttribute('data-id-cantiere');
        if (!cantiereId) {
            googleModalMessage.textContent = 'Errore: ID cantiere non trovato.';
            return;
        }

        console.log('Type:', type);
        console.log('Keyword:', keyword);
        console.log('Cantiere ID:', cantiereId);


        // Mostra messaggio di caricamento
        googleModalMessage.textContent = 'Caricamento risultati...';
        googleResultsTable.classList.add('d-none');

        // Costruisci i parametri per la chiamata API
        const params = new URLSearchParams({
            page: 'google',
            ajax: 'get_google_results',
            type: type,
            keyword: keyword || '', // Aggiungi solo se esiste
            cantiere_id: cantiereId,
            indirizzo: indirizzo
        });

        // Richiedi i risultati da Google Places
        fetch(`index.php?${params.toString()}`)
            .then(response => response.json())
            .then(data => {
                const tbody = googleResultsTable.querySelector('tbody');
                tbody.innerHTML = ''; // Svuota eventuali risultati precedenti

                if (data.success) {
                    data.results.forEach(result => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${result.name}</td>
                            <td>${result.address}</td>
                            <td>${result.distance}</td>
                            <td>
                                <!-- Checkbox per memorizzare -->
                                <input type="checkbox" class="save-address-checkbox" 
                                    data-id-cantiere="${cantiereId}" 
                                    data-address="${result.address}" 
                                    data-name="${result.name}" 
                                    data-distance="${result.distance}" 
                                    data-lat="${result.latitude}" 
                                    data-lng="${result.longitude}">
                            </td>
                            <td>
                                <!-- Pulsante Chiama -->
                                <a href="tel:${result.phone || ''}" class="btn btn-sm btn-primary" title="Chiama">
                                    <i class="bi bi-telephone-fill"></i>
                                </a>
                                <!-- Pulsante Sito -->
                                <a href="${result.website || '#'}" target="_blank" class="btn btn-sm btn-secondary" title="Sito">
                                    <i class="bi bi-globe"></i>
                                </a>
                                <!-- Pulsante Indicazioni -->
                                <a href="https://www.google.com/maps/dir/?api=1&destination=${result.latitude},${result.longitude}" target="_blank" class="btn btn-sm btn-info" title="Indicazioni">
                                    <i class="bi bi-geo-alt-fill"></i>
                                </a>
                            </td>
                        `;
                        tbody.appendChild(row);
                    });

                    // Mostra i risultati
                    googleResultsTable.classList.remove('d-none');
                    googleModalMessage.textContent = '';
                } else {
                    googleModalMessage.textContent = data.message || 'Errore nel caricamento dei risultati.';
                }
            })
            .catch(error => {
                googleModalMessage.textContent = 'Errore durante il caricamento dei risultati.';
                console.error(error);
            });
    });

    //Evento che salva l'indirizzo al click nel Checkbox
    $(document).on('click', '.save-address-checkbox', function () {
        const idCantiere = $(this).data('id-cantiere'); // ID del cantiere
        const name = $(this).data('name'); // Nome del luogo
        const address = $(this).data('address'); // Indirizzo
        const distance = $(this).data('distance'); // Distanza
        const latitude = $(this).data('lat'); // Latitudine
        const longitude = $(this).data('lng'); // Longitudine

        const isChecked = $(this).is(':checked'); // Stato del checkbox

        if (!idCantiere || !address) {
            console.error('Dati mancanti: idCantiere o address');
            alert('Errore: dati mancanti per il salvataggio.');
            return;
        }

        // Costruisci i parametri per la richiesta GET
        const params = new URLSearchParams({
            ajax: isChecked ? 'save_address' : 'delete_address',
            id_cantiere: idCantiere,
            name: name,
            address: address,
            distance: distance,
            lat: latitude,
            lng: longitude,
        });

        // Effettua la richiesta AJAX
        $.ajax({
            url: `index.php?page=google&${params.toString()}`, // Aggiungi i parametri alla URL
            type: 'GET',
            success: function (response) {
                if (response.success) {
                    console.log('Operazione completata:', response.message);
                    //alert(response.message);
                } else {
                    console.error('Errore:', response.message);
                    alert('Errore: ' + response.message);
                }
            },
            error: function (xhr, status, error) {
                console.error('Errore AJAX:', error);
                console.error('Dettagli risposta:', xhr.responseText);
                alert('Errore nella comunicazione con il server.');
            }
        });
    });



    //=== ELIMINA CANTIERE ====================================================================
    let cantiereIdToDelete = null;
    // Quando la modale di eliminazione viene mostrata
    modalElimina.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget; // Bottone che ha attivato la modale
        cantiereIdToDelete = button.getAttribute('data-id'); // ID del cantiere da eliminare
    });
    // Quando viene cliccato il pulsante di conferma
    document.getElementById('confirmDelete').addEventListener('click', function () {
        if (!cantiereIdToDelete) return;

        // Invia richiesta POST per eliminare il cantiere
        fetch('index.php?page=cantieri', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'delete',
                id: cantiereIdToDelete,
            }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                //alert(data.message);
                location.reload(); // Ricarica la pagina
            } else {
                alert('Errore: ' + data.message);
            }
        })
        .catch(error => console.error('Errore:', error));
    });



    //=== DETTAGLI CANTIERE ====================================================================
    document.querySelectorAll('.btn-dettagli').forEach(button => {
        button.addEventListener('click', function () {
            const cantiereId = this.getAttribute('data-id');
            fetch(`index.php?page=cantieri&action=getDettagli&id=${cantiereId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const dettagli = data.dettagli.cantiere;
                        const voci = data.dettagli.voci;

                        // Popola i campi dei dettagli
                        document.getElementById('dettagli-cliente').textContent = `${dettagli.cliente_nome} ${dettagli.cliente_cognome}`;
                        document.getElementById('dettagli-posatore').textContent = `${dettagli.posatore_nome} ${dettagli.posatore_cognome}`;
                        document.getElementById('dettagli-regione').textContent = dettagli.regione_nome;
                        document.getElementById('dettagli-provincia').textContent = dettagli.provincia_nome;
                        document.getElementById('dettagli-comune').textContent = dettagli.comune_nome; // Aggiungi il comune
                        document.getElementById('dettagli-stato').textContent = dettagli.stato_cantiere_nome;
                        document.getElementById('dettagli-indirizzo').textContent = dettagli.indirizzo;
                        document.getElementById('dettagli-note').textContent = dettagli.note;
                        document.getElementById('dettagli-data-inizio').textContent = dettagli.data_inizio;
                        document.getElementById('dettagli-data-fine').textContent = dettagli.data_fine;

                        // Gestisci le voci
                        const vociContainer = document.getElementById('dettagli-voci');
                        vociContainer.innerHTML = '';
                        let totale = 0;

                        voci.forEach(voce => {
                            const prezzo = parseFloat(voce.prezzo);
                            const quantita = parseInt(voce.quantita);
                            const subtotale = prezzo * quantita;
                            totale += subtotale;

                            vociContainer.innerHTML += `
                                <tr>
                                    <td>${voce.nome_voce}</td>
                                    <td>${quantita}</td>
                                    <td>${prezzo.toFixed(2)}</td>
                                    <td>${subtotale.toFixed(2)}</td>
                                </tr>
                            `;
                        });

                        document.getElementById('dettagli-totale').textContent = totale.toFixed(2);

                        // Mostra la modale
                        const bootstrapModal = new bootstrap.Modal(modalDettagli);
                        bootstrapModal.show();
                    } else {
                        alert(data.message);
                    }
                })
            .catch(error => console.error('Errore nel recupero dei dettagli:', error));
        });
    });



    //=== ALLEGATI / PHOTOGALLERY ==============================================================
    const modalAllegatiFotogallery = document.getElementById('modalAllegatiFotogallery');
    const caricaAllegatiButton = document.getElementById('caricaAllegati');
    const caricaFotogalleryButton = document.getElementById('caricaFotogallery');

    // Quando si apre la modale per gli Allegati e la Fotogallery
    modalAllegatiFotogallery.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget; // Bottone che ha attivato la modale
        const idCantiere = button.getAttribute('data-id'); // ID del cantiere
        document.getElementById('allegati-id-cantiere').value = idCantiere;
        document.getElementById('fotogallery-id-cantiere').value = idCantiere;

        // Carica gli allegati esistenti
        fetch(`backend/upload_handler.php?action=get_files&type=allegati&id_cantiere=${idCantiere}`)
            .then(response => response.json())
            .then(data => {
                const allegatiTable = document.querySelector('#allegatiTable tbody');
                allegatiTable.innerHTML = '';
                if (data.success) {
                    data.files.forEach(allegato => {
                        allegatiTable.innerHTML += `
                            <tr>
                                <td><a href="allegati/${idCantiere}/${allegato.nome_file}" target="_blank">${allegato.nome_file}</a></td>
                                <td>
                                    <button type="button" 
                                            class="btn btn-danger btn-sm delete-allegato" 
                                            data-id="${allegato.id}" 
                                            data-file-path="allegati/${idCantiere}/${allegato.nome_file}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>`;
                    });
                } else {
                    alert(data.message || 'Errore durante il caricamento degli allegati');
                }
            })
            .catch(error => console.error('Errore durante il recupero degli allegati:', error));


        // Carica le immagini della fotogallery
        fetch(`backend/upload_handler.php?action=get_files&type=fotogallery&id_cantiere=${idCantiere}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Errore nella risposta del server');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    const fotogalleryGrid = document.getElementById('fotogalleryGrid');
                    fotogalleryGrid.innerHTML = '';
                    data.files.forEach(foto => {
                        fotogalleryGrid.innerHTML += `
                            <div class="col-4 position-relative">
                                <img src="photogallery/${idCantiere}/thumbnail/${foto.nome_file}" 
                                    class="img-thumbnail fotogallery-image" 
                                    data-src="photogallery/${idCantiere}/img/${foto.nome_file}" 
                                    alt="Immagine">
                                <!-- Icona del cestino -->
                                <button type="button" 
                                        class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2 delete-image"
                                        data-id="${foto.id}" 
                                        data-file-path="photogallery/${idCantiere}/img/${foto.nome_file}">
                                    <i class="bi bi-trash"></i> <!-- Icona Bootstrap Trash -->
                                </button>
                            </div>
                        `;
                    });
                } else {
                    alert(data.message || 'Errore durante il caricamento della fotogallery');
                }
            })
            .catch(error => console.error('Errore durante il recupero della fotogallery:', error));
    });

    // Al caricamento degli allegati
    caricaAllegatiButton.addEventListener('click', function () {
        const formData = new FormData(document.getElementById('allegatiForm'));
        formData.append('type', 'allegati'); // Specifica il tipo come "allegati"
        fetch('backend/upload_handler.php?action=upload_files', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    //alert('Allegati caricati con successo!');
                    location.reload();
                } else {
                    alert('Errore: ' + data.message);
                }
            })
            .catch(error => console.error('Errore:', error));
    });

    // Al caricamento della fotogallery
    caricaFotogalleryButton.addEventListener('click', function () {
        const formData = new FormData(document.getElementById('fotogalleryForm'));
        formData.append('type', 'fotogallery'); // Specifica il tipo come "fotogallery"
        fetch('backend/upload_handler.php?action=upload_files', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    //alert('Fotogallery caricata con successo!');
                    location.reload();
                } else {
                    alert('Errore: ' + data.message);
                }
            });
    });

    // Individua tutte le immagini nella fotogallery
    const fotogalleryImages = document.querySelectorAll('.fotogallery-image');

    // Assegna l'evento di clic ad ogni immagine
    const fotogalleryGrid = document.getElementById('fotogalleryGrid');
    fotogalleryGrid.addEventListener('click', function (event) {
        const img = event.target;
        if (img.tagName === 'IMG' && img.classList.contains('fotogallery-image')) {
            event.preventDefault();

            // Ottieni il valore del "data-src" (URL immagine grande)
            const imageUrl = img.getAttribute('data-src');

            console.log("Immagine cliccata:", imageUrl); // Log per debug

            // Imposta l'immagine grande nella modale
            const imagePreview = document.getElementById('imagePreview');
            if (imagePreview) {
                imagePreview.setAttribute('src', imageUrl);

                // Mostra la modale con l'immagine grande
                const imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
                imageModal.show();
            } else {
                console.error('Errore: Elemento "imagePreview" non trovato.');
            }
        }
    });

    //Elimina gli allegati
    document.getElementById('allegatiTable').addEventListener('click', function (event) {
        if (event.target && event.target.closest('.delete-allegato')) {
            const deleteButton = event.target.closest('.delete-allegato');
            const allegatoId = deleteButton.getAttribute('data-id');
            const filePath = deleteButton.getAttribute('data-file-path');

            if (confirm('Sei sicuro di voler eliminare questo allegato?')) {
                fetch('backend/upload_handler.php?action=delete_allegato', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        id: allegatoId,
                        file_path: filePath,
                    }),
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            //alert('Allegato eliminato con successo!');
                            deleteButton.closest('tr').remove(); // Rimuove la riga dalla tabella
                        } else {
                            alert('Errore durante l\'eliminazione: ' + data.message);
                        }
                    })
                    .catch(error => console.error('Errore:', error));
            }
        }
    });

    //Elimina le immagini 
    document.getElementById('fotogalleryGrid').addEventListener('click', function (event) {
        if (event.target && event.target.closest('.delete-image')) {
            // Recupera i dati relativi all'immagine
            const deleteButton = event.target.closest('.delete-image');
            const imageId = deleteButton.getAttribute('data-id');
            const filePath = deleteButton.getAttribute('data-file-path');

            // Conferma eliminazione
            if (confirm('Sei sicuro di voler eliminare questa immagine?')) {
                // Invio richiesta al server
                fetch('backend/upload_handler.php?action=delete_image', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        id: imageId,
                        file_path: filePath,
                    }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        //alert('Immagine eliminata con successo!');
                        deleteButton.closest('.col-4').remove(); // Rimuove l'immagine dalla UI
                    } else {
                        alert('Errore durante l\'eliminazione: ' + data.message);
                    }
                })
                .catch(error => console.error('Errore:', error));
            }
        }
    });




    //=== STAMPA / SALVA PDF ==================================================================
    document.getElementById('btnStampa').addEventListener('click', function () {
        // Ottieni il contenuto della modale
        const modalContent = document.querySelector('#modalDettagli .modal-content').innerHTML;

        // Crea una nuova finestra temporanea
        const printWindow = window.open('', '_blank', 'width=800,height=600,scrollbars=yes');

        // Scrivi il contenuto della modale nella nuova finestra
        printWindow.document.open();
        printWindow.document.write(`
            <html>
                <head>
                    <title>Dettagli Cantiere</title>
                    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
                    <style>
                        /* Aggiungi eventuali stili personalizzati per la stampa */
                        body { margin: 20px; }
                        .table { width: 100%; }
                    </style>
                </head>
                <body>
                    ${modalContent}
                </body>
            </html>
        `);
        printWindow.document.close();

        // Attendi il caricamento della finestra e ridimensionala
        printWindow.onload = function () {
            // Calcola le dimensioni del contenuto
            const contentHeight = printWindow.document.body.scrollHeight;
            const contentWidth = printWindow.document.body.scrollWidth;

            // Ridimensiona la finestra
            printWindow.resizeTo(contentWidth + 50, contentHeight + 50);

            // Avvia la stampa
            printWindow.print();

            // Chiudi la finestra dopo la stampa
            printWindow.close();
        };
    });

    btnSalvaPDF.addEventListener('click', function () {
        alert("Funzionalità PDF non implementata. Usa una libreria come jsPDF.");
    });


    //Stampa preventivo
    document.querySelectorAll('.btn-stampa-preventivo').forEach(button => {
        button.addEventListener('click', function () {
            const cantiereId = this.getAttribute('data-id');

            // Reindirizza alla pagina che genera il PDF
            window.open(`index.php?page=stampa_preventivo&id=${cantiereId}`, '_blank');
        });
    });
});
</script>


<script>
document.addEventListener('DOMContentLoaded', () => {
    let modalCantiereInstance = null; // Per memorizzare l'istanza della modale Cantiere

    // Aggiungi un evento per catturare l'apertura della modale Cantiere
    const modalCantiere = document.getElementById('modalCantiere');
    modalCantiere.addEventListener('show.bs.modal', function () {
        modalCantiereInstance = bootstrap.Modal.getInstance(modalCantiere); // Memorizza l'istanza
    });

    // Aggiungi un evento per la modale "Nuovo Cliente"
    const modalNuovoCliente = document.getElementById('modalNuovoCliente');
    modalNuovoCliente.addEventListener('hidden.bs.modal', function () {

        // Resetta i campi cliente nella modale Cantiere
        const clienteInput = document.getElementById('cliente');
        const idClienteInput = document.getElementById('id-cliente');
        if (clienteInput) clienteInput.value = ''; // Pulisce il campo visibile
        if (idClienteInput) idClienteInput.value = ''; // Pulisce il campo ID nascosto

        if (modalCantiereInstance) {
            const cantiereId = document.querySelector('#modalCantiere [name="id"]').value; // Recupera l'ID del cantiere
            if (cantiereId) {
                // Effettua la chiamata AJAX per recuperare i dati del cantiere
                fetch(`index.php?page=cantieri&action=getDettagli&id=${cantiereId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const dettagli = data.dettagli.cantiere;

                            // Popola i campi della modale Cantiere
                            document.getElementById('cliente').value = `${dettagli.cliente_nome} ${dettagli.cliente_cognome}`;
                            document.getElementById('id-cliente').value = dettagli.id_cliente;
                            document.getElementById('comune').value = dettagli.comune_nome;
                            document.getElementById('id-comune').value = dettagli.id_comune;
                            document.getElementById('provincia').value = dettagli.provincia_nome;
                            document.getElementById('id-provincia').value = dettagli.id_provincia;
                            document.getElementById('regione').value = dettagli.regione_nome;
                            document.getElementById('id-regione').value = dettagli.id_regione;
                            document.getElementById('indirizzo').value = dettagli.indirizzo;
                            document.getElementById('note').value = dettagli.note;
                            document.getElementById('data-inizio').value = dettagli.data_inizio;
                            document.getElementById('data-fine').value = dettagli.data_fine;

                            // Popola le voci del cantiere
                            const container = document.getElementById('azioni-container');
                            container.innerHTML = ''; // Svuota il contenitore
                            data.dettagli.voci.forEach(voce => {
                                const newRow = document.createElement('div');
                                newRow.classList.add('d-flex', 'align-items-center', 'mb-2', 'voce-row');
                                newRow.innerHTML = `
                                    <input type="hidden" name="id_voce_cantiere[]" value="${voce.id}">
                                    <input type="text" name="quantita[]" class="form-control me-2 quantita" value="${voce.quantita}" placeholder="Quantità" required>
                                    <select name="id_listino[]" class="form-select me-2 listino-select" required>
                                        <option value="" disabled>Seleziona voce</option>
                                        <option value="${voce.id_listino}" selected>${voce.nome_voce}</option>
                                    </select>
                                    <input type="text" name="prezzo[]" class="form-control me-2 prezzo" value="${voce.prezzo}" placeholder="Prezzo">
                                    <span class="form-control me-2 totale">Totale: ${(voce.quantita * voce.prezzo).toFixed(2)}</span>
                                    <button type="button" class="btn btn-danger btn-remove ms-2">-</button>
                                `;
                                container.appendChild(newRow);
                            });

                            modalCantiereInstance.show(); // Riapri la modale Cantiere
                        } else {
                            alert(data.message || 'Errore nel recupero dei dettagli del cantiere.');
                        }
                    })
                    .catch(error => console.error('Errore durante il caricamento dei dati del cantiere:', error));
            } else {
                modalCantiereInstance.show(); // Riapri la modale Cantiere senza aggiornamenti
            }
        }
    });

    // Salvataggio del nuovo cliente
    const nuovoClienteForm = document.querySelector('#modalNuovoCliente form');
    nuovoClienteForm.addEventListener('submit', function (event) {
        event.preventDefault();

        const formData = new FormData(this);

        fetch('index.php?page=cantieri&action=create', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const nuovoClienteNome = `${formData.get('nome')} ${formData.get('cognome')}`;
                    document.getElementById('cliente').value = nuovoClienteNome;
                    document.getElementById('id-cliente').value = data.id_cliente;
                    const modal = bootstrap.Modal.getInstance(modalNuovoCliente);
                    modal.hide(); // Chiudi la modale "Nuovo Cliente"
                } else {
                    alert('Errore: ' + data.message);
                }
            })
            .catch(error => console.error('Errore:', error));
    });




    //=== MATERIALI CANTIERE ================================================================
    const modalElement = document.getElementById("modalMateriali");

    // Evento per inizializzare la logica solo quando la modale viene aperta
    modalElement.addEventListener("show.bs.modal", (event) => {
        const materialiContainer = document.getElementById("materiali-container");
        const addMaterialRowButton = document.getElementById("add-material-row");
        const saveButton = document.getElementById("salva-materiali");

        // Funzione per caricare i materiali del cantiere
        const loadMaterials = (idCantiere) => {
            fetch(`index.php?page=materiali_cantiere&ajax=load&id_cantiere=${idCantiere}`)
                .then((response) => response.json())
                .then((data) => {
                    materialiContainer.innerHTML = ""; // Svuota il contenitore

                    data.forEach((materiale) => {
                        addMaterialRow(materiale.id, materiale.nome, materiale.quantita, materiale.prezzo);
                    });

                    calculateTotal(); // Calcola il totale dopo aver caricato i materiali
                })
                .catch((error) => console.error("Errore durante il caricamento dei materiali:", error));
        };

        // Aggiunge una nuova riga alla griglia
        const addMaterialRow = (id = null, nome = "", quantita = 1, prezzo = 0) => {
            const row = document.createElement("div");
            row.classList.add("d-flex", "align-items-center", "mb-2", "material-row");
            row.innerHTML = `
                <input type="hidden" name="materiale_id[]" value="${id || ""}">
                <input type="text" name="materiale_nome[]" class="form-control me-2 search-materiale" 
                    value="${nome}" placeholder="Nome Materiale" autocomplete="off" data-prezzo="${prezzo}" required>
                <input type="number" name="materiale_quantita[]" class="form-control me-2 materiale-quantita" 
                    value="${quantita}" min="1" required>
                <button type="button" class="btn btn-sm btn-danger btn-remove-material">
                    <i class="bi bi-trash"></i>
                </button>
            `;
            materialiContainer.appendChild(row);
            attachDropdown(row.querySelector(".search-materiale")); // Aggiungi il dropdown dinamico
            attachRowEvents(row); // Aggiungi gli eventi alla riga
        };

        // Gestisce la dropdown di ricerca dinamica per un input
        const attachDropdown = (input) => {
            const dropdown = document.createElement("ul");
            dropdown.classList.add("dropdown-menu");
            dropdown.style.position = "absolute";
            dropdown.style.zIndex = "1050";
            dropdown.style.width = "100%";
            dropdown.style.display = "none";
            input.parentNode.appendChild(dropdown);

            input.addEventListener("input", () => {
                const query = input.value.trim();

                if (query.length >= 2) {
                    fetch(`index.php?page=materiali_cantiere&ajax=search&query=${query}`)
                        .then((response) => response.json())
                        .then((data) => {
                            dropdown.innerHTML = ""; // Svuota la lista
                            dropdown.style.display = "block"; // Mostra la dropdown

                            data.forEach((materiale) => {
                                const li = document.createElement("li");
                                li.classList.add("dropdown-item");
                                li.textContent = materiale.nome;
                                li.dataset.id = materiale.id;

                                li.addEventListener("click", () => {
                                    input.value = materiale.nome; // Assegna il valore all'input
                                    input.previousElementSibling.value = materiale.id; // Aggiorna il campo nascosto
                                    input.dataset.prezzo = materiale.prezzo; // Memorizza il prezzo nel dataset
                                    dropdown.style.display = "none"; // Nasconde la dropdown

                                    calculateTotal(); // Ricalcola il totale
                                });

                                dropdown.appendChild(li);
                            });
                        })
                        .catch((error) => console.error("Errore durante la ricerca:", error));
                } else {
                    dropdown.style.display = "none"; // Nasconde la dropdown
                }
            });

            // Nascondi la dropdown quando si clicca fuori
            document.addEventListener("click", (event) => {
                if (!dropdown.contains(event.target) && event.target !== input) {
                    dropdown.style.display = "none";
                }
            });
        };

        // Gestisce gli eventi della riga
        const attachRowEvents = (row) => {
            const materialeInput = row.querySelector(".search-materiale");
            const quantitaInput = row.querySelector(".materiale-quantita");
            const removeButton = row.querySelector(".btn-remove-material");

            // Imposta lo stato iniziale del pulsante elimina
            removeButton.disabled = !materialeInput.value.trim();

            // Abilita/disabilita il pulsante elimina in base al contenuto dell'input
            materialeInput.addEventListener("input", () => {
                removeButton.disabled = !materialeInput.value.trim();
            });

            // Gestisci la modifica della quantità
            quantitaInput.addEventListener("input", () => {
                const idMateriale = row.querySelector('input[name="materiale_id[]"]').value;
                const nuovaQuantita = parseInt(quantitaInput.value, 10);
                if (nuovaQuantita > 0) {
                    updateMaterialQuantity(idMateriale, nuovaQuantita);
                    calculateTotal(); // Ricalcola il totale
                } else {
                    alert("La quantità deve essere maggiore di zero.");
                    quantitaInput.value = 1;
                }
            });

            // Elimina riga
            removeButton.addEventListener("click", () => {
                const idMateriale = row.querySelector('input[name="materiale_id[]"]').value;
                if (confirm("Sei sicuro di voler eliminare questo materiale?")) {
                    deleteMaterial(idMateriale, row);
                }
            });
        };

        // Calcola il totale dei materiali
        const calculateTotal = () => {
            const rows = document.querySelectorAll("#materiali-container .material-row");
            let totale = 0;

            rows.forEach((row) => {
                const quantita = parseFloat(row.querySelector(".materiale-quantita").value) || 0;
                const prezzo = parseFloat(row.querySelector(".search-materiale").dataset.prezzo || 0); // Prezzo memorizzato nel dataset
                totale += quantita * prezzo;
            });

            // Aggiorna il totale nel DOM
            const totaleElement = document.getElementById("totale-materiali");
            totaleElement.textContent = `Totale Materiali: €${totale.toFixed(2)}`;
        };

        // Carica i materiali quando la modale si apre
        const button = event.relatedTarget;

        if (button && button.dataset.id) {
            const idCantiere = button.dataset.id;
            loadMaterials(idCantiere);
        } else {
            console.error("Errore: il pulsante che ha aperto la modale non contiene un data-id valido.");
        }
    });

});
</script>






<?php
$content = ob_get_clean();
include 'layout.php';
?>
