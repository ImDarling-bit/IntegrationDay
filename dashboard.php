<?php
require_once 'config.php';
require_login();

$team_score = get_team_score($_SESSION['team_id']);
$team_scans = get_team_scans($_SESSION['team_id']);
$team_info = get_team_by_id($_SESSION['team_id']);
$discovery_stats = get_discovery_stats();
$recent_activity = get_recent_activity(5);

// Calculer la progression
$total_characters = 6;
$discovered_count = count($team_scans);
$progress_percentage = ($discovered_count / $total_characters) * 100;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Maria Curia WAMP</title>
    <link rel="stylesheet" href="style.css">
    <meta http-equiv="refresh" content="30"> <!-- Auto-refresh toutes les 30 secondes -->
</head>
<body>
    <div class="container">
        <div class="clearfix">
            <h1 class="title">🎪 Dashboard - <?= htmlspecialchars($_SESSION['nom_equipe']) ?></h1>
            <a href="logout.php" class="btn-logout button">Déconnexion</a>
        </div>

        <div class="nav">
            <a href="dashboard.php">🏠 Accueil</a>
            <a href="scanner.php">🔍 Scanner QR Code</a>
            <a href="leaderboard.php">🏆 Classement</a>
            <a href="stats.php">📊 Statistiques</a>
        </div>

        <!-- Score et progression -->
        <div class="score-box">
            <h2><?= $team_score ?> points</h2>
            <p>Personnages découverts : <?= $discovered_count ?>/<?= $total_characters ?></p>
            <div class="progress-bar">
                <div class="progress-fill" style="width: <?= $progress_percentage ?>%">
                    <?= round($progress_percentage) ?>%
                </div>
            </div>
        </div>

        <!-- Statistiques rapides -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3><?= $team_score ?></h3>
                <p>Points Total</p>
            </div>
            <div class="stat-card">
                <h3><?= $discovered_count ?></h3>
                <p>Découvertes</p>
            </div>
            <div class="stat-card">
                <h3><?= round($progress_percentage) ?>%</h3>
                <p>Progression</p>
            </div>
            <div class="stat-card">
                <h3><?= date('H:i') ?></h3>
                <p>Heure Actuelle</p>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
            <!-- Découvertes de l'équipe -->
            <div>
                <h3 style="color: #2c3e50;">🔬 Vos Découvertes Scientifiques</h3>

                <?php if (empty($team_scans)): ?>
                    <div class="alert alert-info">
                        <strong>🔍 Aucun personnage découvert pour le moment !</strong><br>
                        Commencez à scanner des QR codes pour découvrir les grands scientifiques de l'histoire.
                        <br><br>
                        <a href="scanner.php" style="color: #0c5460; font-weight: bold;">
                            → Commencer à scanner maintenant
                        </a>
                    </div>
                <?php else: ?>
                    <ul class="scan-list">
                        <?php foreach ($team_scans as $scan): ?>
                            <li>
                                <div class="scan-time"><?= date('d/m/Y H:i', strtotime($scan['scanned_at'])) ?></div>
                                <div class="character-name">🎭 <?= htmlspecialchars($scan['nom']) ?></div>
                                <div class="character-description"><?= htmlspecialchars($scan['description']) ?></div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <?php if ($discovered_count >= $total_characters): ?>
                    <div class="alert alert-success">
                        <strong>🎉 FÉLICITATIONS ! 🎉</strong><br>
                        Vous avez découvert tous les personnages scientifiques du cirque de Maria Curia !<br>
                        Votre équipe a terminé le jeu avec <strong><?= $team_score ?> points</strong> !
                    </div>
                <?php endif; ?>
            </div>

            <!-- Activité récente et infos -->
            <div>
                <h3 style="color: #2c3e50;">📈 Activité Récente</h3>

                <?php if (!empty($recent_activity)): ?>
                    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                        <?php foreach ($recent_activity as $activity): ?>
                            <div style="margin-bottom: 10px; padding: 8px; background: white; border-radius: 5px; border-left: 3px solid #3498db;">
                                <strong style="color: #2c3e50;"><?= htmlspecialchars($activity['nom_equipe']) ?></strong>
                                <br>
                                <small style="color: #7f8c8d;">
                                    a découvert <em><?= htmlspecialchars($activity['character_name']) ?></em>
                                    <br>
                                    <?= date('H:i', strtotime($activity['scanned_at'])) ?>
                                </small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <h3 style="color: #2c3e50;">🎯 Personnages Populaires</h3>
                <div style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                    <?php foreach ($discovery_stats as $stat): ?>
                        <div style="margin-bottom: 8px;">
                            <div style="display: flex; justify-content: between; align-items: center;">
                                <span style="font-weight: bold; color: #2c3e50;">
                                    <?= htmlspecialchars($stat['character_name']) ?>
                                </span>
                                <span style="color: #7f8c8d; font-size: 12px;">
                                    <?= $stat['discoveries'] ?> équipes
                                </span>
                            </div>
                            <div class="progress-bar" style="height: 8px; margin: 3px 0;">
                                <?php
                                $max_discoveries = max(array_column($discovery_stats, 'discoveries'));
                                $char_progress = $max_discoveries > 0 ? ($stat['discoveries'] / $max_discoveries) * 100 : 0;
                                ?>
                                <div class="progress-fill" style="width: <?= $char_progress ?>%; height: 100%; font-size: 0;"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div style="margin-top: 20px; text-align: center;">
                    <a href="scanner.php" class="btn-secondary" style="display: inline-block; padding: 10px 20px; text-decoration: none; color: white; border-radius: 5px;">
                        🔍 Scanner un QR Code
                    </a>
                </div>
            </div>
        </div>

        <!-- Informations équipe -->
        <div class="info-box" style="margin-top: 30px;">
            <h3>ℹ️ Informations de votre équipe</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                <div>
                    <strong>Nom de l'équipe :</strong><br>
                    <?= htmlspecialchars($team_info['nom_equipe']) ?>
                </div>
                <div>
                    <strong>Score actuel :</strong><br>
                    <?= $team_info['score'] ?> points
                </div>
                <div>
                    <strong>Inscrite le :</strong><br>
                    <?= date('d/m/Y à H:i', strtotime($team_info['created_at'])) ?>
                </div>
                <div>
                    <strong>Progression :</strong><br>
                    <?= $discovered_count ?>/<?= $total_characters ?> personnages (<?= round($progress_percentage) ?>%)
                </div>
            </div>
        </div>
    </div>

    <script>
        // Animation du titre
        document.addEventListener('DOMContentLoaded', function() {
            const title = document.querySelector('.title');
            if (title) {
                setInterval(() => {
                    title.style.transform = 'scale(1.05)';
                    setTimeout(() => {
                        title.style.transform = 'scale(1)';
                    }, 200);
                }, 3000);
            }
        });

        // Notification si nouveau scan
        const lastScanCount = localStorage.getItem('lastScanCount') || '0';
        const currentScanCount = '<?= $discovered_count ?>';

        if (parseInt(currentScanCount) > parseInt(lastScanCount)) {
            if (lastScanCount !== '0') {
                // Nouvelle découverte !
                setTimeout(() => {
                    alert('🎉 Nouvelle découverte enregistrée ! Score mis à jour.');
                }, 1000);
            }
        }

        localStorage.setItem('lastScanCount', currentScanCount);
    </script>
</body>
</html>