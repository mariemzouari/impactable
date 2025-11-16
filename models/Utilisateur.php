<?php
class Utilisateur {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function connecter($email, $mot_de_passe) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM utilisateur WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user) {
                // Vérifier d'abord si c'est un mot de passe haché
                if (password_verify($mot_de_passe, $user['mot_de_passe'])) {
                    return $user;
                }
                // Sinon vérifier en texte clair
                elseif ($user['mot_de_passe'] === $mot_de_passe) {
                    // Si le mot de passe est en texte clair, le hacher pour la prochaine fois
                    $this->hacherMotDePasse($user['Id_utilisateur'], $mot_de_passe);
                    return $user;
                }
            }
            return false;
        } catch(PDOException $e) {
            error_log("Erreur connexion utilisateur: " . $e->getMessage());
            return false;
        }
    }
    
    private function hacherMotDePasse($userId, $mot_de_passe) {
        try {
            $mot_de_passe_hache = password_hash($mot_de_passe, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("UPDATE utilisateur SET mot_de_passe = ? WHERE Id_utilisateur = ?");
            return $stmt->execute([$mot_de_passe_hache, $userId]);
        } catch(PDOException $e) {
            error_log("Erreur hachage mot de passe: " . $e->getMessage());
            return false;
        }
    }
    
    public function creer($data) {
        try {
            $sql = "INSERT INTO utilisateur (nom, prenom, genre, date_naissance, email, numero_tel, mot_de_passe, role, type_handicap) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute([
                $data['nom'],
                $data['prenom'],
                $data['genre'],
                $data['date_naissance'],
                $data['email'],
                $data['numero_tel'],
                password_hash($data['mot_de_passe'], PASSWORD_DEFAULT),
                $data['role'] ?? 'candidat',
                $data['type_handicap'] ?? NULL
            ]);
        } catch(PDOException $e) {
            error_log("Erreur création utilisateur: " . $e->getMessage());
            return false;
        }
    }
    
    public function getById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM utilisateur WHERE Id_utilisateur = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch(PDOException $e) {
            error_log("Erreur récupération utilisateur: " . $e->getMessage());
            return null;
        }
    }
    
    public function emailExiste($email) {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM utilisateur WHERE email = ?");
            $stmt->execute([$email]);
            return $stmt->fetch()['count'] > 0;
        } catch(PDOException $e) {
            error_log("Erreur vérification email: " . $e->getMessage());
            return false;
        }
    }
}
?>
