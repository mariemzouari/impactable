-- Script complet pour mettre à jour la table reclamation avec tous les attributs nécessaires
-- À exécuter dans phpMyAdmin sur la base de données khalilbenhamouda

USE `khalilbenhamouda`;

-- Vérifier et ajouter chaque champ s'il n'existe pas
SET @dbname = DATABASE();
SET @tablename = "reclamation";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = 'image')
  ) > 0,
  "SELECT 'Column image already exists.'",
  "ALTER TABLE reclamation ADD COLUMN image VARCHAR(255) DEFAULT NULL AFTER agentAttribue"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = 'nom')
  ) > 0,
  "SELECT 'Column nom already exists.'",
  "ALTER TABLE reclamation ADD COLUMN nom VARCHAR(100) DEFAULT NULL AFTER image"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = 'prenom')
  ) > 0,
  "SELECT 'Column prenom already exists.'",
  "ALTER TABLE reclamation ADD COLUMN prenom VARCHAR(100) DEFAULT NULL AFTER nom"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = 'email')
  ) > 0,
  "SELECT 'Column email already exists.'",
  "ALTER TABLE reclamation ADD COLUMN email VARCHAR(255) DEFAULT NULL AFTER prenom"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = 'telephone')
  ) > 0,
  "SELECT 'Column telephone already exists.'",
  "ALTER TABLE reclamation ADD COLUMN telephone VARCHAR(20) DEFAULT NULL AFTER email"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = 'lieu')
  ) > 0,
  "SELECT 'Column lieu already exists.'",
  "ALTER TABLE reclamation ADD COLUMN lieu VARCHAR(255) DEFAULT NULL AFTER telephone"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = 'dateIncident')
  ) > 0,
  "SELECT 'Column dateIncident already exists.'",
  "ALTER TABLE reclamation ADD COLUMN dateIncident DATE DEFAULT NULL AFTER lieu"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = 'typeHandicap')
  ) > 0,
  "SELECT 'Column typeHandicap already exists.'",
  "ALTER TABLE reclamation ADD COLUMN typeHandicap VARCHAR(100) DEFAULT NULL AFTER dateIncident"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = 'personnesImpliquees')
  ) > 0,
  "SELECT 'Column personnesImpliquees already exists.'",
  "ALTER TABLE reclamation ADD COLUMN personnesImpliquees TEXT DEFAULT NULL AFTER typeHandicap"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = 'temoins')
  ) > 0,
  "SELECT 'Column temoins already exists.'",
  "ALTER TABLE reclamation ADD COLUMN temoins TEXT DEFAULT NULL AFTER personnesImpliquees"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = 'actionsPrecedentes')
  ) > 0,
  "SELECT 'Column actionsPrecedentes already exists.'",
  "ALTER TABLE reclamation ADD COLUMN actionsPrecedentes TEXT DEFAULT NULL AFTER temoins"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = 'solutionSouhaitee')
  ) > 0,
  "SELECT 'Column solutionSouhaitee already exists.'",
  "ALTER TABLE reclamation ADD COLUMN solutionSouhaitee TEXT DEFAULT NULL AFTER actionsPrecedentes"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

SELECT 'Mise à jour de la table reclamation terminée avec succès!' as Resultat;

