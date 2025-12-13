<?php

class Utilisateur {

    private ?int $Id_utilisateur;
    private ?string $nom;
    private ?string $prenom;
    private string $genre;
    private ?string $date_naissance;
    private ?string $email;
    private ?string $numero_tel;
    private ?string $mot_de_passe;
    private string $role;
    private string $type_handicap;
    private int $blocked;
    private string $date_inscription;



    // constructeur
   public function __construct($data = []) {

    // valeur par defaut de la base
    $this->Id_utilisateur   = $data['Id_utilisateur'] ?? null;
    $this->nom              = $data['nom'] ?? null;   // obligatoire 
    $this->prenom           = $data['prenom'] ?? null; // obligatoire
    $this->genre            = $data['genre'] ?? 'prefere_ne_pas_dire';
    $this->date_naissance   = $data['date_naissance'] ?? null;
    $this->email            = $data['email'] ?? null; // obligatoire
    $this->numero_tel       = $data['numero_tel'] ?? null;  //obligatoire
    $this->mot_de_passe     = $data['mot_de_passe'] ?? null; // obligatoire
    $this->role             = $data['role'] ?? 'user';
    $this->type_handicap    = $data['type_handicap'] ?? 'aucun';
    $this->blocked          = $data['blocked'] ?? 0;
    $this->date_inscription = $data['date_inscription'] ?? date("Y-m-d H:i:s");
}

    // getters
    public function getId_utilisateur() { return $this->Id_utilisateur; }
    public function getNom() { return $this->nom; }
    public function getPrenom() { return $this->prenom; }
    public function getGenre() { return $this->genre; }
    public function getDate_naissance() { return $this->date_naissance; }
    public function getEmail() { return $this->email; }
    public function getNumero_tel() { return $this->numero_tel; }
    public function getMot_de_passe() { return $this->mot_de_passe; }
    public function getRole() { return $this->role; }
    public function getType_handicap() { return $this->type_handicap; }
    public function getBlocked() { return $this->blocked; }
    public function getDate_inscription() { return $this->date_inscription; }

    // setters
    public function setId_utilisateur($id) { $this->Id_utilisateur = $id; }
    public function setNom($nom) { $this->nom = $nom; }
    public function setPrenom($prenom) { $this->prenom = $prenom; }
    public function setGenre($genre) { $this->genre = $genre; }
    public function setDate_naissance($date) { $this->date_naissance = $date; }
    public function setEmail($email) { $this->email = $email; }
    public function setNumero_tel($tel) { $this->numero_tel = $tel; }
    public function setMot_de_passe($mdp) { $this->mot_de_passe = $mdp; }
    public function setRole($role) { $this->role = $role; }
    public function setType_handicap($type) { $this->type_handicap = $type; }
    public function setBlocked($blocked) { $this->blocked = $blocked; }
    public function setDate_inscription($date) { $this->date_inscription = $date; }





    // DB helper to fetch a user by id (returns associative array) to match legacy controller usage
    public function getById($id) {
        try {
            if (!class_exists('Database')) {
                require_once __DIR__ . '/Database.php';
            }
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT * FROM utilisateur WHERE Id_utilisateur = ?");
            $stmt->execute([(int)$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('Utilisateur::getById error: ' . $e->getMessage());
            return null;
        }
    }

    // Authenticate user by email and password
    public function connecter($email, $mot_de_passe) {
        try {
            if (!class_exists('Database')) {
                require_once __DIR__ . '/Database.php';
            }
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT * FROM utilisateur WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user && isset($user['mot_de_passe'])) {
                if (password_verify($mot_de_passe, $user['mot_de_passe'])) {
                    return $user;
                }
            }
            return false;
        } catch (Exception $e) {
            error_log('Utilisateur::connecter error: ' . $e->getMessage());
            return false;
        }
    }

}
