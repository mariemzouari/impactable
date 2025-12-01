# 🚀 Guide Rapide - Répondre aux Réclamations

## ⚡ Installation en 3 Étapes

### 1️⃣ Créer la Table (1 minute)

Ouvrez phpMyAdmin et exécutez ce SQL :

```sql
USE `khalilbenhamouda`;

CREATE TABLE IF NOT EXISTS `reponse` (
  `Id_reponse` INT(11) NOT NULL AUTO_INCREMENT,
  `Id_reclamation` INT(11) NOT NULL,
  `Id_utilisateur` INT(11) NOT NULL,
  `message` TEXT NOT NULL,
  `piece_jointe` VARCHAR(255) DEFAULT NULL,
  `type_reponse` ENUM('premiere','suivi','resolution') NOT NULL DEFAULT 'premiere',
  `date_reponse` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`Id_reponse`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 2️⃣ Vérifier les Fichiers

Tous les fichiers sont déjà créés ! ✅

### 3️⃣ Tester

1. Allez sur : `http://localhost/khalil%20projt/VIEW/backoffice/admin_dashboard.php`
2. Cliquez sur le bouton vert **"Répondre"** d'une réclamation
3. Écrivez votre réponse et cliquez sur **"Envoyer la Réponse"**
4. ✅ C'est fait !

## 📍 Accès Rapide

### Depuis le Dashboard Admin
```
Dashboard → Bouton "Répondre" (vert) → Formulaire
```

### Depuis la Page de Détails
```
Voir Réclamation → Bouton "Répondre" → Formulaire
```

### Voir Toutes les Réponses
```
Dashboard → Bouton "Voir" → Bouton "Voir les Réponses"
```

## 🎯 URLs Directes

- **Dashboard Admin** : `VIEW/backoffice/admin_dashboard.php`
- **Ajouter Réponse** : `VIEW/backoffice/reponsecrud/ajouter_reponse.php?reclamation_id=X`
- **Liste Réponses** : `VIEW/backoffice/reponsecrud/liste_reponses.php?reclamation_id=X`

## 💡 Astuces

- Les réponses sont affichées du plus récent au plus ancien
- Vous pouvez voir les réponses précédentes en bas du formulaire
- Le nombre de réponses est affiché dans la page de liste

## ✅ Tout Fonctionne !

Le système est **100% opérationnel** et prêt à l'emploi !
