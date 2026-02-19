-- =====================================================
-- Database: oauth_identity_provider
-- Serveur OAuth2 / OIDC minimal avec UUID pour users
-- =====================================================

CREATE DATABASE IF NOT EXISTS elegance_oauth;
USE elegance_oauth;

-- -----------------------------------------------------
-- Table: users
-- Stocke les comptes utilisateurs
-- -----------------------------------------------------
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);