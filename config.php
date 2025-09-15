<?php
// Configuration MySQL pour Maria Curia WAMP
session_start();

// Configuration de la base de données MySQL
$host = 'localhost';
$dbname = 'maria_curia';
$username = 'root';  // Utilisateur par défaut WAMP
$password = '';      // Pas de mot de passe par défaut WAMP

// Connexion à MySQL
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die('Erreur de connexion MySQL: ' . $e->getMessage() . '<br>Vérifiez que WAMP est démarré et que la base "maria_curia" existe.');
}

// Fonctions utilitaires simples
function redirect($url) {
    header("Location: $url");
    exit();
}

function is_logged_in() {
    return isset($_SESSION['team_id']) && !empty($_SESSION['team_id']);
}

function require_login() {
    if (!is_logged_in()) {
        redirect('login.php');
    }
}

function get_team_score($team_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT score FROM teams WHERE id = ?");
    $stmt->execute([$team_id]);
    $result = $stmt->fetch();
    return $result ? $result['score'] : 0;
}

function get_team_scans($team_id) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT c.nom, c.description, s.scanned_at
        FROM scans s
        JOIN characters c ON s.character_id = c.id
        WHERE s.team_id = ?
        ORDER BY s.scanned_at DESC
    ");
    $stmt->execute([$team_id]);
    return $stmt->fetchAll();
}

function scan_character($team_id, $qr_code) {
    global $pdo;

    // Vérifier si le personnage existe
    $stmt = $pdo->prepare("SELECT id, nom, points FROM characters WHERE qr_code = ?");
    $stmt->execute([$qr_code]);
    $character = $stmt->fetch();

    if (!$character) {
        return ['success' => false, 'message' => 'QR Code invalide'];
    }

    // Vérifier si déjà scanné
    $stmt = $pdo->prepare("SELECT id FROM scans WHERE team_id = ? AND character_id = ?");
    $stmt->execute([$team_id, $character['id']]);
    if ($stmt->fetch()) {
        return ['success' => false, 'message' => 'Personnage déjà découvert'];
    }

    try {
        // Démarrer une transaction
        $pdo->beginTransaction();

        // Enregistrer le scan
        $stmt = $pdo->prepare("INSERT INTO scans (team_id, character_id) VALUES (?, ?)");
        $stmt->execute([$team_id, $character['id']]);

        // Mettre à jour le score
        $stmt = $pdo->prepare("UPDATE teams SET score = score + ? WHERE id = ?");
        $stmt->execute([$character['points'], $team_id]);

        // Valider la transaction
        $pdo->commit();

        return [
            'success' => true,
            'message' => 'Personnage découvert : ' . $character['nom'] . ' !',
            'points' => $character['points'],
            'character_name' => $character['nom']
        ];

    } catch (Exception $e) {
        // Annuler la transaction en cas d'erreur
        $pdo->rollBack();
        return ['success' => false, 'message' => 'Erreur lors de l\'enregistrement'];
    }
}

function get_leaderboard() {
    global $pdo;
    $stmt = $pdo->query("SELECT nom_equipe, score FROM teams ORDER BY score DESC, nom_equipe ASC");
    return $stmt->fetchAll();
}

function get_team_by_id($team_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM teams WHERE id = ?");
    $stmt->execute([$team_id]);
    return $stmt->fetch();
}

function get_all_characters() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM characters ORDER BY nom");
    return $stmt->fetchAll();
}

function get_discovery_stats() {
    global $pdo;

    $stmt = $pdo->query("
        SELECT
            c.nom as character_name,
            COUNT(s.id) as discoveries
        FROM characters c
        LEFT JOIN scans s ON c.id = s.character_id
        GROUP BY c.id, c.nom
        ORDER BY discoveries DESC, c.nom
    ");

    return $stmt->fetchAll();
}

function get_recent_activity($limit = 10) {
    global $pdo;

    // Assurer que $limit est un entier pour éviter les injections SQL
    $limit = (int)$limit;

    $stmt = $pdo->query("
        SELECT
            t.nom_equipe,
            c.nom as character_name,
            s.scanned_at
        FROM scans s
        JOIN teams t ON s.team_id = t.id
        JOIN characters c ON s.character_id = c.id
        ORDER BY s.scanned_at DESC
        LIMIT $limit
    ");

    return $stmt->fetchAll();
}

function test_mysql_connection() {
    global $pdo;
    try {
        $stmt = $pdo->query("SELECT 1");
        return true;
    } catch (Exception $e) {
        return false;
    }
}
?>