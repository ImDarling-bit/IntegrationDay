

USE maria_curia;

-- Renomme colonne password > mot_de_passe si existe
SET @column_exists = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = 'maria_curia'
    AND TABLE_NAME = 'teams'
    AND COLUMN_NAME = 'password'
);

SET @sql = IF(@column_exists > 0,
    'ALTER TABLE teams CHANGE password mot_de_passe VARCHAR(255) NOT NULL',
    'SELECT "Colonne password n\'existe pas, pas de modification nécessaire" as message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Mettre scores existants sur scans
UPDATE teams t SET score = (
    SELECT COALESCE(SUM(c.points), 0)
    FROM scans s
    JOIN characters c ON s.character_id = c.id
    WHERE s.team_id = t.id
);

-- Afficher statut
SELECT 'Base de données mise à jour avec succès' as status;