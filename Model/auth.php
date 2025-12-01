<?php

require_once '../config.php';

class Auth {
    public $pdo;

    public function __construct() {
        $this->pdo = config::getConnexion();
    }

    // Inscription d'un nouvel utilisateur
    public function register($nom, $prenom, $email, $mot_de_passe, $telephone = '', $date_naissance = null, $handicap = '') {
        try {
            error_log("🔔 Tentative d'inscription: $email");
            
            // Vérifier si l'email existe déjà
            if ($this->emailExists($email)) {
                error_log("❌ Email déjà utilisé: $email");
                return ['success' => false, 'message' => 'Cet email est déjà utilisé'];
            }

            // CORRECTION : Utiliser 'numero_tel' au lieu de 'telephone'
            $sql = "INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, numero_tel, date_naissance, type_handicap, role) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, 'user')";
            $stmt = $this->pdo->prepare($sql);
            $hashedPassword = password_hash($mot_de_passe, PASSWORD_DEFAULT);
            
            if ($stmt->execute([$nom, $prenom, $email, $hashedPassword, $telephone, $date_naissance, $handicap])) {
                $newUserId = $this->pdo->lastInsertId();
                error_log("✅ Utilisateur créé avec ID: $newUserId - $prenom $nom ($email)");
                return ['success' => true, 'message' => 'Inscription réussie', 'user_id' => $newUserId];
            }
            
            return ['success' => false, 'message' => 'Erreur lors de l\'inscription'];
        } catch (PDOException $e) {
            error_log("❌ Erreur register: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors de l\'inscription'];
        }
    }

    // Connexion d'un utilisateur - CORRIGÉ AVEC SESSION
    public function login($email, $mot_de_passe) {
        try {
            $sql = "SELECT * FROM utilisateur WHERE email = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($mot_de_passe, $user['mot_de_passe'])) {
                // STOCKAGE EN SESSION - CORRECTION CRITIQUE
                $_SESSION['user_id'] = $user['Id_utilisateur'];
                $_SESSION['user_name'] = $user['prenom'] . ' ' . $user['nom'];
                $_SESSION['is_admin'] = ($user['role'] === 'admin');
                $_SESSION['user_email'] = $user['email'];
                
                error_log("✅ Connexion réussie: " . $_SESSION['user_name'] . " (ID: " . $_SESSION['user_id'] . ")");
                
                return [
                    'success' => true,
                    'user' => [
                        'Id_utilisateur' => $user['Id_utilisateur'],
                        'nom' => $user['nom'],
                        'prenom' => $user['prenom'],
                        'email' => $user['email'],
                        'role' => $user['role']
                    ]
                ];
            }

            return ['success' => false, 'message' => 'Email ou mot de passe incorrect'];
        } catch (PDOException $e) {
            error_log("❌ Erreur login: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors de la connexion'];
        }
    }

    // Vérifier si un email existe
    public function emailExists($email) {
        try {
            $sql = "SELECT Id_utilisateur FROM utilisateur WHERE email = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$email]);
            return $stmt->fetch() !== false;
        } catch (PDOException $e) {
            error_log("❌ Erreur emailExists: " . $e->getMessage());
            return false;
        }
    }

    // Validation des données d'inscription
    public function validateRegistration($nom, $prenom, $email, $mot_de_passe, $confirmer_mot_de_passe) {
        $errors = [];

        if (empty(trim($nom))) {
            $errors[] = "Le nom est obligatoire";
        }

        if (empty(trim($prenom))) {
            $errors[] = "Le prénom est obligatoire";
        }

        if (empty(trim($email))) {
            $errors[] = "L'email est obligatoire";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "L'email n'est pas valide";
        }

        if (empty($mot_de_passe)) {
            $errors[] = "Le mot de passe est obligatoire";
        } elseif (strlen($mot_de_passe) < 6) {
            $errors[] = "Le mot de passe doit contenir au moins 6 caractères";
        }

        if ($mot_de_passe !== $confirmer_mot_de_passe) {
            $errors[] = "Les mots de passe ne correspondent pas";
        }

        return $errors;
    }

    // Validation des données de connexion
    public function validateLogin($email, $mot_de_passe) {
        $errors = [];

        if (empty(trim($email))) {
            $errors[] = "L'email est obligatoire";
        }

        if (empty($mot_de_passe)) {
            $errors[] = "Le mot de passe est obligatoire";
        }

        return $errors;
    }
}
?>