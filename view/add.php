<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier les Points</title>
</head>
<body>
    <h1>Modifier les Points des Équipes</h1>

    <?php if (!empty($message)): ?>
        <div>
            <p><?php echo htmlspecialchars($message); ?></p>
        </div>
    <?php endif; ?>

    <?php if (!empty($teams)): ?>
        <?php foreach ($teams as $team): ?>
            <div>
                <h3><?php echo htmlspecialchars($team['nom']); ?></h3>
                <p>Points actuels: <strong><?php echo htmlspecialchars($team['points']); ?></strong></p>

                <form method="POST">
                    <input type="hidden" name="team_id" value="<?php echo $team['id']; ?>">
                    <input type="hidden" name="operation" value="subtract">
                    <button type="submit">-</button>
                </form>

                <span>
                    <?php echo htmlspecialchars($team['points']); ?>
                </span>

                <form method="POST">
                    <input type="hidden" name="team_id" value="<?php echo $team['id']; ?>">
                    <input type="hidden" name="operation" value="add">
                    <button type="submit">+</button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Aucune équipe trouvée.</p>
    <?php endif; ?>

</body>
</html>