<?php
require_once 'config.php';
require_admin_login();

$stats = get_game_stats();
$teams = get_all_teams();
$admin_logs = get_admin_logs(20);

// Traitement des actions
if ($_POST) {
    $action = $_POST['action'] ?? '';
    $team_id = $_POST['team_id'] ?? '';
    $points = $_POST['points'] ?? '';

    switch ($action) {
        case 'reset_team':
            if ($team_id) {
                reset_team_progress($team_id);
                $message = "✅ Progression de l'équipe réinitialisée";
            }
            break;

        case 'reset_all':
            reset_all_progress();
            $message = "✅ Toutes les progressions réinitialisées";
            break;

        case 'add_points':
            if ($team_id && $points) {
                add_points_to_team($team_id, $points);
                $message = "✅ Points ajoutés à l'équipe";
            }
            break;
    }

    // Recharger les données après modification
    if (isset($message)) {
        $stats = get_game_stats();
        $teams = get_all_teams();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Maria Curia</title>
    <link rel="stylesheet" href="style.css">
    <meta http-equiv="refresh" content="30">
</head>

<body>
    <div class="admin-container">
        <!-- En-tête Admin -->
        <div class="admin-header">
            <div>
                <h1 class="admin-title">🔓 ADMIN CONTROL CENTER</h1>
                <div class="admin-user">
                    Connecté en tant que: <strong><?= htmlspecialchars($_SESSION['admin_username']) ?></strong>
                    (<?= htmlspecialchars($_SESSION['admin_role']) ?>)
                </div>
            </div>
            <a href="logout.php" class="logout-btn">🚪 Déconnexion</a>
        </div>

        <!-- Navigation -->
        <div class="admin-nav">
            <a href="dashboard.php" class="nav-btn active">🏠 Dashboard</a>
            <a href="teams.php" class="nav-btn">👥 Équipes</a>
            <a href="monitoring.php" class="nav-btn">📊 Monitoring</a>
            <a href="logs.php" class="nav-btn">📋 Logs</a>
        </div>

        <?php if (isset($message)): ?>
            <div
                style="background: rgba(0, 255, 0, 0.2); border: 2px solid #00ff00; padding: 15px; border-radius: 10px; margin-bottom: 20px; text-align: center; color: #00ff00;">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <!-- Statistiques Générales -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3><?= $stats['total_teams'] ?></h3>
                <p>👥 Équipes Inscrites</p>
            </div>
            <div class="stat-card">
                <h3><?= $stats['total_scans'] ?></h3>
                <p>🔍 Scans Totaux</p>
            </div>
            <div class="stat-card">
                <h3><?= count($stats['recent_activity']) ?></h3>
                <p>⚡ Activités Récentes</p>
            </div>
            <div class="stat-card">
                <h3><?= date('H:i:s') ?></h3>
                <p>🕒 Dernière MAJ</p>
            </div>
        </div>

        <!-- Actions Rapides -->
        <div style="margin-bottom: 30px;">
            <h2 style="color: #ff0000; margin-bottom: 15px;">⚡ Actions Rapides</h2>
            <div class="quick-actions">
                <form method="POST" style="display: contents;"
                    onsubmit="return confirm('⚠️ Êtes-vous sûr de vouloir réinitialiser TOUTES les progressions ?')">
                    <input type="hidden" name="action" value="reset_all">
                    <button type="submit" class="action-btn danger">
                        💥 RESET GÉNÉRAL
                    </button>
                </form>

                <button onclick="refreshPage()" class="action-btn success">
                    🔄 ACTUALISER
                </button>

                <button onclick="toggleMonitoring()" class="action-btn" id="monitoring-btn">
                    📡 MONITORING AUTO
                </button>

                <button onclick="exportData()" class="action-btn">
                    💾 EXPORTER DONNÉES
                </button>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
            <!-- Gestion des Équipes -->
            <div>
                <h2 style="color: #ff0000; margin-bottom: 15px;">👥 Gestion des Équipes</h2>
                <div class="data-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Équipe</th>
                                <th>Score</th>
                                <th>Scans</th>
                                <th>Progression</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($teams as $team): ?>
                                <tr>
                                    <td>
                                        <strong style="color: #00ff00;">
                                            <?= htmlspecialchars($team['nom_equipe']) ?>
                                        </strong>
                                    </td>
                                    <td style="color: #ffff00;">
                                        <?= $team['total_score'] ?> pts
                                    </td>
                                    <td>
                                        <?= $team['scans_count'] ?>/6
                                    </td>
                                    <td>
                                        <div style="background: #333; height: 10px; border-radius: 5px; overflow: hidden;">
                                            <div
                                                style="background: linear-gradient(to right, #00ff00, #ffff00); height: 100%; width: <?= ($team['scans_count'] / 6) * 100 ?>%; transition: width 0.3s;">
                                            </div>
                                        </div>
                                        <?= round(($team['scans_count'] / 6) * 100) ?>%
                                    </td>
                                    <td>
                                        <div style="display: flex; gap: 5px;">
                                            <form method="POST" style="display: inline;"
                                                onsubmit="return confirm('Reset cette équipe ?')">
                                                <input type="hidden" name="action" value="reset_team">
                                                <input type="hidden" name="team_id" value="<?= $team['id'] ?>">
                                                <button type="submit"
                                                    style="background: #ff4444; color: white; border: none; padding: 5px 10px; border-radius: 3px; cursor: pointer; font-size: 12px;">
                                                    🗑️
                                                </button>
                                            </form>

                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="add_points">
                                                <input type="hidden" name="team_id" value="<?= $team['id'] ?>">
                                                <select name="points"
                                                    style="background: #000; color: #00ff00; border: 1px solid #00ff00; padding: 3px; font-size: 12px;">
                                                    <option value="100">+100pts</option>
                                                    <option value="200">+200pts</option>
                                                    <option value="300">+300pts</option>
                                                </select>
                                                <button type="submit"
                                                    style="background: #00ff00; color: #000; border: none; padding: 5px 8px; border-radius: 3px; cursor: pointer; font-size: 12px;">
                                                    +
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Activité en Temps Réel -->
            <div>
                <h2 style="color: #ff0000; margin-bottom: 15px;">⚡ Activité Temps Réel</h2>
                <div
                    style="background: rgba(0, 0, 0, 0.8); border: 2px solid #00ff00; border-radius: 10px; padding: 20px; max-height: 400px; overflow-y: auto;">
                    <?php if (!empty($stats['recent_activity'])): ?>
                        <?php foreach ($stats['recent_activity'] as $index => $activity): ?>
                            <div
                                style="margin-bottom: 12px; padding: 10px; background: rgba(0, 255, 0, 0.1); border-left: 4px solid #00ff00; border-radius: 5px;">
                                <div style="font-weight: bold; color: #00ff00; font-size: 14px;">
                                    <?= htmlspecialchars($activity['nom_equipe']) ?>
                                </div>
                                <div style="color: #ffff00; font-size: 12px;">
                                    → <?= htmlspecialchars($activity['character_name']) ?>
                                </div>
                                <div style="color: #888; font-size: 11px;">
                                    <?= date('H:i:s', strtotime($activity['scanned_at'])) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="text-align: center; color: #888; font-style: italic;">
                            Aucune activité récente
                        </p>
                    <?php endif; ?>
                </div>

                <!-- Logs Admin Récents -->
                <h3 style="color: #ffff00; margin-top: 30px; margin-bottom: 15px;">📋 Logs Admin</h3>
                <div
                    style="background: rgba(0, 0, 0, 0.8); border: 2px solid #ffff00; border-radius: 10px; padding: 15px; max-height: 300px; overflow-y: auto;">
                    <?php foreach (array_slice($admin_logs, 0, 10) as $log): ?>
                        <div
                            style="margin-bottom: 8px; padding: 8px; background: rgba(255, 255, 0, 0.1); border-radius: 3px; font-size: 12px;">
                            <div style="color: #ffff00;">
                                <strong><?= htmlspecialchars($log['username']) ?></strong>
                                → <?= htmlspecialchars($log['action']) ?>
                            </div>
                            <?php if ($log['details']): ?>
                                <div style="color: #ccc; margin-top: 3px;">
                                    <?= htmlspecialchars($log['details']) ?>
                                </div>
                            <?php endif; ?>
                            <div style="color: #888; font-size: 10px;">
                                <?= date('H:i:s', strtotime($log['created_at'])) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Informations Système -->
        <div
            style="margin-top: 30px; padding: 20px; background: rgba(0, 0, 0, 0.8); border: 2px solid #444; border-radius: 10px;">
            <h3 style="color: #888; margin-bottom: 15px;">⚙️ Informations Système</h3>
            <div
                style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; font-size: 14px; color: #ccc;">
                <div>
                    <strong>Serveur :</strong><br>
                    <?= $_SERVER['SERVER_NAME'] ?><?= $_SERVER['SERVER_PORT'] != 80 ? ':' . $_SERVER['SERVER_PORT'] : '' ?>
                </div>
                <div>
                    <strong>Version PHP :</strong><br>
                    <?= PHP_VERSION ?>
                </div>
                <div>
                    <strong>Base de données :</strong><br>
                    <?php $mysqli = new mysqli("localhost", "root", "", "maria_curia");
                    echo $mysqli->server_info; // Affiche la version du serveur MySQL?>
                </div>
                <div>
                    <strong>Session Admin :</strong><br>
                    <?= session_id() ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        let monitoringActive = false;
        let refreshInterval;

        function refreshPage() {
            window.location.reload();
        }

        function toggleMonitoring() {
            const btn = document.getElementById('monitoring-btn');

            if (monitoringActive) {
                clearInterval(refreshInterval);
                monitoringActive = false;
                btn.textContent = '📡 MONITORING AUTO';
                btn.style.background = 'linear-gradient(45deg, #ff4444, #ff0000)';
            } else {
                refreshInterval = setInterval(refreshPage, 10000); // Refresh toutes les 10 secondes
                monitoringActive = true;
                btn.textContent = '🔴 MONITORING ACTIF';
                btn.style.background = 'linear-gradient(45deg, #00ff00, #00cc00)';
                btn.style.color = '#000';
            }
        }

        function exportData() {
            // Simuler un export
            const data = {
                timestamp: new Date().toISOString(),
                teams: <?= json_encode($teams) ?>,
                stats: <?= json_encode($stats) ?>
            };

            const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'maria-curia-export-' + Date.now() + '.json';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }

        // Animation des cartes de stats
        document.addEventListener('DOMContentLoaded', function () {
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach((card, index) => {
                card.style.animation = `fadeIn 0.6s ease-out ${index * 0.2}s both`;
            });

            // Effet de glow sur les éléments importants
            const importantElements = document.querySelectorAll('.admin-title, .stat-card h3');
            importantElements.forEach(el => {
                el.classList.add('matrix-text');
            });
        });

        // Notification de nouvelles activités
        let lastActivityCount = <?= count($stats['recent_activity']) ?>;

        setInterval(() => {
            fetch('get_activity_count.php')
                .then(response => response.json())
                .then(data => {
                    if (data.count > lastActivityCount) {
                        // Nouvelle activité détectée
                        const notification = document.createElement('div');
                        notification.innerHTML = '🚨 NOUVELLE ACTIVITÉ DÉTECTÉE';
                        notification.style.cssText = `
                            position: fixed;
                            top: 20px;
                            right: 20px;
                            background: rgba(255, 0, 0, 0.9);
                            color: white;
                            padding: 15px;
                            border-radius: 5px;
                            z-index: 1000;
                            animation: pulse 1s infinite;
                        `;
                        document.body.appendChild(notification);

                        setTimeout(() => {
                            document.body.removeChild(notification);
                        }, 3000);

                        lastActivityCount = data.count;
                    }
                })
                .catch(console.error);
        }, 5000);

        // Style pour l'animation fadeIn
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }
            @keyframes pulse {
                0%, 100% { transform: scale(1); }
                50% { transform: scale(1.05); }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>

</html>