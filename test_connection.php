<?php
// Test de connexion MySQL pour Maria Curia
require_once 'config.php';

echo "<h1>🔧 Test de Connexion MySQL - Maria Curia</h1>";

// Test connexion principale
try {
    $pdo->query("SELECT 1");
    echo "<p style='color: green;'>✅ Connexion à la base 'maria_curia' : OK</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erreur base 'maria_curia' : " . $e->getMessage() . "</p>";
}

// Test des tables
$tables = ['teams', 'characters', 'scans'];
foreach ($tables as $table) {
    try {
        $stmt = $pdo->query("DESCRIBE $table");
        $columns = $stmt->fetchAll();
        echo "<p style='color: green;'>✅ Table '$table' : " . count($columns) . " colonnes</p>";

        // Afficher les colonnes
        echo "<ul style='margin-left: 20px; color: #666;'>";
        foreach ($columns as $column) {
            echo "<li>" . $column['Field'] . " (" . $column['Type'] . ")</li>";
        }
        echo "</ul>";

    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Erreur table '$table' : " . $e->getMessage() . "</p>";
    }
}

// Test des données
try {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM teams");
    $count = $stmt->fetch()['count'];
    echo "<p style='color: blue;'>📊 Équipes en base : $count</p>";

    $stmt = $pdo->query("SELECT COUNT(*) as count FROM characters");
    $count = $stmt->fetch()['count'];
    echo "<p style='color: blue;'>📊 Personnages en base : $count</p>";

    $stmt = $pdo->query("SELECT COUNT(*) as count FROM scans");
    $count = $stmt->fetch()['count'];
    echo "<p style='color: blue;'>📊 Scans en base : $count</p>";

} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erreur lecture données : " . $e->getMessage() . "</p>";
}

// Test des fonctions
echo "<h2>🧪 Test des Fonctions</h2>";

try {
    $teams = get_leaderboard();
    echo "<p style='color: green;'>✅ get_leaderboard() : " . count($teams) . " équipes</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ get_leaderboard() : " . $e->getMessage() . "</p>";
}

try {
    $activity = get_recent_activity(5);
    echo "<p style='color: green;'>✅ get_recent_activity() : " . count($activity) . " activités</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ get_recent_activity() : " . $e->getMessage() . "</p>";
}

try {
    $characters = get_all_characters();
    echo "<p style='color: green;'>✅ get_all_characters() : " . count($characters) . " personnages</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ get_all_characters() : " . $e->getMessage() . "</p>";
}

echo "<h2>✅ Test terminé</h2>";
echo "<p><a href='login.php'>🔙 Retour au jeu</a></p>";
?>