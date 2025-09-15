<?php
require_once 'config.php';
require_login();

$leaderboard = get_leaderboard();
$team_scans = get_team_scans($_SESSION['team_id']);
$recent_activity = get_recent_activity(10);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classement - Maria Curia WAMP</title>
    <link rel="stylesheet" href="style.css">
    <meta http-equiv="refresh" content="15"> <!-- Auto-refresh toutes les 15 secondes -->
</head>
<body>
    <div class="container">
        <div class="clearfix">
            <h1 class="title">🏆 Classement des Équipes</h1>
            <a href="logout.php" class="btn-logout button">Déconnexion</a>
        </div>

        <div class="nav">
            <a href="dashboard.php">🏠 Accueil</a>
            <a href="scanner.php">🔍 Scanner QR Code</a>
            <a href="leaderboard.php">🏆 Classement</a>
            <a href="stats.php">📊 Statistiques</a>
        </div>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
            <!-- Classement principal -->
            <div>
                <h3 style="color: #2c3e50;">🥇 Classement Général</h3>

                <table class="leaderboard">
                    <thead>
                        <tr>
                            <th>Rang</th>
                            <th>Équipe</th>
                            <th>Score</th>
                            <th>Découvertes</th>
                            <th>Progression</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($leaderboard)): ?>
                            <tr>
                                <td colspan="5" class="text-center">Aucune équipe n'a encore de points</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($leaderboard as $index => $team): ?>
                                <?php
                                $rank = $index + 1;
                                $class = '';
                                if ($rank == 1) $class = 'rank-1';
                                elseif ($rank == 2) $class = 'rank-2';
                                elseif ($rank == 3) $class = 'rank-3';

                                $is_current = ($team['nom_equipe'] == $_SESSION['nom_equipe']);
                                if ($is_current) $class .= ' current-team';

                                // Calculer les découvertes de cette équipe
                                $team_discoveries = 0;
                                $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM scans WHERE team_id = (SELECT id FROM teams WHERE nom_equipe = ?)");
                                $stmt->execute([$team['nom_equipe']]);
                                $result = $stmt->fetch();
                                $team_discoveries = $result['count'];

                                $progression = ($team_discoveries / 6) * 100;
                                ?>
                                <tr class="<?= $class ?>">
                                    <td>
                                        <?php if ($rank == 1): ?>
                                            🥇 #<?= $rank ?>
                                        <?php elseif ($rank == 2): ?>
                                            🥈 #<?= $rank ?>
                                        <?php elseif ($rank == 3): ?>
                                            🥉 #<?= $rank ?>
                                        <?php else: ?>
                                            #<?= $rank ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong><?= htmlspecialchars($team['nom_equipe']) ?></strong>
                                        <?= $is_current ? ' 👈 <em>(Vous)</em>' : '' ?>
                                    </td>
                                    <td style="font-weight: bold; color: #e74c3c;">
                                        <?= $team['score'] ?> points
                                    </td>
                                    <td>
                                        <?= $team_discoveries ?>/6
                                    </td>
                                    <td>
                                        <div class="progress-bar" style="height: 15px; margin: 0;">
                                            <div class="progress-fill" style="width: <?= $progression ?>%; font-size: 11px;">
                                                <?= round($progression) ?>%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>

                <div style="margin-top: 20px; text-align: center;">
                    <button onclick="window.location.reload()" class="btn-secondary">
                        🔄 Actualiser le Classement
                    </button>
                </div>

                <!-- Statistiques rapides -->
                <div class="stats-grid" style="margin-top: 30px;">
                    <div class="stat-card">
                        <h3><?= count($leaderboard) ?></h3>
                        <p>Équipes Participantes</p>
                    </div>
                    <div class="stat-card">
                        <h3><?= array_sum(array_column($leaderboard, 'score')) ?></h3>
                        <p>Points Totaux</p>
                    </div>
                    <div class="stat-card">
                        <h3><?= $leaderboard ? max(array_column($leaderboard, 'score')) : 0 ?></h3>
                        <p>Meilleur Score</p>
                    </div>
                    <div class="stat-card">
                        <h3><?= count($recent_activity) ?></h3>
                        <p>Activités Récentes</p>
                    </div>
                </div>
            </div>

            <!-- Activité en temps réel -->
            <div>
                <h3 style="color: #2c3e50;">📈 Activité en Temps Réel</h3>

                <div style="background: #f8f9fa; padding: 15px; border-radius: 10px; margin-bottom: 20px;">
                    <h4 style="color: #3498db; margin-top: 0;">🔥 Dernières Découvertes</h4>
                    <?php if (!empty($recent_activity)): ?>
                        <?php foreach ($recent_activity as $activity): ?>
                            <div style="margin-bottom: 12px; padding: 10px; background: white; border-radius: 8px; border-left: 4px solid #3498db;">
                                <div style="font-weight: bold; color: #2c3e50;">
                                    <?= htmlspecialchars($activity['nom_equipe']) ?>
                                </div>
                                <div style="color: #7f8c8d; font-size: 14px;">
                                    a découvert <strong><?= htmlspecialchars($activity['character_name']) ?></strong>
                                </div>
                                <div style="color: #95a5a6; font-size: 12px;">
                                    <?= date('H:i:s', strtotime($activity['scanned_at'])) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="color: #7f8c8d; text-align: center; font-style: italic;">
                            Aucune activité récente
                        </p>
                    <?php endif; ?>
                </div>

                <h4 style="color: #2c3e50;">🎯 Votre Position</h4>
                <div style="background: #e3f2fd; padding: 15px; border-radius: 10px; border: 2px solid #3498db;">
                    <?php
                    $current_rank = 0;
                    foreach ($leaderboard as $index => $team) {
                        if ($team['nom_equipe'] == $_SESSION['nom_equipe']) {
                            $current_rank = $index + 1;
                            break;
                        }
                    }
                    ?>
                    <div style="text-align: center;">
                        <h3 style="color: #3498db; margin: 0;">
                            <?php if ($current_rank == 1): ?>
                                🥇 1ère place !
                            <?php elseif ($current_rank == 2): ?>
                                🥈 2ème place !
                            <?php elseif ($current_rank == 3): ?>
                                🥉 3ème place !
                            <?php else: ?>
                                #<?= $current_rank ?> sur <?= count($leaderboard) ?>
                            <?php endif; ?>
                        </h3>
                        <p style="margin: 5px 0; color: #2c3e50;">
                            Score: <strong><?= get_team_score($_SESSION['team_id']) ?> points</strong>
                        </p>
                        <p style="margin: 5px 0; color: #2c3e50;">
                            Découvertes: <strong><?= count($team_scans) ?>/6</strong>
                        </p>
                    </div>
                </div>

                <div style="margin-top: 20px; text-align: center;">
                    <a href="scanner.php" style="display: inline-block; padding: 12px 20px; background: linear-gradient(135deg, #e74c3c, #c0392b); color: white; text-decoration: none; border-radius: 8px; font-weight: bold;">
                        🚀 Scanner pour Progresser
                    </a>
                </div>

                <!-- Récompenses -->
                <div style="margin-top: 30px;">
                    <h4 style="color: #2c3e50;">🏅 Récompenses</h4>
                    <div style="background: #fff3cd; padding: 15px; border-radius: 10px; border-left: 4px solid #f1c40f;">
                        <div style="margin-bottom: 8px;">
                            <strong>🥇 1ère place :</strong> Trophée d'or + Diplôme
                        </div>
                        <div style="margin-bottom: 8px;">
                            <strong>🥈 2ème place :</strong> Trophée d'argent + Certificat
                        </div>
                        <div style="margin-bottom: 8px;">
                            <strong>🥉 3ème place :</strong> Trophée de bronze + Mention
                        </div>
                        <div>
                            <strong>🎯 Participation :</strong> Certificat de participation
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Mise à jour du titre avec le classement
        document.addEventListener('DOMContentLoaded', function() {
            const currentRank = <?= $current_rank ?>;
            const totalTeams = <?= count($leaderboard) ?>;

            if (currentRank > 0) {
                document.title = `Classement #${currentRank}/${totalTeams} - Maria Curia`;
            }
        });

        // Animation du podium
        const podiumRows = document.querySelectorAll('.rank-1, .rank-2, .rank-3');
        podiumRows.forEach((row, index) => {
            row.style.animation = `fadeIn 0.6s ease-out ${index * 0.2}s both`;
        });

        // Ajouter l'animation CSS
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }
        `;
        document.head.appendChild(style);

        // Notification de position
        const currentTeamRow = document.querySelector('.current-team');
        if (currentTeamRow) {
            currentTeamRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    </script>
</body>
</html>