

CREATE TABLE IF NOT EXISTS utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    niveau_permission INT NOT NULL DEFAULT 0,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    CHECK (niveau_permission >= 0 AND niveau_permission <= 3)
);

CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS produits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    quantite INT NOT NULL DEFAULT 0,
    categorie_id INT NOT NULL,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (categorie_id) REFERENCES categories(id) ON DELETE RESTRICT,
    CHECK (quantite >= 0)
);

INSERT INTO categories (nom, description) VALUES
('Viande', 'Tous types de viandes'),
('Légumes', 'Légumes frais et conserves'),
('Boissons non alcoolisées', 'Sodas, jus, eau'),
('Alcool', 'Vins, bières et spiritueux'),
('Épicerie', 'Produits secs et conserves'),
('Produits laitiers', 'Lait, fromage, yaourts');

INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, niveau_permission) VALUES
('Admin', 'System', 'admin@restaurant.fr', '$2y$10$YourHashedPasswordHere', 3);
