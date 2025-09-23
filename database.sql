-- Base de données pour le système MVC
-- Structure des tables pour la gestion des utilisateurs, rôles et équipes

CREATE DATABASE IF NOT EXISTS maria_curia_id;
USE maria_curia_id;

-- Table des rôles
CREATE TABLE roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(50) NOT NULL UNIQUE,
    description TEXT
);

-- Table des utilisateurs
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    identifiant VARCHAR(50) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    role_id INT,
    team_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id),
    FOREIGN KEY (team_id) REFERENCES teams(id)
);

-- Table des équipes
CREATE TABLE teams (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    points INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    freeze BOOLEAN DEFAULT FALSE
);

-- Table de liaison users-teams (pour les utilisateurs avec rôle "user")
CREATE TABLE user_teams (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    team_id INT,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (team_id) REFERENCES teams(id)
);

-- Insertion des rôles
INSERT INTO roles (nom, description) VALUES
('admin', 'Administrateur du système'),
('user', 'Utilisateur standard'),
('organisateur', 'Organisateur d\'événements');

-- Insertion des utilisateurs avec mots de passe
INSERT INTO users (identifiant, mot_de_passe, role_id, team_id) VALUES
('admin', '123', 1, NULL),
('bts_sio', '123', 2, 1),
('organisateur1', '123', 3, 1),
('organisateur2', '123', 3, 2),
('organisateur3', '123', 3, 3),
('organisateur4', '123', 3, 4);

-- Insertion d'équipes exemple avec points
INSERT INTO teams (nom, points) VALUES
('Équipe Alpha', 150),
('Équipe Beta', 200),
('Équipe Gamma', 75),
('Équipe Delta', 180);

-- Association de l'utilisateur "bts_sio" (rôle user) à une équipe
INSERT INTO user_teams (user_id, team_id) VALUES
(2, 1); -- bts_sio dans l'équipe Alpha