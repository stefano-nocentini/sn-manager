<div class="container mt-4">
    <h2 class="mb-4">Genera Preventivo</h2>

    <form method="GET" action="index.php">
        <input type="hidden" name="page" value="stampa_preventivo">
        
        <div class="mb-3">
            <label for="cliente" class="form-label">Cliente:</label>
            <input type="text" name="cliente" id="cliente" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="data" class="form-label">Data:</label>
            <input type="date" name="data" id="data" class="form-control" value="<?= date('Y-m-d') ?>" required>
        </div>

        <h4>Prodotti</h4>
        <div id="prodotti-container">
            <div class="row mb-2">
                <div class="col-md-4">
                    <input type="text" name="prodotti[0][descrizione]" class="form-control" placeholder="Descrizione" required>
                </div>
                <div class="col-md-2">
                    <input type="number" name="prodotti[0][quantita]" class="form-control" placeholder="Quantità" required>
                </div>
                <div class="col-md-2">
                    <input type="number" step="0.01" name="prodotti[0][prezzo]" class="form-control" placeholder="Prezzo" required>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger remove-product">Rimuovi</button>
                </div>
            </div>
        </div>

        <button type="button" id="add-product" class="btn btn-success">Aggiungi Prodotto</button>
        <br><br>

        <button type="submit" class="btn btn-primary">Genera PDF</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    let productIndex = 1;

    document.getElementById('add-product').addEventListener('click', () => {
        const container = document.getElementById('prodotti-container');
        const div = document.createElement('div');
        div.classList.add('row', 'mb-2');

        div.innerHTML = `
            <div class="col-md-4">
                <input type="text" name="prodotti[${productIndex}][descrizione]" class="form-control" placeholder="Descrizione" required>
            </div>
            <div class="col-md-2">
                <input type="number" name="prodotti[${productIndex}][quantita]" class="form-control" placeholder="Quantità" required>
            </div>
            <div class="col-md-2">
                <input type="number" step="0.01" name="prodotti[${productIndex}][prezzo]" class="form-control" placeholder="Prezzo" required>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger remove-product">Rimuovi</button>
            </div>
        `;

        container.appendChild(div);
        productIndex++;
    });

    document.getElementById('prodotti-container').addEventListener('click', (event) => {
        if (event.target.classList.contains('remove-product')) {
            event.target.closest('.row').remove();
        }
    });
});
</script>
