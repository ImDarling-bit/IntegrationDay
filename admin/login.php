<?php
require_once 'config.php';

$error = '';

// Traitement du formulaire de connexion
if ($_POST) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        if (admin_login($username, $password)) {
            redirect('dashboard.php');
        } else {
            $error = 'Nom d\'utilisateur ou mot de passe incorrect';
        }
    } else {
        $error = 'Veuillez remplir tous les champs';
    }
}

// Si déjà connecté, rediriger
if (isset($_SESSION['admin_id'])) {
    redirect('dashboard.php');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Maria Curia</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-header">
            <h1>🔓 ADMIN PANEL</h1>
            <h2>Maria Curia Control Center</h2>
            <p class="hacker-text">⚠️ ACCÈS RESTREINT - ORGANISATEURS UNIQUEMENT ⚠️</p>
        </div>

        <form method="POST" class="login-form">
            <div class="form-group">
                <label for="username">👤 Nom d'utilisateur :</label>
                <input type="text" id="username" name="username" required
                       placeholder="admin, hacker1, hacker2, organizer"
                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="password">🔐 Mot de passe :</label>
                <input type="password" id="password" name="password" required
                       placeholder="Entrez votre mot de passe">
            </div>

            <?php if ($error): ?>
                <div class="error-message">
                    ❌ <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <button type="submit" class="login-btn">
                🚀 ACCÉDER AU CONTRÔLE
            </button>
        </form>

        <div class="login-info">
            <h3>🎮 Comptes de Test :</h3>
            <div class="accounts-grid">
                <div class="account-card">
                    <strong>admin</strong><br>
                    <small>admin123</small>
                </div>
                <div class="account-card">
                    <strong>hacker1</strong><br>
                    <small>hack123</small>
                </div>
                <div class="account-card">
                    <strong>hacker2</strong><br>
                    <small>hack456</small>
                </div>
                <div class="account-card">
                    <strong>organizer</strong><br>
                    <small>org123</small>
                </div>
            </div>

            <div class="mysql-status">
                <h3>📊 Statut MySQL :</h3>
                <div class="status-grid">
                    <div class="status-item">
                        <strong>Base Admin :</strong>
                        <?php
                        try {
                            $admin_pdo->query("SELECT 1");
                            echo '<span class="status-ok">✅ Connectée</span>';
                        } catch(Exception $e) {
                            echo '<span class="status-error">❌ Erreur</span>';
                        }
                        ?>
                    </div>
                    <div class="status-item">
                        <strong>Base Jeu :</strong>
                        <?php
                        try {
                            $game_pdo->query("SELECT 1");
                            echo '<span class="status-ok">✅ Connectée</span>';
                        } catch(Exception $e) {
                            echo '<span class="status-error">❌ Erreur</span>';
                        }
                        ?>
                    </div>
                </div>
            </div>

            <div class="setup-instructions">
                <h3>⚙️ Installation :</h3>
                <ol>
                    <li>Créer la base <code>maria_curia_admin</code></li>
                    <li>Importer <code>admin_database.sql</code></li>
                    <li>Vérifier que WAMP est démarré</li>
                    <li>Accéder via : <code>localhost/maria-curia-wamp/admin/</code></li>
                </ol>
            </div>
        </div>

        <div class="footer">
            <p>🎪 Maria Curia Admin Panel - Version WAMP/MySQL</p>
            <p><a href="../index.php">🔙 Retour au Jeu</a></p>
        </div>
    </div>

    <script>
        // Effet Matrix pour l'arrière-plan
        document.addEventListener('DOMContentLoaded', function() {
            const chars = '01';
            const speed = 50;

            function createMatrixEffect() {
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');
                canvas.style.position = 'fixed';
                canvas.style.top = '0';
                canvas.style.left = '0';
                canvas.style.zIndex = '-1';
                canvas.style.opacity = '0.1';
                document.body.appendChild(canvas);

                canvas.width = window.innerWidth;
                canvas.height = window.innerHeight;

                const columns = canvas.width / 20;
                const drops = [];

                for (let i = 0; i < columns; i++) {
                    drops[i] = 1;
                }

                function draw() {
                    ctx.fillStyle = 'rgba(0, 0, 0, 0.05)';
                    ctx.fillRect(0, 0, canvas.width, canvas.height);

                    ctx.fillStyle = '#00ff00';
                    ctx.font = '15px monospace';

                    for (let i = 0; i < drops.length; i++) {
                        const text = chars[Math.floor(Math.random() * chars.length)];
                        ctx.fillText(text, i * 20, drops[i] * 20);

                        if (drops[i] * 20 > canvas.height && Math.random() > 0.975) {
                            drops[i] = 0;
                        }
                        drops[i]++;
                    }
                }

                setInterval(draw, speed);
            }

            createMatrixEffect();
        });

        // Animation du titre
        const title = document.querySelector('h1');
        let isGlitching = false;

        setInterval(() => {
            if (!isGlitching) {
                isGlitching = true;
                title.style.textShadow = '2px 0 #ff0000, -2px 0 #00ff00';
                setTimeout(() => {
                    title.style.textShadow = 'none';
                    isGlitching = false;
                }, 200);
            }
        }, 3000);
    </script>
</body>
</html>