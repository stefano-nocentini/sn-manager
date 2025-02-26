<?php
// Contenuto della pagina login
$errorMsg = isset($error) ? "<div class='alert alert-danger'>{$error}</div>" : '';

$content = <<<HTML
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <h2 class="text-center mb-4">Login</h2>
            $errorMsg
            <form method="POST" action="index.php?page=login" class="card p-4 shadow">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="Inserisci la tua email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Inserisci la tua password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
        </div>
    </div>
</div>
HTML;

include 'layout.php';
?>
