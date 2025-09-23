<?php
session_start();
require_once '../model/config.php';
require_once '../model/function.php';

requireRole('admin');

$message = '';
$users = getAllUsers();
$roles = getAllRoles();
$teams = getAllTeams();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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
                $users = getAllUsers();
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
                $users = getAllUsers();
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
                $users = getAllUsers();
            } else {
                $message = "Erreur lors de la suppression de l'utilisateur.";
            }
        }
    }
}

require_once '../view/mod_user.php';
?>