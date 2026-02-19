-- Crée l’utilisateur
CREATE USER 'oauth_user'@'localhost' IDENTIFIED BY 'motdepasse123';

-- Donne tous les droits sur ta base
GRANT ALL PRIVILEGES ON oauth_identity_provider.* TO 'oauth_user'@'localhost';

-- Recharge les privilèges
FLUSH PRIVILEGES;
