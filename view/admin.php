
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Administrateur</title>
</head>
<body>
    <h1>Espace Administrateur</h1>

    <h2>Liste des équipes</h2>

    <?php if (!empty($teams)): ?>
        <table>
            <thead>
                <tr>
                    <th>Nom de l'équipe</th>
                    <th>Points</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($teams as $team): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($team['nom']); ?></td>
                        <td><?php echo htmlspecialchars($team['points']); ?></td>
                        <td>
                            <button>Freeze</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Aucune équipe trouvée.</p>
    <?php endif; ?>

</body>
</html>