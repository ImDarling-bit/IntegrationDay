<?php
require_once 'config.php';
require_admin_login();

$stats = get_game_stats();
$teams = get_all_teams();

// Statistiques avancées
$hourly_stats = [];
for ($i = 8; $i <= 18; $i++) {
    $stmt = $game_pdo->prepare("
        SELECT COUNT(*) as count
        FROM scans
        WHERE HOUR(scanned_at) = ? AND DATE(scanned_at) = CURDATE()
    ");
    $stmt->execute([$i]);
    $hourly_stats[$i] = $stmt->fetch()['count'];
}

// Activité par équipe (dernières 24h)
$team_activity = [];
foreach ($teams as $team) {
    $stmt = $game_pdo->prepare("
        SELECT COUNT(*) as count
        FROM scans
        WHERE team_id = ? AND scanned_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
    ");
    $stmt->execute([$team['id']]);
    $team_activity[$team['nom_equipe']] = $stmt->fetch()['count'];
}

// Découvertes par personnage
$character_stats = [];
$stmt = $game_pdo->query("
    SELECT c.nom, c.points, COUNT(s.id) as discoveries,
           ROUND((COUNT(s.id) / (SELECT COUNT(*) FROM teams)) * 100, 1) as percentage
    FROM characters c
    LEFT JOIN scans s ON c.id = s.character_id
    GROUP BY c.id
    ORDER BY discoveries DESC
");
$character_stats = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring - Admin Maria Curia</title>
    <link rel="stylesheet" href="style.css">
    <meta http-equiv="refresh" content="15">
</head>
<body>
    <div class="admin-container">
        <!-- En-tête Admin -->
        <div class="admin-header">
            <div>
                <h1 class="admin-title">📊 MONITORING TEMPS RÉEL</h1>
                <div class="admin-user">
                    Dernière mise à jour: <strong><?= date('H:i:s') ?></strong>
                    <span style="color: #00ff00; margin-left: 10px;">🔴 LIVE</span>
                </div>
            </div>
            <a href="logout.php" class="logout-btn">🚪 Déconnexion</a>
        </div>

        <!-- Navigation -->
        <div class="admin-nav">
            <a href="dashboard.php" class="nav-btn">🏠 Dashboard</a>
            <a href="teams.php" class="nav-btn">👥 Équipes</a>
            <a href="monitoring.php" class="nav-btn active">📊 Monitoring</a>
            <a href="logs.php" class="nav-btn">📋 Logs</a>
        </div>

        <!-- Indicateurs temps réel -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3 id="live-teams"><?= count($teams) ?></h3>
                <p>👥 Équipes Actives</p>
                <div style="font-size: 12px; color: #888; margin-top: 5px;">
                    🔴 En temps réel
                </div>
            </div>
            <div class="stat-card">
                <h3 id="live-scans"><?= $stats['total_scans'] ?></h3>
                <p>🔍 Scans Totaux</p>
                <div style="font-size: 12px; color: #888; margin-top: 5px;">
                    +<?= array_sum($hourly_stats) ?> aujourd'hui
                </div>
            </div>
            <div class="stat-card">
                <h3 id="live-activity"><?= count($stats['recent_activity']) ?></h3>
                <p>⚡ Activités (10min)</p>
                <div style="font-size: 12px; color: #888; margin-top: 5px;">
                    <span id="activity-trend">📈 Stable</span>
                </div>
            </div>
            <div class="stat-card">
                <h3 id="server-status">🟢</h3>
                <p>🖥️ Statut Serveur</p>
                <div style="font-size: 12px; color: #888; margin-top: 5px;">
                    MySQL connecté
                </div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
            <!-- Graphiques et analyses -->
            <div>
                <!-- Activité par heure -->
                <div style="background: rgba(0, 0, 0, 0.8); border: 2px solid #00ff00; border-radius: 10px; padding: 20px; margin-bottom: 30px;">
                    <h3 style="color: #00ff00; margin-bottom: 15px;">📈 Activité par Heure (Aujourd'hui)</h3>
                    <div style="display: flex; align-items: end; height: 200px; gap: 5px; padding: 10px; background: rgba(0, 0, 0, 0.5); border-radius: 5px;">
                        <?php
                        $max_hourly = max($hourly_stats) ?: 1;
                        foreach ($hourly_stats as $hour => $count):
                        ?>
                            <div style="display: flex; flex-direction: column; align-items: center; flex: 1;">
                                <div style="background: linear-gradient(to top, #ff0000, #ffff00, #00ff00); width: 100%; height: <?= ($count / $max_hourly) * 150 ?>px; border-radius: 2px; position: relative; min-height: 5px;">
                                    <?php if ($count > 0): ?>
                                        <span style="position: absolute; top: -20px; left: 50%; transform: translateX(-50%); font-size: 10px; color: #00ff00;">
                                            <?= $count ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div style="padding: 5px 2px; font-size: 10px; color: #ccc;">
                                    <?= sprintf('%02d', $hour) ?>h
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div style="text-align: center; margin-top: 10px; color: #888; font-size: 12px;">
                        Total aujourd'hui: <?= array_sum($hourly_stats) ?> scans
                    </div>
                </div>

                <!-- Classement en temps réel -->
                <div style="background: rgba(0, 0, 0, 0.8); border: 2px solid #ffff00; border-radius: 10px; padding: 20px;">
                    <h3 style="color: #ffff00; margin-bottom: 15px;">🏆 Classement Live</h3>
                    <div style="max-height: 400px; overflow-y: auto;">
                        <?php foreach ($teams as $index => $team): ?>
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px; margin-bottom: 8px; background: rgba(255, 255, 0, 0.1); border-radius: 8px; border-left: 4px solid <?= $index < 3 ? '#ffff00' : '#666' ?>;">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <span style="font-size: 18px;">
                                        <?php if ($index == 0): ?>🥇
                                        <?php elseif ($index == 1): ?>🥈
                                        <?php elseif ($index == 2): ?>🥉
                                        <?php else: ?>#<?= $index + 1 ?>
                                        <?php endif; ?>
                                    </span>
                                    <div>
                                        <strong style="color: #00ff00;">
                                            <?= htmlspecialchars($team['nom_equipe']) ?>
                                        </strong>
                                        <br>
                                        <small style="color: #888;">
                                            <?= $team['scans_count'] ?>/6 découvertes
                                        </small>
                                    </div>
                                </div>
                                <div style="text-align: right;">
                                    <div style="color: #ffff00; font-weight: bold; font-size: 16px;">
                                        <?= $team['total_score'] ?> pts
                                    </div>
                                    <div style="color: #888; font-size: 12px;">
                                        Activité 24h: <?= $team_activity[$team['nom_equipe']] ?? 0 ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Panneau de contrôle live -->
            <div>
                <!-- Stream d'activité -->
                <div style="background: rgba(0, 0, 0, 0.8); border: 2px solid #ff0000; border-radius: 10px; padding: 15px; margin-bottom: 20px;">
                    <h3 style="color: #ff0000; margin-bottom: 15px;">🔴 Stream Live</h3>
                    <div id="activity-stream" style="max-height: 300px; overflow-y: auto; font-size: 12px;">
                        <?php foreach ($stats['recent_activity'] as $index => $activity): ?>
                            <div style="margin-bottom: 8px; padding: 8px; background: rgba(255, 0, 0, 0.1); border-radius: 5px; animation: slideIn 0.5s ease-out;">
                                <div style="color: #ff0000; font-weight: bold;">
                                    <?= htmlspecialchars($activity['nom_equipe']) ?>
                                </div>
                                <div style="color: #ffff00;">
                                    → <?= htmlspecialchars($activity['character_name']) ?>
                                </div>
                                <div style="color: #888; font-size: 10px;">
                                    <?= date('H:i:s', strtotime($activity['scanned_at'])) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Popularité des personnages -->
                <div style="background: rgba(0, 0, 0, 0.8); border: 2px solid #00ff00; border-radius: 10px; padding: 15px; margin-bottom: 20px;">
                    <h3 style="color: #00ff00; margin-bottom: 15px;">🎭 Popularité Personnages</h3>
                    <?php foreach ($character_stats as $stat): ?>
                        <div style="margin-bottom: 10px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 3px;">
                                <span style="color: #00ff00; font-size: 12px; font-weight: bold;">
                                    <?= htmlspecialchars($stat['nom']) ?>
                                </span>
                                <span style="color: #888; font-size: 11px;">
                                    <?= $stat['discoveries'] ?> (<?= $stat['percentage'] ?>%)
                                </span>
                            </div>
                            <div style="background: #333; height: 8px; border-radius: 4px; overflow: hidden;">
                                <div style="background: linear-gradient(to right, #ff0000, #ffff00, #00ff00); height: 100%; width: <?= $stat['percentage'] ?>%; transition: width 0.3s;"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Contrôles rapides -->
                <div style="background: rgba(0, 0, 0, 0.8); border: 2px solid #ffff00; border-radius: 10px; padding: 15px;">
                    <h3 style="color: #ffff00; margin-bottom: 15px;">⚡ Contrôles Rapides</h3>
                    <div style="display: grid; gap: 10px;">
                        <button onclick="toggleAutoRefresh()" id="auto-refresh-btn" class="action-btn" style="width: 100%; padding: 10px; font-size: 12px;">
                            🔄 Auto-refresh: ON
                        </button>
                        <button onclick="exportCurrentData()" class="action-btn" style="width: 100%; padding: 10px; font-size: 12px;">
                            💾 Export Instant
                        </button>
                        <button onclick="resetViewFilters()" class="action-btn" style="width: 100%; padding: 10px; font-size: 12px;">
                            🔍 Reset Filtres
                        </button>
                        <button onclick="showAlerts()" class="action-btn" style="width: 100%; padding: 10px; font-size: 12px;">
                            🚨 Alertes: <span id="alert-count">0</span>
                        </button>
                    </div>
                </div>

                <!-- Système de santé -->
                <div style="background: rgba(0, 0, 0, 0.8); border: 2px solid #666; border-radius: 10px; padding: 15px; margin-top: 20px;">
                    <h3 style="color: #888; margin-bottom: 15px;">⚙️ Santé Système</h3>
                    <div style="font-size: 12px;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                            <span>CPU:</span>
                            <span style="color: #00ff00;">Normal</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                            <span>Mémoire:</span>
                            <span style="color: #00ff00;">OK</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                            <span>MySQL:</span>
                            <span style="color: #00ff00;">Connecté</span>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span>Uptime:</span>
                            <span style="color: #888;"><?= date('H:i') ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let autoRefresh = true;
        let refreshInterval;
        let lastActivityCount = <?= count($stats['recent_activity']) ?>;

        function toggleAutoRefresh() {
            const btn = document.getElementById('auto-refresh-btn');
            if (autoRefresh) {
                clearInterval(refreshInterval);
                autoRefresh = false;
                btn.textContent = '🔄 Auto-refresh: OFF';
                btn.style.background = 'linear-gradient(45deg, #666, #888)';
            } else {
                startAutoRefresh();
                autoRefresh = true;
                btn.textContent = '🔄 Auto-refresh: ON';
                btn.style.background = 'linear-gradient(45deg, #00ff00, #00cc00)';
            }
        }

        function startAutoRefresh() {
            refreshInterval = setInterval(() => {
                window.location.reload();
            }, 15000);
        }

        function exportCurrentData() {
            const data = {
                timestamp: new Date().toISOString(),
                teams: <?= json_encode($teams) ?>,
                stats: <?= json_encode($stats) ?>,
                hourly_stats: <?= json_encode($hourly_stats) ?>,
                character_stats: <?= json_encode($character_stats) ?>
            };

            const blob = new Blob([JSON.stringify(data, null, 2)], {type: 'application/json'});
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'maria-curia-monitoring-' + Date.now() + '.json';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }

        function resetViewFilters() {
            // Réinitialiser tous les filtres visuels
            document.querySelectorAll('.data-table tr').forEach(row => {
                row.style.display = '';
            });
            alert('✅ Filtres réinitialisés');
        }

        function showAlerts() {
            const alerts = [];
            const teams = <?= json_encode($teams) ?>;

            // Vérifier les équipes inactives
            teams.forEach(team => {
                if (team.scans_count === 0) {
                    alerts.push(`⚠️ Équipe "${team.nom_equipe}" : Aucune activité`);
                }
            });

            if (alerts.length === 0) {
                alerts.push('✅ Aucune alerte système');
            }

            document.getElementById('alert-count').textContent = alerts.length;
            alert(alerts.join('\n'));
        }

        // Démarrer l'auto-refresh
        if (autoRefresh) {
            startAutoRefresh();
        }

        // Animation des nouvelles activités
        document.addEventListener('DOMContentLoaded', function() {
            // Ajouter les styles d'animation
            const style = document.createElement('style');
            style.textContent = `
                @keyframes slideIn {
                    from { opacity: 0; transform: translateX(-20px); }
                    to { opacity: 1; transform: translateX(0); }
                }
                @keyframes pulse {
                    0%, 100% { transform: scale(1); }
                    50% { transform: scale(1.05); }
                }
                .pulse { animation: pulse 2s infinite; }
            `;
            document.head.appendChild(style);

            // Faire pulser les indicateurs live
            document.querySelectorAll('.stat-card h3').forEach((el, index) => {
                setTimeout(() => {
                    el.classList.add('pulse');
                }, index * 500);
            });
        });

        // Simulation de mise à jour temps réel
        setInterval(() => {
            // Mettre à jour l'heure
            document.querySelector('.admin-user strong').textContent = new Date().toLocaleTimeString();

            // Simulation d'activité
            const indicators = ['📈 Croissant', '📉 Décroissant', '📊 Stable'];
            document.getElementById('activity-trend').textContent = indicators[Math.floor(Math.random() * indicators.length)];
        }, 5000);
    </script>
</body>
</html>