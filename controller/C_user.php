<?php
session_start();
require_once '../model/config.php';
require_once '../model/function.php';
require_once '../model/function_freeze.php';
require_once '../view/head.php';

requireRole('user');

// Vérifier le statut freeze de l'équipe de l'utilisateur
$isTeamFrozen = getUserTeamFreezeStatus($_SESSION['user_id'], $pdo);

// Script de refresh automatique toutes les 3 secondes
echo '<script>setTimeout(function() { window.location.reload(); }, 3000);</script>';

if ($isTeamFrozen) {
    // Si l'équipe est gelée, afficher seulement la vue freeze
    require_once '../view/freeze.php';
} else {
    // Si l'équipe n'est pas gelée, afficher les vues normales
    $teams = getTeamsWithScores();
    $userTeam = getUserTeam($_SESSION['user_id']);
    
    require_once '../view/user.php';
    require_once '../view/view_point.php';
    require_once '../view/score_user.php';
    require_once '../view/deco.php';
    require_once '../view/footer.php';
}


?>