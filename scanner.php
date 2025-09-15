<?php
require_once 'config.php';
require_login();

$message = '';
$message_type = '';
$scan_result = null;

// Traitement du scan
if ($_POST && isset($_POST['qr_code'])) {
    $qr_code = trim($_POST['qr_code']);

    if (empty($qr_code)) {
        $message = 'Veuillez entrer un code QR';
        $message_type = 'error';
    } else {
        $scan_result = scan_character($_SESSION['team_id'], $qr_code);
        $message = $scan_result['message'];
        $message_type = $scan_result['success'] ? 'success' : 'error';

        if ($scan_result['success']) {
            $message .= ' Vous gagnez ' . $scan_result['points'] . ' points !';
        }
    }
}

// Récupérer la liste des personnages pour l'aide
$all_characters = get_all_characters();
$team_scans = get_team_scans($_SESSION['team_id']);
$discovered_character_names = array_column($team_scans, 'nom');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scanner QR Code - Maria Curia WAMP</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="clearfix">
            <h1 class="title">🔍 Scanner QR Code</h1>
            <a href="logout.php" class="btn-logout button">Déconnexion</a>
        </div>

        <div class="nav">
            <a href="dashboard.php">🏠 Accueil</a>
            <a href="scanner.php">🔍 Scanner QR Code</a>
            <a href="leaderboard.php">🏆 Classement</a>
            <a href="stats.php">📊 Statistiques</a>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?= $message_type ?>">
                <?php if ($message_type === 'success'): ?>
                    🎉 <?= htmlspecialchars($message) ?>
                    <?php if ($scan_result && isset($scan_result['character_name'])): ?>
                        <br><br>
                        <strong>Personnage découvert :</strong> <?= htmlspecialchars($scan_result['character_name']) ?>
                        <br>
                        <a href="dashboard.php" style="color: inherit; text-decoration: underline;">
                            → Voir votre progression
                        </a>
                    <?php endif; ?>
                <?php else: ?>
                    ❌ <?= htmlspecialchars($message) ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
            <!-- Formulaire de scan -->
            <div>
                <h3 style="color: #2c3e50;">📱 Scanner un QR Code</h3>

                <form method="POST">
                    <div class="form-group">
                        <label for="qr_code">Code QR ou Code du personnage :</label>
                        <input type="text" id="qr_code" name="qr_code"
                               placeholder="Ex: MARIE_CURIE_001"
                               value="<?= htmlspecialchars($_POST['qr_code'] ?? '') ?>"
                               style="font-family: monospace; font-size: 18px;"
                               required>
                        <small style="color: #7f8c8d; display: block; margin-top: 5px;">
                            Saisissez le code QR trouvé sur les affiches ou panneaux
                        </small>
                    </div>

                    <div class="text-center">
                        <button type="submit">🚀 Scanner / Valider</button>
                    </div>
                </form>

                <!-- Instructions -->
                <div class="info-box" style="margin-top: 30px;">
                    <h3>📖 Instructions</h3>
                    <ol>
                        <li>Trouvez un QR Code affiché dans l'espace de jeu</li>
                        <li>Scannez-le avec votre téléphone ou notez le code</li>
                        <li>Saisissez le code dans le champ ci-dessus</li>
                        <li>Validez pour découvrir le personnage !</li>
                    </ol>
                    <p><strong>Note :</strong> Chaque personnage ne peut être découvert qu'une seule fois par équipe.</p>
                </div>
            </div>

            <!-- Liste des codes disponibles -->
            <div>
                <h3 style="color: #2c3e50;">🎯 Personnages à Découvrir</h3>

                <div style="background: #f8f9fa; padding: 20px; border-radius: 10px;">
                    <?php foreach ($all_characters as $character): ?>
                        <?php $is_discovered = in_array($character['nom'], $discovered_character_names); ?>
                        <div style="margin-bottom: 15px; padding: 15px; background: white; border-radius: 8px; border-left: 5px solid <?= $is_discovered ? '#28a745' : '#dee2e6' ?>;">
                            <div style="display: flex; justify-content: between; align-items: center;">
                                <div style="flex-grow: 1;">
                                    <strong style="color: <?= $is_discovered ? '#28a745' : '#2c3e50' ?>;">
                                        <?= $is_discovered ? '✅' : '🔒' ?> <?= htmlspecialchars($character['nom']) ?>
                                    </strong>
                                    <br>
                                    <small style="color: #7f8c8d;">
                                        <?= htmlspecialchars($character['description']) ?>
                                    </small>
                                    <?php if (!$is_discovered): ?>
                                        <br>
                                        <code style="background: #e9ecef; padding: 2px 6px; border-radius: 3px; font-size: 12px;">
                                            Code: <?= htmlspecialchars($character['qr_code']) ?>
                                        </code>
                                    <?php endif; ?>
                                </div>
                                <div style="text-align: right; margin-left: 15px;">
                                    <?php if ($is_discovered): ?>
                                        <span style="color: #28a745; font-weight: bold;">DÉCOUVERT</span>
                                    <?php else: ?>
                                        <span style="background: #3498db; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px;">
                                            <?= $character['points'] ?> pts
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Progression -->
                <div style="margin-top: 20px; text-align: center;">
                    <?php
                    $discovered_count = count($discovered_character_names);
                    $total_count = count($all_characters);
                    $progress = ($discovered_count / $total_count) * 100;
                    ?>
                    <h4 style="color: #2c3e50;">📊 Votre Progression</h4>
                    <div class="progress-bar" style="margin: 10px 0;">
                        <div class="progress-fill" style="width: <?= $progress ?>%">
                            <?= $discovered_count ?>/<?= $total_count ?>
                        </div>
                    </div>
                    <p style="color: #7f8c8d;">
                        <?= round($progress) ?>% du jeu terminé
                    </p>

                    <?php if ($discovered_count >= $total_count): ?>
                        <div class="alert alert-success">
                            <strong>🏆 JEU TERMINÉ ! 🏆</strong><br>
                            Félicitations ! Vous avez découvert tous les personnages !
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-focus sur le champ de saisie
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('qr_code').focus();
        });

        // Transformation automatique en majuscules
        document.getElementById('qr_code').addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });

        // Raccourci clavier pour scanner rapidement
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'Enter') {
                document.querySelector('form').submit();
            }
        });

        // Animation de succès
        <?php if ($scan_result && $scan_result['success']): ?>
        setTimeout(function() {
            // Effet de confettis simple
            document.body.style.animation = 'shake 0.5s';
            setTimeout(() => {
                document.body.style.animation = '';
            }, 500);
        }, 500);

        // Style pour l'animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes shake {
                0%, 100% { transform: translateX(0); }
                25% { transform: translateX(-5px); }
                75% { transform: translateX(5px); }
            }
        `;
        document.head.appendChild(style);
        <?php endif; ?>
    </script>
</body>
</html>