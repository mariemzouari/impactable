<?php
class Campagne {
    private $Id_campagne;
    private $Id_utilisateur;
    private $titre;
    private $categorie_impact;
    private $urgence;
    private $description;
    private $statut;
    private $image_campagne;
    private $objectif_montant;
    private $montant_actuel;
    private $date_debut;
    private $date_fin;
    
    public function __construct($Id_campagne, $Id_utilisateur, $titre, $categorie_impact, $urgence, $description, $statut, $image_campagne, $objectif_montant, $montant_actuel, $date_debut, $date_fin) {
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
        $this->date_debut = $date_debut;
        $this->date_fin = $date_fin;
    }
    
    // Getters
    public function getIdCampagne() { return $this->Id_campagne; }
    public function getIdUtilisateur() { return $this->Id_utilisateur; }
    public function getTitre() { return $this->titre; }
    public function getCategorieImpact() { return $this->categorie_impact; }
    public function getUrgence() { return $this->urgence; }
    public function getDescription() { return $this->description; }
    public function getStatut() { return $this->statut; }
    public function getImageCampagne() { return $this->image_campagne; }
    public function getObjectifMontant() { return $this->objectif_montant; }
    public function getMontantActuel() { return $this->montant_actuel; }
    public function getDateDebut() { return $this->date_debut; }
    public function getDateFin() { return $this->date_fin; }
    
    // Setters
    public function setTitre($titre) { $this->titre = $titre; }
    public function setCategorieImpact($categorie_impact) { $this->categorie_impact = $categorie_impact; }
    public function setUrgence($urgence) { $this->urgence = $urgence; }
    public function setDescription($description) { $this->description = $description; }
    public function setStatut($statut) { $this->statut = $statut; }
    public function setObjectifMontant($objectif_montant) { $this->objectif_montant = $objectif_montant; }
    public function setDateDebut($date_debut) { $this->date_debut = $date_debut; }
    public function setDateFin($date_fin) { $this->date_fin = $date_fin; }
}
?>