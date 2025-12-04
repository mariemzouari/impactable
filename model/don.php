<?php
// projet/model/Don.php
if (!class_exists('Don')) {
    class Don {
        private $Id_don;
        private $Id_campagne;
        private $Id_utilisateur;
        private $montant;
        private $message;
        private $methode_paiment;
        private $email_donateur;
        private $nom_donateur;
        private $telephone;
        private $code_verification;
        private $numero_reçu;
        private $date_don;
        private $date_verification;
        private $date_confirmation;
        private $statut;

        // Constructeur complet
        public function __construct(
            $Id_don = null,
            $Id_campagne = null,
            $Id_utilisateur = null,
            $montant = 0.0,
            $message = '',
            $methode_paiment = '',
            $email_donateur = '',
            $nom_donateur = '',
            $telephone = '',
            $code_verification = '',
            $numero_reçu = '',
            $date_don = null,
            $date_verification = null,
            $date_confirmation = null,
            $statut = 'en_attente'
        ) {
            $this->Id_don = $Id_don;
            $this->Id_campagne = $Id_campagne;
            $this->Id_utilisateur = $Id_utilisateur;
            $this->montant = $montant;
            $this->message = $message;
            $this->methode_paiment = $methode_paiment;
            $this->email_donateur = $email_donateur;
            $this->nom_donateur = $nom_donateur;
            $this->telephone = $telephone;
            $this->code_verification = $code_verification;
            $this->numero_reçu = $numero_reçu;
            $this->date_don = $date_don ?: date('Y-m-d H:i:s');
            $this->date_verification = $date_verification;
            $this->date_confirmation = $date_confirmation;
            $this->statut = $statut;
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
        public function getTelephone() { return $this->telephone; }
        public function getCodeVerification() { return $this->code_verification; }
        public function getNumeroReçu() { return $this->numero_reçu; }
        public function getDateDon() { return $this->date_don; }
        public function getDateVerification() { return $this->date_verification; }
        public function getDateConfirmation() { return $this->date_confirmation; }
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
        public function setTelephone($value) { $this->telephone = $value; }
        public function setCodeVerification($value) { $this->code_verification = $value; }
        public function setNumeroReçu($value) { $this->numero_reçu = $value; }
        public function setDateDon($value) { $this->date_don = $value; }
        public function setDateVerification($value) { $this->date_verification = $value; }
        public function setDateConfirmation($value) { $this->date_confirmation = $value; }
        public function setStatut($value) { $this->statut = $value; }

        // Méthode utilitaire
        public function isVerifie() {
            return $this->statut === 'confirmé';
        }
        
        public function getStatutColor() {
            switch($this->statut) {
                case 'confirmé': return 'success';
                case 'en_attente': return 'warning';
                case 'annulé': return 'danger';
                default: return 'secondary';
            }
        }
    }
}
?>