DROP TABLE IF EXISTS participation;

CREATE TABLE participation (
  id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  id_evenement INT NOT NULL,
  id_utilisateur INT NOT NULL,
  statut ENUM('en_attente','confirmé','annulé') NOT NULL DEFAULT 'en_attente',
  message TEXT DEFAULT NULL,
  besoins_accessibilite SET('lsf','sous_titrage','documents_accessibles','stationnement_adapte','assistance_personnelle','aucun_besoin') NOT NULL DEFAULT 'aucun_besoin',
  nombre_accompagnants INT NOT NULL DEFAULT 0,
  date_inscription DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY unique_participation (id_evenement, id_utilisateur),
  KEY idx_participation_event (id_evenement),
  KEY idx_participation_user (id_utilisateur),
  CONSTRAINT participation_ibfk_1 FOREIGN KEY (id_evenement) REFERENCES evenements(id) ON DELETE CASCADE,
  CONSTRAINT participation_ibfk_2 FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

SELECT 'Table participation recréée avec succès!' AS status;
