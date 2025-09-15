<?php
require_once 'config.php';

$error = '';
$mysql_status = test_mysql_connection();

// Si déjà connecté, rediriger
if (is_logged_in()) {
    redirect('dashboard.php');
}

// Traitement du formulaire
if ($_POST) {
    $nom_equipe = trim($_POST['nom_equipe'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($nom_equipe) || empty($password)) {
        $error = 'Veuillez remplir tous les champs';
    } else {
        try {
            // Vérifier les identifiants
            $stmt = $pdo->prepare("SELECT id, nom_equipe FROM teams WHERE nom_equipe = ? AND mot_de_passe = ?");
            $stmt->execute([$nom_equipe, $password]);
            $team = $stmt->fetch();

            if ($team) {
                $_SESSION['team_id'] = $team['id'];
                $_SESSION['nom_equipe'] = $team['nom_equipe'];
                redirect('dashboard.php');
            } else {
                $error = 'Nom d\'équipe ou mot de passe incorrect';
            }
        } catch (Exception $e) {
            $error = 'Erreur de connexion à la base de données: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maria Curia - Connexion (WAMP/MySQL)</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="text-center">
            <h1 class="title">🎪 Maria Curia 🎪</h1>
            <h2 class="subtitle">Version WAMP/MySQL</h2>
        </div>

        <!-- Statut MySQL -->
        <?php if ($mysql_status): ?>
            <div class="mysql-status">
                ✅ MySQL connecté - Base de données opérationnelle
            </div>
        <?php else: ?>
            <div class="mysql-error">
                ❌ Erreur MySQL - Vérifiez que WAMP est démarré et que la base "maria_curia" existe
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="nom_equipe">🎯 Nom de l'équipe :</label>
                <input type="text" id="nom_equipe" name="nom_equipe"
                       value="<?= htmlspecialchars($_POST['nom_equipe'] ?? '') ?>"
                       placeholder="Ex: Equipe1"
                       required>
            </div>

            <div class="form-group">
                <label for="password">🔐 Mot de passe :</label>
                <input type="password" id="password" name="password"
                       placeholder="Saisissez votre mot de passe"
                       required>
            </div>

            <div class="text-center">
                <button type="submit">🚀 Se connecter</button>
            </div>
        </form>

        <div class="info-box">
            <h3>📋 Équipes de test disponibles :</h3>
            <ul>
                <li><strong>Equipe1</strong> - mot de passe : <code>pass1</code></li>
                <li><strong>Equipe2</strong> - mot de passe : <code>pass2</code></li>
                <li><strong>Equipe3</strong> - mot de passe : <code>pass3</code></li>
                <li><strong>Test Team</strong> - mot de passe : <code>test123</code></li>
                <li><strong>Hackers</strong> - mot de passe : <code>hack</code></li>
            </ul>
        </div>

        <div class="info-box" style="background: linear-gradient(135deg, #9b59b6, #8e44ad);">
            <h3>⚙️ Configuration WAMP :</h3>
            <ol>
                <li>Démarrez <strong>WAMP Server</strong></li>
                <li>Ouvrez <strong>phpMyAdmin</strong></li>
                <li>Importez le fichier <code>database.sql</code></li>
                <li>Vérifiez que la base <code>maria_curia</code> existe</li>
                <li>Lancez le jeu depuis <code>http://localhost/maria-curia-wamp/</code></li>
            </ol>
        </div>

        <?php if (!$mysql_status): ?>
            <div class="info-box" style="background: linear-gradient(135deg, #e74c3c, #c0392b);">
                <h3>🔧 Dépannage MySQL :</h3>
                <ul>
                    <li>Vérifiez que <strong>WAMP</strong> est démarré (icône verte)</li>
                    <li>Vérifiez que <strong>MySQL</strong> fonctionne dans WAMP</li>
                    <li>Créez la base <code>maria_curia</code> dans phpMyAdmin</li>
                    <li>Importez le fichier <code>database.sql</code></li>
                    <li>Vérifiez les paramètres dans <code>config.php</code></li>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>