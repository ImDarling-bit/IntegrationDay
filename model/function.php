<?php

require_once 'config.php';

function login($identifiant, $mot_de_passe) {
    global $pdo;

    try {
        $stmt = $pdo->prepare("SELECT u.id, u.identifiant, u.mot_de_passe, u.team_id, r.nom as role FROM users u JOIN roles r ON u.role_id = r.id WHERE u.identifiant = :identifiant");
        $stmt->bindParam(':identifiant', $identifiant);
        $stmt->execute();

        $user = $stmt->fetch();

        if ($user && $user['mot_de_passe'] === $mot_de_passe) {
            return [
                'id' => $user['id'],
                'identifiant' => $user['identifiant'],
                'role' => $user['role'],
                'team_id' => $user['team_id']
            ];
        }

        return false;
    } catch (PDOException $e) {
        error_log("Erreur de login : " . $e->getMessage());
        return false;
    }
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getUserRole() {
    return $_SESSION['user_role'] ?? null;
}

function logout() {
    session_destroy();
    header('Location: ../index.php');
    exit();
}

function requireRole($requiredRole) {
    if (!isLoggedIn() || getUserRole() !== $requiredRole) {
        header('Location: login.php');
        exit();
    }
}

function getAllTeams() {
    global $pdo;

    try {
        $stmt = $pdo->prepare("SELECT id, nom, points FROM teams ORDER BY points DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Erreur lors de la récupération des équipes : " . $e->getMessage());
        return [];
    }
}

function getTeamsWithScores() {
    global $pdo;
    $maxPoints = 8;

    try {
        $stmt = $pdo->prepare("SELECT id, nom, points FROM teams ORDER BY points DESC");
        $stmt->execute();
        $teams = $stmt->fetchAll();

        $result = [];
        $position = 1;

        foreach ($teams as $team) {
            $percentage = ($team['points'] / $maxPoints) * 100;
            $result[] = [
                'position' => $position,
                'nom' => $team['nom'],
                'points' => $team['points'],
                'percentage' => round($percentage, 2)
            ];
            $position++;
        }

        return $result;
    } catch (PDOException $e) {
        error_log("Erreur lors de la récupération des scores : " . $e->getMessage());
        return [];
    }
}

function updateTeamPoints($teamId, $operation) {
    global $pdo;

    try {
        if ($operation === 'add') {
            $stmt = $pdo->prepare("UPDATE teams SET points = points + 1 WHERE id = :id");
        } elseif ($operation === 'subtract') {
            $stmt = $pdo->prepare("UPDATE teams SET points = GREATEST(points - 1, 0) WHERE id = :id");
        } else {
            return false;
        }

        $stmt->bindParam(':id', $teamId, PDO::PARAM_INT);
        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("Erreur lors de la modification des points : " . $e->getMessage());
        return false;
    }
}

function getOrganizerTeam($userId) {
    global $pdo;

    try {
        $stmt = $pdo->prepare("SELECT t.id, t.nom, t.points FROM teams t JOIN users u ON t.id = u.team_id WHERE u.id = :user_id");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Erreur lors de la récupération de l'équipe : " . $e->getMessage());
        return false;
    }
}

function getAllUsers() {
    global $pdo;

    try {
        $stmt = $pdo->prepare("SELECT u.id, u.identifiant, u.mot_de_passe, u.team_id, r.nom as role, t.nom as team_name FROM users u JOIN roles r ON u.role_id = r.id LEFT JOIN teams t ON u.team_id = t.id ORDER BY u.id");
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Erreur lors de la récupération des utilisateurs : " . $e->getMessage());
        return [];
    }
}

function getAllRoles() {
    global $pdo;

    try {
        $stmt = $pdo->prepare("SELECT id, nom FROM roles ORDER BY nom");
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Erreur lors de la récupération des rôles : " . $e->getMessage());
        return [];
    }
}

function updateUser($userId, $identifiant, $motDePasse, $roleId, $teamId) {
    global $pdo;

    try {
        $stmt = $pdo->prepare("UPDATE users SET identifiant = :identifiant, mot_de_passe = :mot_de_passe, role_id = :role_id, team_id = :team_id WHERE id = :id");
        $stmt->bindParam(':identifiant', $identifiant);
        $stmt->bindParam(':mot_de_passe', $motDePasse);
        $stmt->bindParam(':role_id', $roleId, PDO::PARAM_INT);
        $stmt->bindParam(':team_id', $teamId, PDO::PARAM_INT);
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("Erreur lors de la modification de l'utilisateur : " . $e->getMessage());
        return false;
    }
}

function addUser($identifiant, $motDePasse, $roleId, $teamId) {
    global $pdo;

    try {
        $stmt = $pdo->prepare("INSERT INTO users (identifiant, mot_de_passe, role_id, team_id) VALUES (:identifiant, :mot_de_passe, :role_id, :team_id)");
        $stmt->bindParam(':identifiant', $identifiant);
        $stmt->bindParam(':mot_de_passe', $motDePasse);
        $stmt->bindParam(':role_id', $roleId, PDO::PARAM_INT);
        $stmt->bindParam(':team_id', $teamId, PDO::PARAM_INT);
        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("Erreur lors de l'ajout de l'utilisateur : " . $e->getMessage());
        return false;
    }
}

function deleteUser($userId) {
    global $pdo;

    try {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("Erreur lors de la suppression de l'utilisateur : " . $e->getMessage());
        return false;
    }
}

function updateTeam($teamId, $nom, $points) {
    global $pdo;

    try {
        $stmt = $pdo->prepare("UPDATE teams SET nom = :nom, points = :points WHERE id = :id");
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':points', $points, PDO::PARAM_INT);
        $stmt->bindParam(':id', $teamId, PDO::PARAM_INT);
        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("Erreur lors de la modification de l'équipe : " . $e->getMessage());
        return false;
    }
}

