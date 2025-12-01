CREATE TABLE `articles` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `titre` VARCHAR(255) NOT NULL,
    `auteur` VARCHAR(255) NOT NULL,
    `date_creation` DATE NOT NULL,
    `categorie` VARCHAR(100) NOT NULL,
    `contenu` TEXT NOT NULL,
    `image` VARCHAR(255) DEFAULT NULL,
    `tags` JSON DEFAULT NULL,
    `statut` ENUM('brouillon', 'publie', 'archive') DEFAULT 'brouillon',
    `auteur_id` INT NOT NULL,
    `lieu` VARCHAR(255) DEFAULT NULL,
    `date_soumission` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `date_publication` DATETIME DEFAULT NULL,
    `date_modification` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

