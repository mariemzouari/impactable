# 🧠 Système Intelligent de Réclamations - ImpactAble

## 📋 Fonctionnalités Avancées Ajoutées

Ce document décrit les nouvelles fonctionnalités intelligentes ajoutées au système de réclamations.

---

## ✅ 1. Priorisation Automatique Intelligente

### Description
Le système analyse automatiquement le contenu des réclamations pour attribuer une priorité basée sur l'analyse sémantique.

### Comment ça fonctionne ?
- **Analyse de mots-clés** : Plus de 100 mots-clés sont détectés
- **Scoring intelligent** : Chaque mot a un score de points
- **Niveau de confiance** : Indication de la fiabilité de l'analyse

### Niveaux de priorité
| Priorité | Emoji | Score | Exemples de mots-clés |
|----------|-------|-------|----------------------|
| 🔴 Urgente | >= 15 | "urgent", "bloqué", "danger", "accident", "blessure" |
| 🟠 Moyenne | 7-14 | "problème", "difficulté", "discrimination", "inaccessible" |
| 🟢 Faible | < 7 | "suggestion", "amélioration", "question", "renseignement" |

### Fichier : `SERVICE/PrioriteIntelligente.php`

---

## 📊 2. Dashboard Statistiques Avancé

### Accès
`VIEW/backoffice/statistiques_avancees.php`

### Statistiques disponibles
- 📈 **KPIs principaux** : Total, urgentes non résolues, résolues, en attente, en cours
- 📅 **Graphique temporel** : Réclamations par jour (7 derniers jours)
- 🥧 **Graphique par priorité** : Répartition Urgent/Moyen/Faible
- 📊 **Graphique par catégorie** : Technique, Service, etc.
- 🎯 **Graphique par statut** : En attente, En cours, Résolue, Fermée
- 📈 **Évolution mensuelle** : 6 derniers mois
- 👥 **Top 5 utilisateurs** : Plus de réclamations

### Technologies utilisées
- **Chart.js** pour les graphiques interactifs
- Palette de couleurs respectée (moss, copper, brown, sage, sand)

---

## 🔍 3. Système de Suivi (Tracking)

### Accès
`VIEW/frontoffice/suivi_reclamation.php`

### Fonctionnalités
- **Timeline visuelle** : 4 étapes (Reçue → En traitement → Résolue → Clôturée)
- **Détails complets** : Sujet, catégorie, priorité, dates, agent
- **Analyse IA** : Score et confiance affichés
- **Historique des réponses** : Liste chronologique des communications

### Comment l'utiliser ?
1. Accéder à "Suivre ma Réclamation"
2. Entrer le numéro de réclamation
3. Voir l'état d'avancement en temps réel

---

## 🧪 4. Démo IA Interactive

### Accès
`VIEW/frontoffice/demo_ia.php`

### Description
Page de démonstration permettant de tester le système de priorisation intelligente avant de soumettre une réclamation.

### Fonctionnalités
- Analyse en temps réel du texte
- Affichage des mots-clés détectés
- Score et niveau de confiance
- Exemples pré-remplis à tester

---

## 🔌 5. API d'Analyse

### Endpoint
`VIEW/frontoffice/api_analyse_priorite.php`

### Utilisation
```
GET/POST: api_analyse_priorite.php?texte=Votre texte ici&categorie=Technique
```

### Réponse JSON
```json
{
  "success": true,
  "resultat": {
    "priorite": "Urgente",
    "priorite_icon": "🔴",
    "score": 25,
    "confiance": "87%",
    "mots_detectes": [...]
  },
  "interpretation": "Cette réclamation nécessite une attention IMMÉDIATE..."
}
```

---

## 🎨 Respect de la Palette de Couleurs

Toutes les nouvelles fonctionnalités utilisent la palette existante :

```css
--brown: #4b2e16;    /* Marron principal */
--copper: #b47b47;   /* Cuivre */
--moss: #5e6d3b;     /* Vert mousse */
--sage: #a9b97d;     /* Sauge */
--sand: #f4ecdd;     /* Sable */
--white: #fffaf5;    /* Blanc cassé */
```

---

## 📁 Nouveaux Fichiers Créés

```
khalilprojt/
├── SERVICE/
│   ├── PrioriteIntelligente.php    # Classe d'analyse IA
│   └── index.php                    # Protection
├── VIEW/
│   ├── backoffice/
│   │   └── statistiques_avancees.php  # Dashboard stats
│   └── frontoffice/
│       ├── suivi_reclamation.php      # Page de suivi
│       ├── demo_ia.php                # Démo interactive
│       └── api_analyse_priorite.php   # API JSON
```

---

## 🚀 Comment Tester

### 1. Tester la priorisation automatique
1. Aller sur la page d'accueil
2. Soumettre une réclamation avec des mots-clés urgents
3. Observer la priorité suggérée par l'IA

### 2. Voir les statistiques
1. Aller sur Dashboard Admin
2. Cliquer sur "Statistiques IA"
3. Explorer les différents graphiques

### 3. Tester le suivi
1. Cliquer sur "Suivre ma Réclamation"
2. Entrer un numéro de réclamation existant
3. Voir la timeline et l'historique

### 4. Tester la démo IA
1. Cliquer sur "Démo IA"
2. Entrer différents textes
3. Observer les variations de priorité

---

## ✨ Améliorations Futures Possibles

- [ ] Apprentissage automatique basé sur les corrections manuelles
- [ ] Analyse de sentiment plus avancée
- [ ] Notifications par email automatiques
- [ ] Export des statistiques en PDF
- [ ] Comparaison avec les périodes précédentes

---

**Développé pour ImpactAble** 🎯

