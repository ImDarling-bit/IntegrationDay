<?php
session_start();
require_once '../model/config.php';
require_once '../model/function.php';

$message = '';
$teams = getAllTeams();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $teamId = $_POST['team_id'] ?? '';
    $operation = $_POST['operation'] ?? '';

    if (!empty($teamId) && !empty($operation)) {
        if (updateTeamPoints($teamId, $operation)) {
            $message = "Points modifiés avec succès !";
            $teams = getAllTeams();
        } else {
            $message = "Erreur lors de la modification des points.";
        }
    } else {
        $message = "Veuillez sélectionner une équipe et une action.";
    }
    header('Location: ../controller/C_add.php');
    exit();
}

require_once '../view/add.php';
?>