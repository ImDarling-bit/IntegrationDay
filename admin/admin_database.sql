-- Admin Database Schema for Maria Curia WAMP Version
-- Base de données pour les organisateurs/hackers

-- Table des admins/hackers
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'hacker', 'organizer') DEFAULT 'hacker',
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table des logs d'activité admin
CREATE TABLE IF NOT EXISTS admin_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NOT NULL,
    action VARCHAR(100) NOT NULL,
    target_team VARCHAR(50),
    details TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE CASCADE
);

-- Table des paramètres du jeu
CREATE TABLE IF NOT EXISTS game_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(50) UNIQUE NOT NULL,
    setting_value TEXT,
    updated_by INT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (updated_by) REFERENCES admins(id) ON DELETE SET NULL
);

-- Insérer les admins par défaut
INSERT INTO admins (username, password, role) VALUES
-- ('admin', 'admin123', ['admin' ou 'organizer' ou 'hacker']),  Example

-- Paramètres par défaut du jeu
INSERT INTO game_settings (setting_key, setting_value) VALUES
('game_active', 'true'),
('max_teams', '10'),
('qr_codes_active', 'true'),
('leaderboard_visible', 'true'),
('reset_allowed', 'true');