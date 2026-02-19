-- Pour enregistrer les utilisateurs
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Pour enregistrer les applications tierces qui vont utiliser ton OAuth2.
CREATE TABLE oauth_client (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    client_id VARCHAR(100) NOT NULL UNIQUE,
    client_secret VARCHAR(255) NOT NULL,
    redirect_uri VARCHAR(500) NOT NULL,
    name VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Pour stocker les tokens d’accès délivrés à un utilisateur pour un client.
CREATE TABLE oauth_access_token (
    token VARCHAR(255) PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    client_id BIGINT UNSIGNED NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (client_id) REFERENCES oauth_client(id) ON DELETE CASCADE
);

-- Si tu veux gérer les refresh tokens pour renouveler les access tokens.
CREATE TABLE oauth_refresh_token (
    token VARCHAR(255) PRIMARY KEY,
    access_token VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (access_token) REFERENCES oauth_access_token(token) ON DELETE CASCADE
);

-- Si tu veux gérer des permissions fines (read, write, admin, etc.)
CREATE TABLE oauth_scope (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(255)
);

-- Table de jointure
CREATE TABLE oauth_access_token_scope (
    access_token VARCHAR(255) NOT NULL,
    scope_id BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (access_token, scope_id),
    FOREIGN KEY (access_token) REFERENCES oauth_access_token(token) ON DELETE CASCADE,
    FOREIGN KEY (scope_id) REFERENCES oauth_scope(id) ON DELETE CASCADE
);
