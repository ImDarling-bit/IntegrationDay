<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau des Scores</title>
</head>
<body>
    <h1>Tableau des Scores</h1>

    <?php
    $scoreTeams = isset($teamsWithScores) ? $teamsWithScores : (isset($teams) ? $teams : []);
    if (!empty($scoreTeams)):
    ?>
        <table>
            <thead>
                <tr>
                    <th>Position</th>
                    <th>Nom de l'équipe</th>
                    <th>Points</th>
                    <th>Pourcentage</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($scoreTeams as $team): ?>
                    <tr>
                        <td>
                            <?php
                            if ($team['position'] == 1) {
                                echo "🥇 " . $team['position'];
                            } elseif ($team['position'] == 2) {
                                echo "🥈 " . $team['position'];
                            } elseif ($team['position'] == 3) {
                                echo "🥉 " . $team['position'];
                            } else {
                                echo $team['position'];
                            }
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars($team['nom']); ?></td>
                        <td><?php echo htmlspecialchars($team['points']); ?>/8</td>
                        <td><?php echo $team['percentage']; ?>%</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Aucune équipe trouvée.</p>
    <?php endif; ?>

</body>
</html>