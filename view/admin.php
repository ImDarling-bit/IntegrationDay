
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <?php include("reload.html"); ?>
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
                            <?php 
                            // Si l'équipe n'est pas ou plus gelé...
                            freezed($team["id"]);
                            if (isset($_SESSION["freeze"][$team["id"]]) && $_SESSION["freeze"][$team["id"]] == true) { 
                                echo "<input type='submit' value='Freezed' />"; 
                            }
                            else {
                                echo "<form method='GET'> 
                                <input type='hidden' name ='freeze' value='$team[id]'/>
                                <input type='submit' value='Freeze' />
                                </form>"; 
                            }
                            
                            ?>
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