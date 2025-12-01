# Instructions - Ajout des Attributs pour les Réclamations

## ✅ Modifications Effectuées

### 1. Base de Données
- **Script SQL créé** : `add_reclamation_fields.sql`
- **Champs ajoutés** :
  - `image` (VARCHAR 255) - Chemin de l'image
  - `nom` (VARCHAR 100) - Nom de l'utilisateur
  - `prenom` (VARCHAR 100) - Prénom de l'utilisateur
  - `email` (VARCHAR 255) - Email de l'utilisateur
  - `telephone` (VARCHAR 20) - Téléphone de l'utilisateur
  - `lieu` (VARCHAR 255) - Lieu de l'incident
  - `dateIncident` (DATE) - Date de l'incident
  - `typeHandicap` (VARCHAR 100) - Type de handicap
  - `personnesImpliquees` (TEXT) - Personnes impliquées
  - `temoins` (TEXT) - Témoins
  - `actionsPrecedentes` (TEXT) - Actions déjà entreprises
  - `solutionSouhaitee` (TEXT) - Solution souhaitée

### 2. Modèle Reclamation (`MODEL/Reclamation.php`)
- ✅ Tous les nouveaux attributs ajoutés
- ✅ Getters et setters créés
- ✅ Constructeur mis à jour

### 3. Contrôleur (`controller/ReclamationController.php`)
- ✅ Méthode `addReclamation()` mise à jour
- ✅ Méthode `updateReclamation()` mise à jour

### 4. Formulaire Frontoffice (`VIEW/frontoffice/index.php`)
- ✅ Formulaire complet avec toutes les sections :
  - Section 1: Informations Personnelles (nom, prénom, email, téléphone)
  - Section 2: Informations de la Réclamation (sujet, catégorie, description, priorité)
  - Section 3: Détails de l'Incident (lieu, date, type de handicap)
  - Section 4: Personnes Impliquées (personnes impliquées, témoins)
  - Section 5: Actions et Solutions (actions précédentes, solution souhaitée)
  - Section 6: Pièce Jointe (image)

### 5. Script de Traitement (`VIEW/frontoffice/submit_reclamation.php`)
- ✅ Gestion de tous les nouveaux champs
- ✅ Validation des champs obligatoires
- ✅ Upload d'image fonctionnel

## 📋 Étapes pour Finaliser

### Étape 1 : Mettre à jour la Base de Données

**Option A : Via phpMyAdmin (Recommandé)**
1. Ouvrir phpMyAdmin : `http://localhost/phpmyadmin`
2. Sélectionner la base `khalilbenhamouda`
3. Cliquer sur l'onglet "SQL"
4. Copier-coller le contenu de `add_reclamation_fields.sql`
5. Cliquer sur "Exécuter"

**Option B : Via le script automatique**
1. Accéder à : `http://localhost/khalil%20projt/setup_database.php`
2. Le script créera automatiquement tous les champs

### Étape 2 : Vérifier

Accéder à : `http://localhost/khalil%20projt/test_connection.php`
- Vérifier que tous les champs sont présents dans la table

## 📝 Champs du Formulaire

### Champs Obligatoires (*)
- Nom
- Prénom
- Email
- Téléphone
- Sujet
- Catégorie
- Description
- Priorité
- Lieu
- Date de l'incident
- Solution souhaitée

### Champs Optionnels
- Type de handicap
- Personnes impliquées
- Témoins
- Actions précédentes
- Image

## 🔄 Structure Complète de la Table

```sql
CREATE TABLE `reclamation` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `sujet` VARCHAR(255) NOT NULL,
    `description` TEXT NOT NULL,
    `categorie` VARCHAR(100) NOT NULL,
    `priorite` VARCHAR(50) NOT NULL,
    `statut` VARCHAR(50) NOT NULL,
    `dateCreation` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `derniereModification` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `utilisateurId` INT NOT NULL,
    `agentAttribue` VARCHAR(255) DEFAULT NULL,
    `image` VARCHAR(255) DEFAULT NULL,
    `nom` VARCHAR(100) DEFAULT NULL,
    `prenom` VARCHAR(100) DEFAULT NULL,
    `email` VARCHAR(255) DEFAULT NULL,
    `telephone` VARCHAR(20) DEFAULT NULL,
    `lieu` VARCHAR(255) DEFAULT NULL,
    `dateIncident` DATE DEFAULT NULL,
    `typeHandicap` VARCHAR(100) DEFAULT NULL,
    `personnesImpliquees` TEXT DEFAULT NULL,
    `temoins` TEXT DEFAULT NULL,
    `actionsPrecedentes` TEXT DEFAULT NULL,
    `solutionSouhaitee` TEXT DEFAULT NULL
);
```

## ✅ Tout est Prêt !

Une fois le script SQL exécuté, le formulaire frontoffice permettra aux utilisateurs de remplir tous les attributs nécessaires pour une réclamation complète.

