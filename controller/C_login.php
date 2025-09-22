<?php
session_start();
require_once '../model/config.php';
require_once '../model/function.php';

$error = '';

if (isset($_GET['logout'])) {
    logout();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $identifiant = $_POST['identifiant'] ?? '';
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';

    if (!empty($identifiant) && !empty($mot_de_passe)) {
        $resultat = login($identifiant, $mot_de_passe);
        if ($resultat) {
            $_SESSION['user_id'] = $resultat['id'];
            $_SESSION['user_role'] = $resultat['role'];
            $_SESSION['user_team_id'] = $resultat['team_id'];

            switch ($resultat['role']) {
                case 'admin':
                    header('Location: C_admin.php');
                    break;
                case 'user':
                    header('Location: C_user.php');
                    break;
                case 'organisateur':
                    header('Location: C_orga.php');
                    break;
                default:
                    header('Location: C_login.php?error=1');
            }
            exit();
        } else {
            $error = "Identifiants incorrects";
        }
    } else {
        $error = "Veuillez remplir tous les champs";
    }
}

require_once '../view/login.php';
?>