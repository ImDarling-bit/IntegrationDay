    <h2>Gestion des Utilisateurs</h2>

    <?php if (!empty($message)): ?>
        <div>
            <p><?php echo htmlspecialchars($message); ?></p>
        </div>
    <?php endif; ?>

    <h3>Liste des utilisateurs</h3>

    <?php if (!empty($users)): ?>
        <table>
            <thead>
                <tr>
                    <th>Identifiant</th>
                    <th>Suit</th>
                    <th>Rôle</th>
                    <th>Mot de passe</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <form method="POST" action="../controller/C_admin.php?view=mod_user">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">

                            <td>
                                <input type="text" name="identifiant" value="<?php echo htmlspecialchars($user['identifiant']); ?>" required>
                            </td>

                            <td>
                                <select name="team_id">
                                    <option value="">Aucune équipe</option>
                                    <?php foreach ($teams as $team): ?>
                                        <option value="<?php echo $team['id']; ?>" <?php echo ($user['team_id'] == $team['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($team['nom']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>

                            <td>
                                <select name="role_id" required>
                                    <?php foreach ($roles as $role): ?>
                                        <option value="<?php echo $role['id']; ?>" <?php echo ($user['role'] == $role['nom']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($role['nom']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>

                            <td>
                                <input type="text" name="mot_de_passe" value="<?php echo htmlspecialchars($user['mot_de_passe']); ?>" required>
                            </td>

                            <td>
                                <button type="submit">Modifier</button>
                        </form>
                        <form method="POST" action="../controller/C_admin.php?view=mod_user">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                            <button type="submit" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">Supprimer</button>
                        </form>
                            </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Aucun utilisateur trouvé.</p>
    <?php endif; ?>

    <h3>Ajouter un utilisateur</h3>

    <form method="POST" action="../controller/C_admin.php?view=mod_user">
        <input type="hidden" name="action" value="add">

        <div>
            <label for="add_identifiant">Identifiant :</label>
            <input type="text" id="add_identifiant" name="identifiant" required>
        </div>

        <div>
            <label for="add_team_id">Équipe :</label>
            <select id="add_team_id" name="team_id">
                <option value="">Aucune équipe</option>
                <?php foreach ($teams as $team): ?>
                    <option value="<?php echo $team['id']; ?>">
                        <?php echo htmlspecialchars($team['nom']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label for="add_role_id">Rôle :</label>
            <select id="add_role_id" name="role_id" required>
                <?php foreach ($roles as $role): ?>
                    <option value="<?php echo $role['id']; ?>">
                        <?php echo htmlspecialchars($role['nom']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label for="add_mot_de_passe">Mot de passe :</label>
            <input type="text" id="add_mot_de_passe" name="mot_de_passe" required>
        </div>

        <div>
            <button type="submit">Ajouter</button>
        </div>
    </form>