function addTeam($nom, $points) {
    global $pdo;

    try {
        $stmt = $pdo->prepare("INSERT INTO teams (nom, points) VALUES (:nom, :points)");
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':points', $points, PDO::PARAM_INT);
        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("Erreur lors de l'ajout de l'équipe : " . $e->getMessage());
        return false;
    }
}

function deleteTeam($teamId) {
    global $pdo;

    try {
        // Vérifier d'abord s'il y a des utilisateurs assignés à cette équipe
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM users WHERE team_id = :team_id");
        $stmt->bindParam(':team_id', $teamId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();

        if ($result['count'] > 0) {
            return false; // Ne pas supprimer si des utilisateurs sont assignés
        }

        $stmt = $pdo->prepare("DELETE FROM teams WHERE id = :id");
        $stmt->bindParam(':id', $teamId, PDO::PARAM_INT);
        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("Erreur lors de la suppression de l'équipe : " . $e->getMessage());
        return false;
    }
}

function freeze($teamId) {
    global $pdo;

    try {

        if (isset($_SESSION["freeze"][$teamId]) &&  $_SESSION["freeze"][$teamId] == false) { 
           // Géle l'équipe.
            $stmt = $pdo->prepare("UPDATE teams SET freezed = 1, debut_freeze = NOW() WHERE id = :id");
            $stmt->bindParam(':id', $teamId, PDO::PARAM_INT);
            $stmt->execute();
            // Indique à l'administrateur que l'équipe est gelée.
            $_SESSION["freeze"][$teamId] = true;
        }

    } catch (PDOException $e) {
        error_log("Erreur lors du gel de l'équipe : " . $e->getMessage());
    }
      //echo  "<script> alert('ok'); </script>"; 
}

function freezed($teamId) {
    global $pdo;

    try {

        if (isFreezed($teamId)==false) {// L'équipe n'est pas gelée
            
            return false; 
        }
        else {// L'équipe est gelée
             
            // Vérifier si le gel dure depuis plus de 30 secondes.
            $stmt = $pdo->prepare("SELECT TIMESTAMPDIFF(SECOND,debut_freeze,NOW()) as temps FROM teams WHERE id = :team_id");
            $stmt->bindParam(':team_id', $teamId, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch();

//echo  "<script> alert('".$result['temps']." / ".$teamId."'); </script>"; 

            if ($result['temps'] >= 30) { // L'équipe est pas dégelée
                $stmt = $pdo->prepare("UPDATE teams SET freezed = 0 WHERE id = :id");
                $stmt->bindParam(':id', $teamId, PDO::PARAM_INT);
                $stmt->execute();
                $_SESSION["freeze"][$teamId] = false;
                return false;
            }
            else { // L'équipe reste gelée
                return true; 
            }
        }

    } catch (PDOException $e) {
        error_log("Erreur lors de la gestion du gel d'une équipe : " . $e->getMessage());
        return false;
    }
  
}

function isFreezed($teamId) {
    global $pdo;

    try {
        // Vérifier si l'équpe est gelée
        $val = 1;
        $stmt = $pdo->prepare("SELECT count(*) as nb FROM teams WHERE id = :team_id and freezed = :val");
        $stmt->bindParam(':team_id', $teamId, PDO::PARAM_INT);
        $stmt->bindParam(':val', $val, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();

        if ($result['nb'] == 0) {// L'équipe n'est pas gelée
            
            return false; 
        }
        else {// L'équipe est gelée
             
           return true;
        }

    } catch (PDOException $e) {
        error_log("Erreur lors de la vérification du gel d'une équipe : " . $e->getMessage());
        return false;
    }
  
}

?>