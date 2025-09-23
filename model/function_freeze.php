<?php

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

// Fonction pour basculer le statut freeze d'une équipe
function toggleTeamFreeze($teamId, $pdo) {
    try {
        // Récupérer le statut actuel
        $stmt = $pdo->prepare("SELECT freeze FROM teams WHERE id = :id");
        $stmt->bindParam(':id', $teamId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            // Inverser le statut
            $newStatus = !$result['freeze'];
            
            // Mettre à jour
            $updateStmt = $pdo->prepare("UPDATE teams SET freeze = :freeze WHERE id = :id");
            $updateStmt->bindParam(':freeze', $newStatus, PDO::PARAM_BOOL);
            $updateStmt->bindParam(':id', $teamId, PDO::PARAM_INT);
            
            return $updateStmt->execute();
        }
        
        return false;
    } catch (PDOException $e) {
        error_log("Erreur lors du basculement du freeze: " . $e->getMessage());
        return false;
    }
}

?>