<?php
require_once __DIR__ . '/../config.php';

class UserModel {
    public $pdo;

    public function __construct() {
        $this->pdo = config::getConnexion();
    }

    public function register($nom, $prenom, $email, $mot_de_passe, $telephone = '', $date_naissance = null, $handicap = '') {
        try {
            // Check if email exists
            if ($this->emailExists($email)) {
                return ['success' => false, 'message' => 'Cet email est déjà utilisé'];
            }

            $sql = "INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, numero_tel, date_naissance, type_handicap, role) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, 'user')";
            $stmt = $this->pdo->prepare($sql);
            $hashedPassword = password_hash($mot_de_passe, PASSWORD_DEFAULT);
            
            if ($stmt->execute([$nom, $prenom, $email, $hashedPassword, $telephone, $date_naissance, $handicap])) {
                $newUserId = $this->pdo->lastInsertId();
                return ['success' => true, 'message' => 'Inscription réussie', 'user_id' => $newUserId];
            }
            
            return ['success' => false, 'message' => 'Erreur lors de l\'inscription'];
        } catch (PDOException $e) {
            error_log("Erreur register: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors de l\'inscription'];
        }
    }

    public function login($email, $mot_de_passe) {
        try {
            $sql = "SELECT * FROM utilisateur WHERE email = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($mot_de_passe, $user['mot_de_passe'])) {
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
            error_log("Erreur login: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors de la connexion'];
        }
    }

    public function emailExists($email) {
        try {
            $sql = "SELECT Id_utilisateur FROM utilisateur WHERE email = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$email]);
            return $stmt->fetch() !== false;
        } catch (PDOException $e) {
            return false;
        }
    }

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
