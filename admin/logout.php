<?php
require_once 'config.php';

// Log de déconnexion
if (isset($_SESSION['admin_id'])) {
    log_admin_action('LOGOUT', null, 'Déconnexion');
}

// Détruire la session
session_destroy();

// Rediriger vers la page de connexion
redirect('login.php');
?>