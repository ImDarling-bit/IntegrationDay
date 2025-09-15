<?php
require_once 'config.php';
require_admin_login();

$teams = get_all_teams();

// Traitement des actions
if ($_POST) {
    $action = $_POST['action'] ?? '';
    $team_id = $_POST['team_id'] ?? '';
    $new_name = $_POST['new_name'] ?? '';
    $new_password = $_POST['new_password'] ?? '';

    switch ($action) {
        case 'update_team':
            if ($team_id && $new_name) {
                $stmt = $game_pdo->prepare("UPDATE teams SET nom_equipe = ? WHERE id = ?");
                if ($stmt->execute([$new_name, $team_id])) {
                    log_admin_action('UPDATE_TEAM', $new_name, "Équipe ID $team_id renommée");
                    $message = "✅ Équipe mise à jour";
                }
            }
            break;

        case 'delete_team':
            if ($team_id) {
                // Supprimer les scans de l'équipe d'abord
                $stmt = $game_pdo->prepare("DELETE FROM scans WHERE team_id = ?");
                $stmt->execute([$team_id]);

                // Supprimer l'équipe
                $stmt = $game_pdo->prepare("DELETE FROM teams WHERE id = ?");
                if ($stmt->execute([$team_id])) {
                    log_admin_action('DELETE_TEAM', null, "Équipe ID $team_id supprimée");
                    $message = "✅ Équipe supprimée";
                }
            }
            break;

        case 'add_team':
            if ($new_name && $new_password) {
                $stmt = $game_pdo->prepare("INSERT INTO teams (nom_equipe, mot_de_passe) VALUES (?, ?)");
                if ($stmt->execute([$new_name, $new_password])) {
                    log_admin_action('ADD_TEAM', $new_name, "Nouvelle équipe créée");
                    $message = "✅ Équipe ajoutée";
                }
            }
            break;
    }

    // Recharger les données
    if (isset($message)) {
        $teams = get_all_teams();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Équipes - Admin Maria Curia</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="admin-container">
        <!-- En-tête Admin -->
        <div class="admin-header">
            <div>
                <h1 class="admin-title">👥 GESTION DES ÉQUIPES</h1>
                <div class="admin-user">
                    Connecté en tant que: <strong><?= htmlspecialchars($_SESSION['admin_username']) ?></strong>
                </div>
            </div>
            <a href="logout.php" class="logout-btn">🚪 Déconnexion</a>
        </div>

        <!-- Navigation -->
        <div class="admin-nav">
            <a href="dashboard.php" class="nav-btn">🏠 Dashboard</a>
            <a href="teams.php" class="nav-btn active">👥 Équipes</a>
            <a href="monitoring.php" class="nav-btn">📊 Monitoring</a>
            <a href="logs.php" class="nav-btn">📋 Logs</a>
        </div>

        <?php if (isset($message)): ?>
            <div style="background: rgba(0, 255, 0, 0.2); border: 2px solid #00ff00; padding: 15px; border-radius: 10px; margin-bottom: 20px; text-align: center; color: #00ff00;">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <!-- Ajouter une équipe -->
        <div style="background: rgba(0, 0, 0, 0.8); border: 2px solid #00ff00; border-radius: 10px; padding: 20px; margin-bottom: 30px;">
            <h2 style="color: #00ff00; margin-bottom: 15px;">➕ Ajouter une Équipe</h2>
            <form method="POST" style="display: grid; grid-template-columns: 1fr 1fr auto; gap: 15px; align-items: end;">
                <div>
                    <label style="color: #00ff00; display: block; margin-bottom: 5px;">Nom de l'équipe :</label>
                    <input type="text" name="new_name" required
                           style="width: 100%; padding: 10px; background: rgba(0, 0, 0, 0.8); border: 2px solid #00ff00; border-radius: 5px; color: #00ff00; font-family: 'Courier New', monospace;"
                           placeholder="Ex: Équipe Delta">
                </div>
                <div>
                    <label style="color: #00ff00; display: block; margin-bottom: 5px;">Mot de passe :</label>
                    <input type="text" name="new_password" required
                           style="width: 100%; padding: 10px; background: rgba(0, 0, 0, 0.8); border: 2px solid #00ff00; border-radius: 5px; color: #00ff00; font-family: 'Courier New', monospace;"
                           placeholder="Ex: delta123">
                </div>
                <div>
                    <input type="hidden" name="action" value="add_team">
                    <button type="submit" class="action-btn success" style="margin: 0; width: 100%;">
                        ➕ AJOUTER
                    </button>
                </div>
            </form>
        </div>

        <!-- Liste des équipes -->
        <div class="data-table">
            <h2 style="color: #ff0000; margin-bottom: 20px;">📋 Liste des Équipes (<?= count($teams) ?>)</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom de l'Équipe</th>
                        <th>Mot de Passe</th>
                        <th>Score Total</th>
                        <th>Scans</th>
                        <th>Progression</th>
                        <th>Date Création</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($teams)): ?>
                        <tr>
                            <td colspan="8" style="text-align: center; color: #888; font-style: italic;">
                                Aucune équipe trouvée
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($teams as $team): ?>
                            <tr>
                                <td style="color: #888;">#<?= $team['id'] ?></td>
                                <td>
                                    <strong style="color: #00ff00;">
                                        <?= htmlspecialchars($team['nom_equipe']) ?>
                                    </strong>
                                </td>
                                <td style="font-family: monospace; color: #ffff00;">
                                    <?= htmlspecialchars($team['mot_de_passe']) ?>
                                </td>
                                <td style="color: #ffff00;">
                                    <strong><?= $team['total_score'] ?></strong> points
                                </td>
                                <td>
                                    <?= $team['scans_count'] ?>/6
                                </td>
                                <td>
                                    <div style="background: #333; height: 15px; border-radius: 5px; overflow: hidden; position: relative;">
                                        <div style="background: linear-gradient(to right, #ff0000, #ffff00, #00ff00); height: 100%; width: <?= ($team['scans_count'] / 6) * 100 ?>%; transition: width 0.3s;"></div>
                                        <span style="position: absolute; top: 0; left: 50%; transform: translateX(-50%); font-size: 11px; color: white; line-height: 15px;">
                                            <?= round(($team['scans_count'] / 6) * 100) ?>%
                                        </span>
                                    </div>
                                </td>
                                <td style="color: #888; font-size: 12px;">
                                    <?= date('d/m/Y H:i', strtotime($team['created_at'])) ?>
                                </td>
                                <td>
                                    <div style="display: flex; gap: 5px; flex-wrap: wrap;">
                                        <!-- Bouton Modifier -->
                                        <button onclick="editTeam(<?= $team['id'] ?>, '<?= htmlspecialchars($team['nom_equipe'], ENT_QUOTES) ?>')"
                                                style="background: #3498db; color: white; border: none; padding: 5px 8px; border-radius: 3px; cursor: pointer; font-size: 11px;">
                                            ✏️ Modifier
                                        </button>

                                        <!-- Bouton Reset -->
                                        <form method="POST" style="display: inline;" onsubmit="return confirm('⚠️ Reset la progression de cette équipe ?')">
                                            <input type="hidden" name="action" value="reset_team">
                                            <input type="hidden" name="team_id" value="<?= $team['id'] ?>">
                                            <button type="submit" style="background: #f39c12; color: white; border: none; padding: 5px 8px; border-radius: 3px; cursor: pointer; font-size: 11px;">
                                                🔄 Reset
                                            </button>
                                        </form>

                                        <!-- Bouton Supprimer -->
                                        <form method="POST" style="display: inline;" onsubmit="return confirm('⚠️ ATTENTION: Supprimer définitivement cette équipe ?')">
                                            <input type="hidden" name="action" value="delete_team">
                                            <input type="hidden" name="team_id" value="<?= $team['id'] ?>">
                                            <button type="submit" style="background: #e74c3c; color: white; border: none; padding: 5px 8px; border-radius: 3px; cursor: pointer; font-size: 11px;">
                                                🗑️ Suppr
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Statistiques rapides -->
        <div class="stats-grid" style="margin-top: 30px;">
            <div class="stat-card">
                <h3><?= count($teams) ?></h3>
                <p>Équipes Totales</p>
            </div>
            <div class="stat-card">
                <h3><?= array_sum(array_column($teams, 'total_score')) ?></h3>
                <p>Points Cumulés</p>
            </div>
            <div class="stat-card">
                <h3><?= array_sum(array_column($teams, 'scans_count')) ?></h3>
                <p>Scans Totaux</p>
            </div>
            <div class="stat-card">
                <h3><?= $teams ? round(array_sum(array_column($teams, 'scans_count')) / count($teams), 1) : 0 ?></h3>
                <p>Scans/Équipe (Moy.)</p>
            </div>
        </div>

        <!-- Actions par lot -->
        <div style="margin-top: 30px; padding: 20px; background: rgba(0, 0, 0, 0.8); border: 2px solid #ff0000; border-radius: 10px;">
            <h3 style="color: #ff0000; margin-bottom: 15px;">⚠️ Actions Dangereuses</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
                <form method="POST" onsubmit="return confirm('⚠️ ATTENTION: Reset toutes les progressions ?')">
                    <input type="hidden" name="action" value="reset_all">
                    <button type="submit" class="action-btn danger" style="width: 100%;">
                        💥 RESET TOUTES LES PROGRESSIONS
                    </button>
                </form>

                <button onclick="exportTeams()" class="action-btn" style="width: 100%;">
                    💾 EXPORTER LISTE ÉQUIPES
                </button>

                <button onclick="generatePasswords()" class="action-btn" style="width: 100%;">
                    🔐 GÉNÉRER NOUVEAUX MDP
                </button>
            </div>
        </div>
    </div>

    <!-- Modal de modification -->
    <div id="editModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.8); z-index: 1000;">
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: #000; border: 2px solid #00ff00; border-radius: 10px; padding: 30px; min-width: 400px;">
            <h3 style="color: #00ff00; margin-bottom: 20px;">✏️ Modifier l'Équipe</h3>
            <form method="POST" id="editForm">
                <input type="hidden" name="action" value="update_team">
                <input type="hidden" name="team_id" id="editTeamId">

                <div style="margin-bottom: 15px;">
                    <label style="color: #00ff00; display: block; margin-bottom: 5px;">Nouveau nom :</label>
                    <input type="text" name="new_name" id="editTeamName" required
                           style="width: 100%; padding: 10px; background: rgba(0, 0, 0, 0.8); border: 2px solid #00ff00; border-radius: 5px; color: #00ff00; font-family: 'Courier New', monospace;">
                </div>

                <div style="display: flex; gap: 10px; justify-content: end;">
                    <button type="button" onclick="closeEditModal()"
                            style="padding: 10px 20px; background: #666; color: white; border: none; border-radius: 5px; cursor: pointer;">
                        Annuler
                    </button>
                    <button type="submit"
                            style="padding: 10px 20px; background: #00ff00; color: #000; border: none; border-radius: 5px; cursor: pointer; font-weight: bold;">
                        Sauvegarder
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function editTeam(teamId, teamName) {
            document.getElementById('editTeamId').value = teamId;
            document.getElementById('editTeamName').value = teamName;
            document.getElementById('editModal').style.display = 'block';
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        function exportTeams() {
            const teams = <?= json_encode($teams) ?>;
            const csvContent = "data:text/csv;charset=utf-8,ID,Nom,Mot de Passe,Score,Scans,Date Création\n" +
                teams.map(team => `${team.id},"${team.nom_equipe}","${team.mot_de_passe}",${team.total_score},${team.scans_count},"${team.created_at}"`).join("\n");

            const encodedUri = encodeURI(csvContent);
            const link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", "maria-curia-teams-" + Date.now() + ".csv");
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        function generatePasswords() {
            if (confirm('⚠️ Générer de nouveaux mots de passe pour toutes les équipes ?')) {
                alert('🔧 Fonctionnalité en développement');
            }
        }

        // Fermer le modal en cliquant à l'extérieur
        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditModal();
            }
        });

        // Animations
        document.addEventListener('DOMContentLoaded', function() {
            const rows = document.querySelectorAll('tbody tr');
            rows.forEach((row, index) => {
                row.style.animation = `fadeIn 0.3s ease-out ${index * 0.1}s both`;
            });
        });
    </script>
</body>
</html>