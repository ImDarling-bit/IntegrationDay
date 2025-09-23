<?php
session_start();
require_once '../model/config.php';
require_once '../model/function.php';

requireRole('admin');

$message = '';
$teams = getAllTeams();

// Gestion des actions utilisateurs si on est dans la vue mod_user
if (isset($_GET['view']) && $_GET['view'] === 'mod_user' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update') {
        $userId = $_POST['user_id'] ?? '';
        $identifiant = $_POST['identifiant'] ?? '';
        $motDePasse = $_POST['mot_de_passe'] ?? '';
        $roleId = $_POST['role_id'] ?? '';
        $teamId = $_POST['team_id'] ?? null;

        if (!empty($userId) && !empty($identifiant) && !empty($motDePasse) && !empty($roleId)) {
            if ($teamId === '') $teamId = null;

            if (updateUser($userId, $identifiant, $motDePasse, $roleId, $teamId)) {
                $message = "Utilisateur modifié avec succès !";
            } else {
                $message = "Erreur lors de la modification de l'utilisateur.";
            }
        } else {
            $message = "Veuillez remplir tous les champs obligatoires.";
        }
    }

    if ($action === 'add') {
        $identifiant = $_POST['identifiant'] ?? '';
        $motDePasse = $_POST['mot_de_passe'] ?? '';
        $roleId = $_POST['role_id'] ?? '';
        $teamId = $_POST['team_id'] ?? null;

        if (!empty($identifiant) && !empty($motDePasse) && !empty($roleId)) {
            if ($teamId === '') $teamId = null;

            if (addUser($identifiant, $motDePasse, $roleId, $teamId)) {
                $message = "Utilisateur ajouté avec succès !";
            } else {
                $message = "Erreur lors de l'ajout de l'utilisateur.";
            }
        } else {
            $message = "Veuillez remplir tous les champs obligatoires.";
        }
    }

    if ($action === 'delete') {
        $userId = $_POST['user_id'] ?? '';

        if (!empty($userId)) {
            if (deleteUser($userId)) {
                $message = "Utilisateur supprimé avec succès !";
            } else {
                $message = "Erreur lors de la suppression de l'utilisateur.";
            }
        }
    }
}

// Gestion des actions équipes si on est dans la vue mod_team
if (isset($_GET['view']) && $_GET['view'] === 'mod_team' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update') {
        $teamId = $_POST['team_id'] ?? '';
        $nom = $_POST['nom'] ?? '';
        $points = $_POST['points'] ?? '';

        if (!empty($teamId) && !empty($nom) && $points !== '') {
            if (updateTeam($teamId, $nom, $points)) {
                $message = "Équipe modifiée avec succès !";
                $teams = getAllTeams();
            } else {
                $message = "Erreur lors de la modification de l'équipe.";
            }
        } else {
            $message = "Veuillez remplir tous les champs obligatoires.";
        }
    }

    if ($action === 'add') {
        $nom = $_POST['nom'] ?? '';
        $points = $_POST['points'] ?? '';

        if (!empty($nom) && $points !== '') {
            if (addTeam($nom, $points)) {
                $message = "Équipe ajoutée avec succès !";
                $teams = getAllTeams();     
            } else {
                $message = "Erreur lors de l'ajout de l'équipe.";
            }
        } else {
            $message = "Veuillez remplir tous les champs obligatoires.";
        }
        header('Location: ../controller/C_admin.php?view=mod_team');
        exit();
    }

    if ($action === 'delete') {
        $teamId = $_POST['team_id'] ?? '';

        if (!empty($teamId)) {
            if (deleteTeam($teamId)) {
                $message = "Équipe supprimée avec succès !";
                $teams = getAllTeams();
            } else {
                $message = "Impossible de supprimer cette équipe (des utilisateurs y sont assignés).";
            }
        }
    }
}

require_once '../view/admin.php';
require_once '../view/deco.php';
require_once '../view/footer.php';
?>