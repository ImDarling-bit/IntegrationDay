<?php
session_start();
require_once '../model/config.php';
require_once '../model/function.php';

requireRole('user');

// Fonction pour récupérer le statut freeze de l'équipe d'un utilisateur
function getUserTeamFreezeStatus($userId, $pdo) {
    try {
        // Pour un utilisateur, on récupère son équipe via la table user_teams
        $stmt = $pdo->prepare("
            SELECT t.freeze 
            FROM user_teams ut 
            JOIN teams t ON ut.team_id = t.id 
            WHERE ut.user_id = :user_id LIMIT 1
        ");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            return (bool) $result['freeze'];
        }
        
        return false; // Par défaut pas gelé
    } catch (PDOException $e) {
        error_log("Erreur lors de la récupération du statut freeze de l'équipe: " . $e->getMessage());
        return false;
    }
}

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