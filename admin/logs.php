<?php
require_once 'config.php';
require_admin_login();

$admin_logs = get_admin_logs(100);

// Filtres
$filter_action = $_GET['action'] ?? '';
$filter_admin = $_GET['admin'] ?? '';

if ($filter_action) {
    $admin_logs = array_filter($admin_logs, function($log) use ($filter_action) {
        return strpos($log['action'], $filter_action) !== false;
    });
}

if ($filter_admin) {
    $admin_logs = array_filter($admin_logs, function($log) use ($filter_admin) {
        return $log['username'] === $filter_admin;
    });
}

// Statistiques des logs
$total_logs = count($admin_logs);
$actions_count = [];
$admins_count = [];

foreach ($admin_logs as $log) {
    $actions_count[$log['action']] = ($actions_count[$log['action']] ?? 0) + 1;
    $admins_count[$log['username']] = ($admins_count[$log['username']] ?? 0) + 1;
}

arsort($actions_count);
arsort($admins_count);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logs Admin - Maria Curia</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="admin-container">
        <!-- En-tête Admin -->
        <div class="admin-header">
            <div>
                <h1 class="admin-title">📋 LOGS ADMINISTRATEUR</h1>
                <div class="admin-user">
                    Total: <strong><?= $total_logs ?></strong> entrées
                    <span style="margin-left: 15px;">Dernière activité: <strong><?= !empty($admin_logs) ? date('H:i:s', strtotime($admin_logs[0]['created_at'])) : 'Aucune' ?></strong></span>
                </div>
            </div>
            <a href="logout.php" class="logout-btn">🚪 Déconnexion</a>
        </div>

        <!-- Navigation -->
        <div class="admin-nav">
            <a href="dashboard.php" class="nav-btn">🏠 Dashboard</a>
            <a href="teams.php" class="nav-btn">👥 Équipes</a>
            <a href="monitoring.php" class="nav-btn">📊 Monitoring</a>
            <a href="logs.php" class="nav-btn active">📋 Logs</a>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 300px; gap: 30px;">
            <!-- Liste des logs -->
            <div>
                <!-- Filtres -->
                <div style="background: rgba(0, 0, 0, 0.8); border: 2px solid #00ff00; border-radius: 10px; padding: 20px; margin-bottom: 20px;">
                    <h3 style="color: #00ff00; margin-bottom: 15px;">🔍 Filtres</h3>
                    <form method="GET" style="display: grid; grid-template-columns: 1fr 1fr auto; gap: 15px; align-items: end;">
                        <div>
                            <label style="color: #00ff00; display: block; margin-bottom: 5px;">Action :</label>
                            <select name="action" style="width: 100%; padding: 8px; background: rgba(0, 0, 0, 0.8); border: 2px solid #00ff00; border-radius: 5px; color: #00ff00; font-family: 'Courier New', monospace;">
                                <option value="">Toutes les actions</option>
                                <?php foreach (array_keys($actions_count) as $action): ?>
                                    <option value="<?= htmlspecialchars($action) ?>" <?= $filter_action === $action ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($action) ?> (<?= $actions_count[$action] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label style="color: #00ff00; display: block; margin-bottom: 5px;">Admin :</label>
                            <select name="admin" style="width: 100%; padding: 8px; background: rgba(0, 0, 0, 0.8); border: 2px solid #00ff00; border-radius: 5px; color: #00ff00; font-family: 'Courier New', monospace;">
                                <option value="">Tous les admins</option>
                                <?php foreach (array_keys($admins_count) as $admin): ?>
                                    <option value="<?= htmlspecialchars($admin) ?>" <?= $filter_admin === $admin ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($admin) ?> (<?= $admins_count[$admin] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <button type="submit" class="action-btn success" style="margin: 0; padding: 8px 15px;">
                                🔍 Filtrer
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Tableau des logs -->
                <div class="data-table">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <h3 style="color: #ff0000;">📋 Journal d'Activité (<?= count($admin_logs) ?> entrées)</h3>
                        <div>
                            <button onclick="exportLogs()" class="action-btn" style="padding: 8px 15px; font-size: 12px;">
                                💾 Exporter
                            </button>
                            <button onclick="clearFilters()" class="action-btn" style="padding: 8px 15px; font-size: 12px;">
                                🔄 Reset
                            </button>
                        </div>
                    </div>

                    <div style="max-height: 600px; overflow-y: auto;">
                        <table style="width: 100%;">
                            <thead style="position: sticky; top: 0; background: rgba(0, 255, 0, 0.2);">
                                <tr>
                                    <th style="width: 120px;">Date/Heure</th>
                                    <th style="width: 100px;">Admin</th>
                                    <th style="width: 120px;">Action</th>
                                    <th style="width: 100px;">Cible</th>
                                    <th>Détails</th>
                                    <th style="width: 100px;">IP</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($admin_logs)): ?>
                                    <tr>
                                        <td colspan="6" style="text-align: center; color: #888; font-style: italic; padding: 30px;">
                                            Aucun log trouvé avec ces filtres
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($admin_logs as $log): ?>
                                        <tr class="log-row" data-action="<?= htmlspecialchars($log['action']) ?>">
                                            <td style="color: #888; font-size: 12px;">
                                                <?= date('d/m H:i:s', strtotime($log['created_at'])) ?>
                                            </td>
                                            <td>
                                                <span style="background: rgba(0, 255, 0, 0.2); padding: 2px 6px; border-radius: 3px; color: #00ff00; font-weight: bold; font-size: 12px;">
                                                    <?= htmlspecialchars($log['username']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="action-badge" data-action="<?= $log['action'] ?>">
                                                    <?= htmlspecialchars($log['action']) ?>
                                                </span>
                                            </td>
                                            <td style="color: #ffff00; font-size: 12px;">
                                                <?= $log['target_team'] ? htmlspecialchars($log['target_team']) : '-' ?>
                                            </td>
                                            <td style="color: #ccc; font-size: 12px;">
                                                <?= $log['details'] ? htmlspecialchars($log['details']) : '-' ?>
                                            </td>
                                            <td style="color: #888; font-size: 11px; font-family: monospace;">
                                                <?= htmlspecialchars($log['ip_address']) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Panneau de statistiques -->
            <div>
                <!-- Statistiques des actions -->
                <div style="background: rgba(0, 0, 0, 0.8); border: 2px solid #ffff00; border-radius: 10px; padding: 20px; margin-bottom: 20px;">
                    <h3 style="color: #ffff00; margin-bottom: 15px;">📊 Actions Populaires</h3>
                    <?php foreach (array_slice($actions_count, 0, 8) as $action => $count): ?>
                        <div style="margin-bottom: 10px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 3px;">
                                <span style="color: #ffff00; font-size: 12px; font-weight: bold;">
                                    <?= htmlspecialchars($action) ?>
                                </span>
                                <span style="color: #888; font-size: 11px;">
                                    <?= $count ?>x
                                </span>
                            </div>
                            <div style="background: #333; height: 6px; border-radius: 3px; overflow: hidden;">
                                <div style="background: linear-gradient(to right, #ffff00, #ff8800); height: 100%; width: <?= max(($count / max($actions_count)) * 100, 5) ?>%; transition: width 0.3s;"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Activité par admin -->
                <div style="background: rgba(0, 0, 0, 0.8); border: 2px solid #ff0000; border-radius: 10px; padding: 20px; margin-bottom: 20px;">
                    <h3 style="color: #ff0000; margin-bottom: 15px;">👤 Activité par Admin</h3>
                    <?php foreach ($admins_count as $admin => $count): ?>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px; margin-bottom: 5px; background: rgba(255, 0, 0, 0.1); border-radius: 5px;">
                            <span style="color: #ff0000; font-weight: bold; font-size: 12px;">
                                <?= htmlspecialchars($admin) ?>
                            </span>
                            <span style="background: rgba(255, 0, 0, 0.3); color: white; padding: 2px 6px; border-radius: 3px; font-size: 11px;">
                                <?= $count ?> actions
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Actions rapides -->
                <div style="background: rgba(0, 0, 0, 0.8); border: 2px solid #00ff00; border-radius: 10px; padding: 20px; margin-bottom: 20px;">
                    <h3 style="color: #00ff00; margin-bottom: 15px;">⚡ Actions Rapides</h3>
                    <div style="display: grid; gap: 10px;">
                        <button onclick="refreshLogs()" class="action-btn" style="width: 100%; padding: 10px; font-size: 12px;">
                            🔄 Actualiser
                        </button>
                        <button onclick="showRecentOnly()" class="action-btn" style="width: 100%; padding: 10px; font-size: 12px;">
                            🕒 Dernière Heure
                        </button>
                        <button onclick="showCriticalOnly()" class="action-btn danger" style="width: 100%; padding: 10px; font-size: 12px;">
                            🚨 Actions Critiques
                        </button>
                        <button onclick="downloadReport()" class="action-btn" style="width: 100%; padding: 10px; font-size: 12px;">
                            📄 Rapport Complet
                        </button>
                    </div>
                </div>

                <!-- Informations système -->
                <div style="background: rgba(0, 0, 0, 0.8); border: 2px solid #666; border-radius: 10px; padding: 15px;">
                    <h3 style="color: #888; margin-bottom: 15px;">ℹ️ Informations</h3>
                    <div style="font-size: 12px; color: #ccc;">
                        <div style="margin-bottom: 8px;">
                            <strong>Logs totaux:</strong> <?= $total_logs ?>
                        </div>
                        <div style="margin-bottom: 8px;">
                            <strong>Période:</strong> <?= !empty($admin_logs) ? date('d/m/Y', strtotime(end($admin_logs)['created_at'])) . ' - ' . date('d/m/Y', strtotime($admin_logs[0]['created_at'])) : 'Aucune' ?>
                        </div>
                        <div style="margin-bottom: 8px;">
                            <strong>Admins actifs:</strong> <?= count($admins_count) ?>
                        </div>
                        <div>
                            <strong>Dernière MAJ:</strong> <?= date('H:i:s') ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function exportLogs() {
            const logs = <?= json_encode($admin_logs) ?>;
            const csvContent = "data:text/csv;charset=utf-8,Date,Admin,Action,Cible,Details,IP\n" +
                logs.map(log => `"${log.created_at}","${log.username}","${log.action}","${log.target_team || ''}","${(log.details || '').replace(/"/g, '""')}","${log.ip_address}"`).join("\n");

            const encodedUri = encodeURI(csvContent);
            const link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", "maria-curia-logs-" + Date.now() + ".csv");
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        function clearFilters() {
            window.location.href = 'logs.php';
        }

        function refreshLogs() {
            window.location.reload();
        }

        function showRecentOnly() {
            const rows = document.querySelectorAll('.log-row');
            const oneHourAgo = new Date(Date.now() - 60 * 60 * 1000);

            rows.forEach(row => {
                const dateText = row.cells[0].textContent.trim();
                // Conversion simple pour la démo
                row.style.display = 'table-row';
            });
        }

        function showCriticalOnly() {
            const criticalActions = ['RESET_ALL', 'DELETE_TEAM', 'RESET_TEAM'];
            const rows = document.querySelectorAll('.log-row');

            rows.forEach(row => {
                const action = row.dataset.action;
                if (criticalActions.includes(action)) {
                    row.style.display = 'table-row';
                    row.style.background = 'rgba(255, 0, 0, 0.2)';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        function downloadReport() {
            const report = {
                generated_at: new Date().toISOString(),
                total_logs: <?= $total_logs ?>,
                actions_stats: <?= json_encode($actions_count) ?>,
                admins_stats: <?= json_encode($admins_count) ?>,
                recent_logs: <?= json_encode(array_slice($admin_logs, 0, 20)) ?>
            };

            const blob = new Blob([JSON.stringify(report, null, 2)], {type: 'application/json'});
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'maria-curia-admin-report-' + Date.now() + '.json';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }

        // Coloration des badges d'action
        document.addEventListener('DOMContentLoaded', function() {
            const actionColors = {
                'LOGIN': '#3498db',
                'LOGOUT': '#95a5a6',
                'RESET_ALL': '#e74c3c',
                'RESET_TEAM': '#f39c12',
                'DELETE_TEAM': '#e74c3c',
                'ADD_TEAM': '#2ecc71',
                'UPDATE_TEAM': '#9b59b6',
                'ADD_POINTS': '#f1c40f'
            };

            document.querySelectorAll('.action-badge').forEach(badge => {
                const action = badge.dataset.action;
                const color = actionColors[action] || '#7f8c8d';
                badge.style.cssText = `
                    background: ${color};
                    color: white;
                    padding: 2px 6px;
                    border-radius: 3px;
                    font-size: 11px;
                    font-weight: bold;
                `;
            });

            // Animation d'apparition des lignes
            const rows = document.querySelectorAll('.log-row');
            rows.forEach((row, index) => {
                row.style.animation = `fadeIn 0.3s ease-out ${index * 0.05}s both`;
            });
        });

        // Styles d'animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(10px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .log-row:hover {
                background: rgba(0, 255, 0, 0.1) !important;
                transform: translateX(5px);
                transition: all 0.2s;
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>