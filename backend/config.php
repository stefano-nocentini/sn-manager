<?php
// Direttive per sessioni sicure
ini_set('session.cookie_secure', 1); // Usa cookie sicuri (solo HTTPS)
ini_set('session.cookie_httponly', 1); // Impedisci l'accesso ai cookie tramite JavaScript
ini_set('session.use_only_cookies', 1); // Evita sessioni tramite URL

// Avvio della sessione
session_start();
