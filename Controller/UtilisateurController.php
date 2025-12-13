<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Model/UtilisateurClass.php';

class UtilisateurController {

 
    // lister les utilisateurs
 
    public function listUsers($search) {

        if (!empty($search)){
            $sql = "SELECT u.*, p.photo_profil FROM utilisateur u
            LEFT JOIN profil p ON p.Id_utilisateur = u.Id_utilisateur 
            WHERE LOWER(u.nom) LIKE LOWER(:search) OR LOWER(u.prenom) LIKE LOWER(:search)";
            $db = config::getConnexion();
            
            try {
            $query = $db->prepare($sql);
            $query->execute(['search' => '%' . $search . '%']);
            return $query->fetchAll();
             } 
            catch (Exception $e) {
            die('Error: ' . $e->getMessage());
             }
        }
        else {

        $sql = "SELECT u.*, p.photo_profil FROM utilisateur u
        LEFT JOIN profil p ON p.Id_utilisateur = u.Id_utilisateur"; //jointure pour lister dans backoffice
        $db = config::getConnexion();
        try {
            $list = $db->query($sql);
            return $list;
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }}

    }

 
    // supprimer  utilisateur par id
 
    public function deleteUser($id) {
        $sql = "DELETE FROM utilisateur WHERE Id_utilisateur = :id";
        $db = config::getConnexion();
        $req = $db->prepare($sql);
        $req->bindValue(':id', $id);
        try {
            $req->execute();
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    // add user 
    public function addUser(Utilisateur $user) {

    
    $sql = "INSERT INTO utilisateur 
        (nom, prenom, genre, date_naissance, email, numero_tel, mot_de_passe, role, type_handicap) 
        VALUES 
        (:nom, :prenom, :genre, :date_naissance, :email, :numero_tel, :mot_de_passe, :role, :type_handicap)";
    
    $db = config::getConnexion();
    try {
        $query = $db->prepare($sql);
        $result = $query->execute([
            'nom' => $user->getNom(),
            'prenom' => $user->getPrenom(),
            'genre' => $user->getGenre(),
            'date_naissance' => $user->getDate_naissance(),
            'email' => $user->getEmail(),
            'numero_tel' => $user->getNumero_tel(),
            'mot_de_passe' => password_hash($user->getMot_de_passe(), PASSWORD_DEFAULT),
            'role' => $user->getRole(),
            'type_handicap' => $user->getType_handicap()
        ]);

        
        // retourne l'id instead of user pour la creation de profil avec le signup
        if ($result) {
            return $db->lastInsertId(); 
        } else {
            return false; 
        }
        
    } catch (Exception $e) {
         die('Error: ' . $e->getMessage());
    }
}

 
    // modifier  utilisateur
    
    public function updateUser(Utilisateur $user, $id) {
        $sql = "UPDATE utilisateur SET 
                    nom = :nom,
                    prenom = :prenom,
                    genre = :genre,
                    date_naissance = :date_naissance,
                    email = :email,
                    numero_tel = :numero_tel,
                    mot_de_passe = :mot_de_passe,
                    role = :role,
                    type_handicap = :type_handicap
                WHERE Id_utilisateur = :id";

        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'id' => $id,
                'nom' => $user->getNom(),
                'prenom' => $user->getPrenom(),
                'genre' => $user->getGenre(),
                'date_naissance' => $user->getDate_naissance(),
                'email' => $user->getEmail(),
                'numero_tel' => $user->getNumero_tel(),
                'mot_de_passe' => $user->getMot_de_passe(),
                'role' => $user->getRole(),
                'type_handicap' => $user->getType_handicap()
            ]);
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

   
    // afficher un utilisateur 
    
    public function showUser($id) {
        $sql = "SELECT * FROM utilisateur WHERE Id_utilisateur = :id";
        $db = config::getConnexion();
        $query = $db->prepare($sql);

        try {
            $query->execute(['id' => $id]);
            $user = $query->fetch(PDO::FETCH_ASSOC);
            return $user;
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

     


    //pour verifier login
    public function verifyLogin($email, $password) {
    $sql = "SELECT * FROM utilisateur WHERE email = :email";
    $db = config::getConnexion();
    
    try {
        $query = $db->prepare($sql);
        $query->execute(['email' => $email]);
        $user = $query->fetch();
        
        if ($user && password_verify($password, $user['mot_de_passe'])) {
            return $user;
        }
        return false;
        
    } catch (Exception $e) {
        throw new Exception("Erreur de connexion: " . $e->getMessage());
    }
}



    // vérifier que email existe
    public function verifyEmail($email) {
    $sql = "SELECT * FROM utilisateur WHERE email = :email";
    $db = config::getConnexion();
    try {
        $query = $db->prepare($sql);
        $query->execute(['email' => $email]);
        $user = $query->fetch();
        
        if ($user) {
            return $user;
        }
        return false;
        
    } catch (Exception $e) {
        throw new Exception("Erreur de connexion: " . $e->getMessage());
    }



    
}


// bloquer un utilisateur
public function blockUser($id) {
    $sql = "UPDATE utilisateur SET blocked = 1 WHERE Id_utilisateur = :id";
    $db = config::getConnexion();

    try {
        $query = $db->prepare($sql);
        $query->execute(['id' => $id]);
    } catch (Exception $e) {
        die('Error: ' . $e->getMessage());
    }
}

// débloquer un utilisateur
public function unblockUser($id) {
    $sql = "UPDATE utilisateur SET blocked = 0 WHERE Id_utilisateur = :id";
    $db = config::getConnexion();

    try {
        $query = $db->prepare($sql);
        $query->execute(['id' => $id]);
    } catch (Exception $e) {
        die('Error: ' . $e->getMessage());
    }
}



}





?>
