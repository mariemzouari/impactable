<?php
class Don {
    private $Id_don;
    private $Id_campagne;
    private $Id_utilisateur;
    private $montant;
    private $message;
    private $methode_paiment;
    private $email_donateur;
    private $nom_donateur;
    private $numero_reçu;
    private $date_don;
    private $statut;

    // Constructeur simple
    public function __construct() {
        // Constructeur vide pour une création flexible
    }

    // Getters
    public function getIdDon() { return $this->Id_don; }
    public function getIdCampagne() { return $this->Id_campagne; }
    public function getIdUtilisateur() { return $this->Id_utilisateur; }
    public function getMontant() { return $this->montant; }
    public function getMessage() { return $this->message; }
    public function getMethodePaiment() { return $this->methode_paiment; }
    public function getEmailDonateur() { return $this->email_donateur; }
    public function getNomDonateur() { return $this->nom_donateur; }
    public function getNumeroReçu() { return $this->numero_reçu; }
    public function getDateDon() { return $this->date_don; }
    public function getStatut() { return $this->statut; }

    // Setters
    public function setIdDon($value) { $this->Id_don = $value; }
    public function setIdCampagne($value) { $this->Id_campagne = $value; }
    public function setIdUtilisateur($value) { $this->Id_utilisateur = $value; }
    public function setMontant($value) { $this->montant = $value; }
    public function setMessage($value) { $this->message = $value; }
    public function setMethodePaiment($value) { $this->methode_paiment = $value; }
    public function setEmailDonateur($value) { $this->email_donateur = $value; }
    public function setNomDonateur($value) { $this->nom_donateur = $value; }
    public function setNumeroReçu($value) { $this->numero_reçu = $value; }
    public function setDateDon($value) { $this->date_don = $value; }
    public function setStatut($value) { $this->statut = $value; }
}
?>