<?php
session_start();
require_once '../model/config.php';
require_once '../model/function.php';

requireRole('organisateur');

// Fonction pour récupérer le statut freeze de l'équipe d'un organisateur
function getTeamFreezeStatus($userId, $pdo) {
    try {
        // Pour un organisateur, on récupère son équipe via team_id
        $stmt = $pdo->prepare("
            SELECT t.freeze 
            FROM users u 
            JOIN teams t ON u.team_id = t.id 
            WHERE u.id = :user_id LIMIT 1
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

// Vérifier le statut freeze de l'équipe de l'organisateur
$isTeamFrozen = getTeamFreezeStatus($_SESSION['user_id'], $pdo);

// Script de refresh automatique toutes les 3 secondes
echo '<script>setTimeout(function() { window.location.reload(); }, 3000);</script>';

// Si l'équipe est gelée, afficher seulement la vue freeze
if ($isTeamFrozen) {
    require_once '../view/freeze.php';
} else {
    // Si l'équipe n'est pas gelée, continuer avec la logique normale
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
        header('Location: ../controller/C_orga.php');
        exit();
    }

    // Afficher les vues normales de l'organisateur
    require_once '../view/orga.php';
    require_once '../view/add.php';
    require_once '../view/score_orga.php';
    require_once '../view/deco.php';
    require_once '../view/footer.php';
}


?>