# Configuration Base de Données - khalilbenhamouda

## ✅ Configuration Actuelle

Le projet est configuré pour utiliser la base de données **`khalilbenhamouda`**.

### Fichier de Configuration
- **Fichier**: `CONFIGRRATION/config.php`
- **Base de données**: `khalilbenhamouda`
- **Serveur**: `localhost`
- **Utilisateur**: `root`
- **Mot de passe**: (vide)

## 📋 Étapes pour Configurer la Base de Données

### 1. Créer la Base de Données dans phpMyAdmin

1. Accédez à phpMyAdmin : `http://localhost/phpmyadmin`
2. Cliquez sur "Nouvelle base de données"
3. Nom de la base : `khalilbenhamouda`
4. Interclassement : `utf8mb4_general_ci`
5. Cliquez sur "Créer"

### 2. Importer la Structure de la Table Reclamation

**Option A : Via phpMyAdmin**
1. Sélectionnez la base de données `khalilbenhamouda`
2. Cliquez sur l'onglet "Importer"
3. Choisissez le fichier : `khalilbenhamouda_reclamation.sql`
4. Cliquez sur "Exécuter"

**Option B : Via SQL**
1. Ouvrez l'onglet "SQL" dans phpMyAdmin
2. Copiez-collez le contenu du fichier `khalilbenhamouda_reclamation.sql`
3. Cliquez sur "Exécuter"

### 3. Vérifier la Connexion

Accédez à : `http://localhost/khalil%20projt/test_connection.php`

Ce script affichera :
- ✅ Statut de la connexion
- ✅ Structure de la table `reclamation`
- ✅ Nombre de réclamations
- ✅ Liste des réclamations (si elles existent)

## 📊 Structure de la Table Reclamation

La table `reclamation` contient les champs suivants :

| Champ | Type | Description |
|-------|------|-------------|
| `id` | INT | Clé primaire (auto-increment) |
| `sujet` | VARCHAR(255) | Sujet de la réclamation |
| `description` | TEXT | Description détaillée |
| `categorie` | VARCHAR(100) | Catégorie (Technique, Service, etc.) |
| `priorite` | VARCHAR(50) | Priorité (Faible, Moyenne, Urgente) |
| `statut` | VARCHAR(50) | Statut (En attente, En cours, Résolue, Fermée) |
| `dateCreation` | DATETIME | Date de création (auto) |
| `derniereModification` | DATETIME | Date de modification (auto) |
| `utilisateurId` | INT | ID de l'utilisateur |
| `agentAttribue` | VARCHAR(255) | Nom de l'agent attribué (optionnel) |

## 🔗 Fichiers Connectés à la Base de Données

Tous les fichiers suivants utilisent la base de données `khalilbenhamouda` :

### Contrôleurs
- `controller/ReclamationController.php` - Gestion CRUD des réclamations

### Vues Backoffice
- `VIEW/backoffice/admin_dashboard.php` - Dashboard admin principal
- `VIEW/backoffice/gestion_reclamation/addReclamation.php` - Ajouter une réclamation
- `VIEW/backoffice/gestion_reclamation/updateReclamation.php` - Modifier une réclamation
- `VIEW/backoffice/gestion_reclamation/showReclamation.php` - Voir les détails
- `VIEW/backoffice/gestion_reclamation/deleteReclamation.php` - Supprimer une réclamation

### Modèles
- `MODEL/Reclamation.php` - Modèle de données

## 🚀 Accès aux Pages

### Frontoffice
- Page principale : `http://localhost/khalil%20projt/VIEW/frontoffice/index.php`
- Dashboard HTML : `http://localhost/khalil%20projt/VIEW/frontoffice/dashboard.html`

### Backoffice (Admin)
- Dashboard Admin : `http://localhost/khalil%20projt/VIEW/backoffice/admin_dashboard.php`

### Test
- Test de connexion : `http://localhost/khalil%20projt/test_connection.php`

## ⚠️ Notes Importantes

1. **Assurez-vous que la base de données existe** avant d'utiliser l'application
2. **La table `utilisateur` est nécessaire** pour les jointures dans le dashboard
3. **Les noms de colonnes sont sensibles à la casse** : utilisez exactement `id`, `sujet`, `description`, etc.
4. **Les dates sont automatiques** : `dateCreation` et `derniereModification` sont gérées automatiquement

## 🔧 Dépannage

### Erreur : "Base de données introuvable"
- Vérifiez que la base `khalilbenhamouda` existe dans phpMyAdmin
- Vérifiez le nom dans `CONFIGRRATION/config.php`

### Erreur : "Table reclamation n'existe pas"
- Exécutez le script `khalilbenhamouda_reclamation.sql`
- Vérifiez que vous êtes dans la bonne base de données

### Erreur : "Colonne introuvable"
- Vérifiez que la structure de la table correspond au script SQL
- Utilisez `test_connection.php` pour voir la structure actuelle

## 📝 Script SQL Disponible

Le fichier `khalilbenhamouda_reclamation.sql` contient :
- Création de la base de données (si elle n'existe pas)
- Création de la table `reclamation`
- Création de la table `utilisateur` (si elle n'existe pas)
- Structure compatible avec le code PHP

---

**Dernière mise à jour** : Configuration pour la base de données `khalilbenhamouda`

