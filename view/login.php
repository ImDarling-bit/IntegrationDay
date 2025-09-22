
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
</head>
<body>
    <h1>Connexion</h1>

    <?php if (isset($error)): ?>
        <div>
            <p><?php echo htmlspecialchars($error); ?></p>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div>
            <p>Erreur de connexion. Veuillez réessayer.</p>
        </div>
    <?php endif; ?>

    <form method="POST" action="../controller/C_login.php">
        <div>
            <label for="identifiant">Identifiant :</label>
            <input type="text" id="identifiant" name="identifiant" required>
        </div>

        <div>
            <label for="mot_de_passe">Mot de passe :</label>
            <input type="password" id="mot_de_passe" name="mot_de_passe" required>
        </div>

        <div>
            <button type="submit">Se connecter</button>
        </div>
    </form>
</body>
</html>