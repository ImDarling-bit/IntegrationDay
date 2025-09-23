<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <div>

        <?php if ($userTeam): ?>
        <div>
            <h2>Mon Équipe</h2>
            <div>
                <p><strong>Score total : <?php echo htmlspecialchars($userTeam['points']); ?>/8 points</strong></p>
                <?php $maxPoints = 8; $percentage = ($userTeam['points'] / $maxPoints) * 100;?>
            </div>
        </div>
        <?php else: ?>
        <div>
            <p>Vous n'êtes assigné à aucune équipe actuellement.</p>
        </div>
        <?php endif; ?>
    </div>
</body>

</html>