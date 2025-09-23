    <h2>Gestion des Équipes</h2>

    <?php if (!empty($message)): ?>
        <div>
            <p><?php echo htmlspecialchars($message); ?></p>
        </div>
    <?php endif; ?>

    <h3>Liste des équipes</h3>

    <?php if (!empty($teams)): ?>
        <table>
            <thead>
                <tr>
                    <th>Nom de l'équipe</th>
                    <th>Points</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($teams as $team): ?>
                    <tr>
                        <form method="POST" action="../controller/C_admin.php?view=mod_team">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="team_id" value="<?php echo $team['id']; ?>">

                            <td>
                                <input type="text" name="nom" value="<?php echo htmlspecialchars($team['nom']); ?>" required>
                            </td>

                            <td>
                                <input type="number" name="points" value="<?php echo htmlspecialchars($team['points']); ?>" min="0" required>
                            </td>

                            <td>
                                <button type="submit">Modifier</button>
                        </form>
                        <form method="POST" action="../controller/C_admin.php?view=mod_team">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="team_id" value="<?php echo $team['id']; ?>">
                            <button type="submit" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette équipe ?')">Supprimer</button>
                        </form>
                            </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Aucune équipe trouvée.</p>
    <?php endif; ?>

    <h3>Ajouter une équipe</h3>

    <form method="POST" action="../controller/C_admin.php?view=mod_team">
        <input type="hidden" name="action" value="add">

        <div>
            <label for="add_nom">Nom de l'équipe :</label>
            <input type="text" id="add_nom" name="nom" required>
        </div>

        <div>
            <label for="add_points">Points :</label>
            <input type="number" id="add_points" name="points" value="0" min="0" required>
        </div>

        <div>
            <button type="submit">Ajouter</button>
        </div>
    </form>