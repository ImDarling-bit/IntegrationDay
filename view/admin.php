<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../src/css/admin.css">
    <title>Espace Administrateur</title>
</head>
<body>
    <h1>Espace Administrateur</h1>
    <div>
        <a href="../controller/C_admin.php">
            <button>Accueil</button>
        </a>
        <a href="../controller/C_admin.php?view=mod_user">
            <button>Modification utilisateur</button>
        </a>
        <a href="../controller/C_admin.php?view=mod_team">
            <button>Modification équipe</button>
        </a>
    </div>

    <?php if (isset($_GET['view']) && $_GET['view'] === 'mod_user'): ?>
        <?php
        $users = getAllUsers();
        $roles = getAllRoles();
        include '../view/mod_user_content.php';
        ?>
    <?php elseif (isset($_GET['view']) && $_GET['view'] === 'mod_team'): ?>
        <?php
        include '../view/mod_team_content.php';
        ?>
    <?php else: ?>
        <h2>Liste des équipes</h2>

        <?php if (!empty($message)): ?>
            <div style="padding: 10px; margin: 10px 0; background-color: #dff0d8; border: 1px solid #d6e9c6; color: #3c763d;">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

    <?php if (!empty($teams)): ?>
        <table>
            <thead>
                <tr>
                    <th>Nom de l'équipe</th>
                    <th>Points</th>
                    <th>Statut</th>
                    <th>Freeze</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($teams as $team): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($team['nom']); ?></td>
                        <td><?php echo htmlspecialchars($team['points']); ?></td>
                        <td>
                            <?php 
                            // Vérification si la clé freeze existe et gestion des valeurs
                            $freezeStatus = isset($team['freeze']) ? (bool)$team['freeze'] : false;
                            ?>
                            <?php if ($freezeStatus): ?>
                                <span style="color: red; font-weight: bold;">Activé</span>
                            <?php else: ?>
                                <span style="color: green; font-weight: bold;">Désactivé</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="toggle_freeze">
                                <input type="hidden" name="team_id" value="<?php echo $team['id']; ?>">
                                <?php if ($freezeStatus): ?>
                                    <button type="submit" style="background-color: #5cb85c; color: white; border: none; padding: 5px 10px; cursor: pointer;">
                                        Désactiver
                                    </button>
                                <?php else: ?>
                                    <button type="submit" style="background-color: #d9534f; color: white; border: none; padding: 5px 10px; cursor: pointer;">
                                        Activer
                                    </button>
                                <?php endif; ?>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Aucune équipe trouvée.</p>
    <?php endif; ?>
    <?php endif; ?>
</body>
</html>