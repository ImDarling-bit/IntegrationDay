<?php
require_once 'config.php';
require_login();

$discovery_stats = get_discovery_stats();
$recent_activity = get_recent_activity(20);
$leaderboard = get_leaderboard();

// Calculer des statistiques avancées
$total_scans = 0;
$stmt = $pdo->query("SELECT COUNT(*) as total FROM scans");
$result = $stmt->fetch();
$total_scans = $result['total'];

$avg_score = 0;
if (!empty($leaderboard)) {
    $avg_score = array_sum(array_column($leaderboard, 'score')) / count($leaderboard);
}

// Statistiques par heure (simulées)
$hourly_stats = [];
for ($i = 8; $i <= 18; $i++) {
    $hourly_stats[$i] = rand(0, 15); // Simulation
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques - Maria Curia WAMP</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="clearfix">
            <h1 class="title">📊 Statistiques du Jeu</h1>
            <a href="logout.php" class="btn-logout button">Déconnexion</a>
        </div>

        <div class="nav">
            <a href="dashboard.php">🏠 Accueil</a>
            <a href="scanner.php">🔍 Scanner QR Code</a>
            <a href="leaderboard.php">🏆 Classement</a>
            <a href="stats.php">📊 Statistiques</a>
        </div>

        <!-- Statistiques générales -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3><?= count($leaderboard) ?></h3>
                <p>Équipes Inscrites</p>
            </div>
            <div class="stat-card">
                <h3><?= $total_scans ?></h3>
                <p>Scans Totaux</p>
            </div>
            <div class="stat-card">
                <h3><?= round($avg_score) ?></h3>
                <p>Score Moyen</p>
            </div>
            <div class="stat-card">
                <h3><?= round(($total_scans / (count($leaderboard) * 6)) * 100) ?>%</h3>
                <p>Progression Globale</p>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
            <!-- Popularité des personnages -->
            <div>
                <h3 style="color: #2c3e50;">🎭 Popularité des Personnages</h3>
                <div style="background: #f8f9fa; padding: 20px; border-radius: 10px;">
                    <?php
                    $max_discoveries = max(array_column($discovery_stats, 'discoveries'));
                    foreach ($discovery_stats as $stat):
                    ?>
                        <div style="margin-bottom: 15px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                                <span style="font-weight: bold; color: #2c3e50;">
                                    <?= htmlspecialchars($stat['character_name']) ?>
                                </span>
                                <span style="color: #7f8c8d; font-size: 14px;">
                                    <?= $stat['discoveries'] ?> découvertes
                                </span>
                            </div>
                            <div class="progress-bar" style="height: 20px;">
                                <?php
                                $percentage = $max_discoveries > 0 ? ($stat['discoveries'] / $max_discoveries) * 100 : 0;
                                ?>
                                <div class="progress-fill" style="width: <?= $percentage ?>%">
                                    <?= round($percentage) ?>%
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Activité par équipe -->
            <div>
                <h3 style="color: #2c3e50;">🏃 Activité des Équipes</h3>
                <div style="background: #f8f9fa; padding: 20px; border-radius: 10px;">
                    <?php foreach ($leaderboard as $team): ?>
                        <?php
                        // Compter les scans de cette équipe
                        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM scans WHERE team_id = (SELECT id FROM teams WHERE nom_equipe = ?)");
                        $stmt->execute([$team['nom_equipe']]);
                        $team_scans_count = $stmt->fetch()['count'];
                        ?>
                        <div style="margin-bottom: 15px; padding: 12px; background: white; border-radius: 8px;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <strong style="color: #2c3e50;">
                                        <?= htmlspecialchars($team['nom_equipe']) ?>
                                    </strong>
                                    <br>
                                    <small style="color: #7f8c8d;">
                                        <?= $team['score'] ?> points
                                    </small>
                                </div>
                                <div style="text-align: right;">
                                    <span style="background: #3498db; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px;">
                                        <?= $team_scans_count ?> scans
                                    </span>
                                </div>
                            </div>
                            <div class="progress-bar" style="height: 8px; margin-top: 8px;">
                                <div class="progress-fill" style="width: <?= ($team_scans_count / 6) * 100 ?>%; font-size: 0;"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Graphique d'activité par heure -->
        <div style="margin-top: 30px;">
            <h3 style="color: #2c3e50;">⏰ Activité par Heure (Simulation)</h3>
            <div style="background: #f8f9fa; padding: 20px; border-radius: 10px;">
                <div style="display: flex; align-items: end; height: 200px; gap: 10px;">
                    <?php
                    $max_hourly = max($hourly_stats);
                    foreach ($hourly_stats as $hour => $count):
                    ?>
                        <div style="display: flex; flex-direction: column; align-items: center; flex: 1;">
                            <div style="background: #3498db; width: 100%; height: <?= $max_hourly > 0 ? ($count / $max_hourly) * 150 : 0 ?>px; border-radius: 4px 4px 0 0; display: flex; align-items: end; justify-content: center; color: white; font-size: 10px; padding: 2px;">
                                <?= $count ?>
                            </div>
                            <div style="padding: 8px 4px; font-size: 12px; font-weight: bold; color: #2c3e50;">
                                <?= sprintf('%02d', $hour) ?>h
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <p style="text-align: center; color: #7f8c8d; margin-top: 15px;">
                    Nombre de scans par heure de la journée
                </p>
            </div>
        </div>

        <!-- Timeline des découvertes -->
        <div style="margin-top: 30px;">
            <h3 style="color: #2c3e50;">⏱️ Timeline des Découvertes</h3>
            <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; max-height: 400px; overflow-y: auto;">
                <?php if (!empty($recent_activity)): ?>
                    <?php foreach ($recent_activity as $index => $activity): ?>
                        <div style="margin-bottom: 12px; padding: 12px; background: white; border-radius: 8px; border-left: 4px solid
                            <?php
                            $colors = ['#e74c3c', '#3498db', '#2ecc71', '#f39c12', '#9b59b6'];
                            echo $colors[$index % count($colors)];
                            ?>;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <strong style="color: #2c3e50;">
                                        <?= htmlspecialchars($activity['nom_equipe']) ?>
                                    </strong>
                                    a découvert
                                    <strong style="color: #e74c3c;">
                                        <?= htmlspecialchars($activity['character_name']) ?>
                                    </strong>
                                </div>
                                <div style="color: #7f8c8d; font-size: 12px;">
                                    <?= date('H:i:s', strtotime($activity['scanned_at'])) ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="text-align: center; color: #7f8c8d; font-style: italic;">
                        Aucune activité enregistrée pour le moment
                    </p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Informations techniques -->
        <div class="info-box" style="margin-top: 30px;">
            <h3>⚙️ Informations Techniques</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                <div>
                    <strong>Base de données :</strong><br>
                    MySQL via WAMP
                </div>
                <div>
                    <strong>Serveur :</strong><br>
                    <?= $_SERVER['SERVER_NAME'] ?><?= $_SERVER['SERVER_PORT'] != 80 ? ':' . $_SERVER['SERVER_PORT'] : '' ?>
                </div>
                <div>
                    <strong>Version PHP :</strong><br>
                    <?= PHP_VERSION ?>
                </div>
                <div>
                    <strong>Mise à jour :</strong><br>
                    <?= date('d/m/Y H:i:s') ?>
                </div>
            </div>
        </div>

        <div style="text-align: center; margin-top: 30px;">
            <button onclick="window.location.reload()" class="btn-secondary">
                🔄 Actualiser les Statistiques
            </button>
        </div>
    </div>

    <script>
        // Animation des barres de progression
        document.addEventListener('DOMContentLoaded', function() {
            const progressBars = document.querySelectorAll('.progress-fill');
            progressBars.forEach((bar, index) => {
                const width = bar.style.width;
                bar.style.width = '0%';
                setTimeout(() => {
                    bar.style.transition = 'width 1s ease-out';
                    bar.style.width = width;
                }, index * 100);
            });

            // Animation des barres du graphique
            const chartBars = document.querySelectorAll('[style*="background: #3498db"]');
            chartBars.forEach((bar, index) => {
                const originalHeight = bar.style.height;
                bar.style.height = '0px';
                setTimeout(() => {
                    bar.style.transition = 'height 0.8s ease-out';
                    bar.style.height = originalHeight;
                }, 500 + (index * 100));
            });
        });

        // Auto-refresh toutes les 30 secondes
        setTimeout(() => {
            window.location.reload();
        }, 30000);
    </script>
</body>
</html>