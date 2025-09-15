-- Base de données MySQL pour Maria Curia
-- Exécuter dans phpMyAdmin ou ligne de commande MySQL

CREATE DATABASE IF NOT EXISTS maria_curia;
USE maria_curia;

-- Table des équipes
CREATE TABLE teams (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom_equipe VARCHAR(100) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    score INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Table des personnages
CREATE TABLE characters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(200) NOT NULL,
    description TEXT,
    qr_code VARCHAR(50) NOT NULL UNIQUE,
    points INT DEFAULT 100
) ENGINE=InnoDB;

-- Table des scans
CREATE TABLE scans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    team_id INT,
    character_id INT,
    scanned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE,
    FOREIGN KEY (character_id) REFERENCES characters(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Insertion des équipes de test
INSERT INTO teams (nom_equipe, mot_de_passe) VALUES
-- ('Equipe1', 'pass1'),  Example


-- Insertion des personnages
INSERT INTO characters (nom, description, qr_code, points) VALUES
-- ('Marie Curie', 'Physicienne et chimiste française, prix Nobel de physique et de chimie', 'MARIE_CURIE_001', 100),  Example
