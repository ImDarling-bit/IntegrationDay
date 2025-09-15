<?php
// Configuration Admin - Maria Curia WAMP
session_start();

// Configuration MySQL pour WAMP
$admin_host = 'localhost';
$admin_dbname = 'maria_curia_admin';  // Base séparée pour l'admin
$admin_username = 'root';
$admin_password = '';

// Configuration MySQL pour le jeu principal
$game_host = 'localhost';
$game_dbname = 'maria_curia';
$game_username = 'root';
$game_password = '';

try {
    // Connexion à la base admin
    $admin_pdo = new PDO("mysql:host=$admin_host;dbname=$admin_dbname;charset=utf8mb4",
                        $admin_username, $admin_password);
    $admin_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Connexion à la base du jeu
    $game_pdo = new PDO("mysql:host=$game_host;dbname=$game_dbname;charset=utf8mb4",
                       $game_username, $game_password);
    $game_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch(PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Fonctions d'authentification admin
function require_admin_login() {
    if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_username'])) {
        header('Location: login.php');
        exit();
    }
}

function admin_login($username, $password) {
    global $admin_pdo;

    $stmt = $admin_pdo->prepare("SELECT id, username, role FROM admins WHERE username = ? AND password = ?");
    $stmt->execute([$username, $password]);
    $admin = $stmt->fetch();

    if ($admin) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        $_SESSION['admin_role'] = $admin['role'];

        // Log de connexion
        $stmt = $admin_pdo->prepare("UPDATE admins SET last_login = NOW() WHERE id = ?");
        $stmt->execute([$admin['id']]);

        log_admin_action('LOGIN', null, 'Connexion réussie');
        return true;
    }
    return false;
}

function log_admin_action($action, $target_team = null, $details = null) {
    global $admin_pdo;

    if (!isset($_SESSION['admin_id'])) return;

    $stmt = $admin_pdo->prepare("INSERT INTO admin_logs (admin_id, action, target_team, details, ip_address) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $_SESSION['admin_id'],
        $action,
        $target_team,
        $details,
        $_SERVER['REMOTE_ADDR'] ?? 'localhost'
    ]);
}

// Fonctions de manipulation du jeu
function get_all_teams() {
    global $game_pdo;

    $stmt = $game_pdo->query("
        SELECT t.*,
               COUNT(s.id) as scans_count,
               COALESCE(SUM(c.points), 0) as total_score
        FROM teams t
        LEFT JOIN scans s ON t.id = s.team_id
        LEFT JOIN characters c ON s.character_id = c.id
        GROUP BY t.id
        ORDER BY total_score DESC, t.nom_equipe
    ");
    return $stmt->fetchAll();
}

function reset_team_progress($team_id) {
    global $game_pdo;

    try {
        $game_pdo->beginTransaction();

        // Supprimer les scans de l'équipe
        $stmt = $game_pdo->prepare("DELETE FROM scans WHERE team_id = ?");
        $stmt->execute([$team_id]);

        // Remettre le score à zéro
        $stmt = $game_pdo->prepare("UPDATE teams SET score = 0 WHERE id = ?");
        $stmt->execute([$team_id]);

        $game_pdo->commit();

        log_admin_action('RESET_TEAM', null, "Reset équipe ID: $team_id");
        return true;
    } catch (Exception $e) {
        $game_pdo->rollBack();
        return false;
    }
}

function reset_all_progress() {
    global $game_pdo;

    try {
        $game_pdo->beginTransaction();

        // Supprimer tous les scans
        $stmt = $game_pdo->prepare("DELETE FROM scans");
        $stmt->execute();

        // Remettre tous les scores à zéro
        $stmt = $game_pdo->prepare("UPDATE teams SET score = 0");
        $stmt->execute();

        $game_pdo->commit();

        log_admin_action('RESET_ALL', null, 'Reset complet du jeu');
        return true;
    } catch (Exception $e) {
        $game_pdo->rollBack();
        return false;
    }
}

function add_points_to_team($team_id, $points) {
    global $game_pdo;

    try {
        // Approche plus simple : ajouter directement les points au score
        $stmt = $game_pdo->prepare("UPDATE teams SET score = score + ? WHERE id = ?");
        $success = $stmt->execute([$points, $team_id]);

        if ($success) {
            log_admin_action('ADD_POINTS', null, "Ajout $points points à équipe ID: $team_id");
        }

        return $success;
    } catch (Exception $e) {
        return false;
    }
}

function get_game_stats() {
    global $game_pdo;

    $stats = [];

    // Total équipes
    $stmt = $game_pdo->query("SELECT COUNT(*) as count FROM teams");
    $stats['total_teams'] = $stmt->fetch()['count'];

    // Total scans
    $stmt = $game_pdo->query("SELECT COUNT(*) as count FROM scans");
    $stats['total_scans'] = $stmt->fetch()['count'];

    // Activité récente
    $stmt = $game_pdo->query("
        SELECT t.nom_equipe, c.nom as character_name, s.scanned_at
        FROM scans s
        JOIN teams t ON s.team_id = t.id
        JOIN characters c ON s.character_id = c.id
        ORDER BY s.scanned_at DESC
        LIMIT 10
    ");
    $stats['recent_activity'] = $stmt->fetchAll();

    return $stats;
}

function get_admin_logs($limit = 50) {
    global $admin_pdo;

    // Assurer que $limit est un entier
    $limit = (int)$limit;

    $stmt = $admin_pdo->query("
        SELECT al.*, a.username
        FROM admin_logs al
        JOIN admins a ON al.admin_id = a.id
        ORDER BY al.created_at DESC
        LIMIT $limit
    ");
    return $stmt->fetchAll();
}

function redirect($url) {
    header("Location: $url");
    exit();
}
?>