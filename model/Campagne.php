<?php
if (!class_exists('Campagne')) {
    class Campagne {
        private ?int $Id_campagne;
        private ?int $Id_utilisateur;
        private ?string $titre;
        private ?string $categorie_impact;
        private ?string $urgence;
        private ?string $description;
        private ?string $statut;
        private ?string $image_campagne;
        private ?float $objectif_montant;
        private ?float $montant_actuel;
        private ?DateTime $date_debut;
        private ?DateTime $date_fin;
        
        //Constructor modifié pour accepter les strings
        public function __construct(
            ?int $Id_campagne, 
            ?int $Id_utilisateur, 
            ?string $titre, 
            ?string $categorie_impact, 
            ?string $urgence, 
            ?string $description, 
            ?string $statut, 
            ?string $image_campagne, 
            ?float $objectif_montant, 
            ?float $montant_actuel, 
            $date_debut,  // Type flexible
            $date_fin     // Type flexible
        ) {
            $this->Id_campagne = $Id_campagne;
            $this->Id_utilisateur = $Id_utilisateur;
            $this->titre = $titre;
            $this->categorie_impact = $categorie_impact;
            $this->urgence = $urgence;
            $this->description = $description;
            $this->statut = $statut;
            $this->image_campagne = $image_campagne;
            $this->objectif_montant = $objectif_montant;
            $this->montant_actuel = $montant_actuel;
            
            // Conversion des dates
            $this->date_debut = $this->convertToDateTime($date_debut);
            $this->date_fin = $this->convertToDateTime($date_fin);
        }

        // Méthode utilitaire pour convertir en DateTime
        private function convertToDateTime($date) {
            if ($date === null) {
                return null;
            }
            if ($date instanceof DateTime) {
                return $date;
            }
            if (is_string($date)) {
                return DateTime::createFromFormat('Y-m-d', $date) ?: null;
            }
            return null;
        }

        public function show() {
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>ID Campagne</th><th>ID Utilisateur</th><th>Titre</th><th>Catégorie Impact</th><th>Urgence</th><th>Description</th><th>Statut</th><th>Image</th><th>Objectif Montant</th><th>Montant Actuel</th><th>Date Début</th><th>Date Fin</th></tr>";
            echo "<tr>";
            echo "<td>{$this->Id_campagne}</td>";
            echo "<td>{$this->Id_utilisateur}</td>";
            echo "<td>{$this->titre}</td>";
            echo "<td>{$this->categorie_impact}</td>";
            echo "<td>{$this->urgence}</td>";
            echo "<td>{$this->description}</td>";
            echo "<td>{$this->statut}</td>";
            echo "<td>{$this->image_campagne}</td>";
            echo "<td>{$this->objectif_montant}</td>";
            echo "<td>{$this->montant_actuel}</td>";
            echo "<td>" . ($this->date_debut ? $this->date_debut->format('Y-m-d') : '') . "</td>";
            echo "<td>" . ($this->date_fin ? $this->date_fin->format('Y-m-d') : '') . "</td>";
            echo "</tr>";
            echo "</table>";
        }
        
        // Getters
        public function getIdCampagne(): ?int { return $this->Id_campagne; }
        public function getIdUtilisateur(): ?int { return $this->Id_utilisateur; }
        public function getTitre(): ?string { return $this->titre; }
        public function getCategorieImpact(): ?string { return $this->categorie_impact; }
        public function getUrgence(): ?string { return $this->urgence; }
        public function getDescription(): ?string { return $this->description; }
        public function getStatut(): ?string { return $this->statut; }
        public function getImageCampagne(): ?string { return $this->image_campagne; }
        public function getObjectifMontant(): ?float { return $this->objectif_montant; }
        public function getMontantActuel(): ?float { return $this->montant_actuel; }
        public function getDateDebut(): ?DateTime { return $this->date_debut; }
        public function getDateFin(): ?DateTime { return $this->date_fin; }
        
        // Setters
        public function setIdCampagne(?int $Id_campagne): void { $this->Id_campagne = $Id_campagne; }
        public function setIdUtilisateur(?int $Id_utilisateur): void { $this->Id_utilisateur = $Id_utilisateur; }
        public function setTitre(?string $titre): void { $this->titre = $titre; }
        public function setCategorieImpact(?string $categorie_impact): void { $this->categorie_impact = $categorie_impact; }
        public function setUrgence(?string $urgence): void { $this->urgence = $urgence; }
        public function setDescription(?string $description): void { $this->description = $description; }
        public function setStatut(?string $statut): void { $this->statut = $statut; }
        public function setImageCampagne(?string $image_campagne): void { $this->image_campagne = $image_campagne; }
        public function setObjectifMontant(?float $objectif_montant): void { $this->objectif_montant = $objectif_montant; }
        public function setMontantActuel(?float $montant_actuel): void { $this->montant_actuel = $montant_actuel; }
        public function setDateDebut($date_debut): void { $this->date_debut = $this->convertToDateTime($date_debut); }
        public function setDateFin($date_fin): void { $this->date_fin = $this->convertToDateTime($date_fin); }
    }
}
?>