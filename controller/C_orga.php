<?php
session_start();
require_once '../model/config.php';
require_once '../model/function.php';

requireRole('organisateur');

$message = '';
$myTeam = getOrganizerTeam($_SESSION['user_id']);
$teams = $myTeam ? [$myTeam] : [];
$teamsWithScores = getTeamsWithScores();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $teamId = $_POST['team_id'] ?? '';
    $operation = $_POST['operation'] ?? '';

    if (!empty($teamId) && !empty($operation)) {
        if ($myTeam && $teamId == $myTeam['id']) {
            if (updateTeamPoints($teamId, $operation)) {
                $message = "Points modifiés avec succès !";
                $myTeam = getOrganizerTeam($_SESSION['user_id']);
                $teams = $myTeam ? [$myTeam] : [];
                $teamsWithScores = getTeamsWithScores();
            } else {
                $message = "Erreur lors de la modification des points.";
            }
        } else {
            $message = "Vous ne pouvez modifier que votre équipe.";
        }
    } else {
        $message = "Veuillez sélectionner une équipe et une action.";
    }
}

if ( isFreezed($_SESSION['user_team_id']) == false ) {
    require_once '../view/orga.php';
    require_once '../view/add.php';
    require_once '../view/score.php';
}
else {
    require_once '../view/ghost.php';
}
require_once '../view/deco.php';
require_once '../view/footer.php';
?>