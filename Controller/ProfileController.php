<?php 
require_once __DIR__ . '/../config.php';
include(__DIR__ . '/../Model/ProfileClass.php');


class ProfileController {



    // show a single profile by USER id
    public function showProfile($user_id) {
        $sql = "SELECT * FROM profil WHERE Id_utilisateur = :user_id";
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        try {
            $query->execute(['user_id' => $user_id]);
            $profile = $query->fetch(PDO::FETCH_ASSOC);
            return $profile;
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    // add a new profile
    public function addProfile(Profil $profil) {
        $sql = "INSERT INTO profil 
                (Id_utilisateur, photo_profil, bio, ville, pays, profession, competences, linkedin, date_creation, date_modification)
                VALUES 
                (:Id_utilisateur, :photo_profil, :bio, :ville, :pays, :profession, :competences, :linkedin, :date_creation, :date_modification)";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'Id_utilisateur' => $profil->getId_utilisateur(),
                'photo_profil' => $profil->getPhoto_profil(),
                'bio' => $profil->getBio(),
                'ville' => $profil->getVille(),
                'pays' => $profil->getPays(),
                'profession' => $profil->getProfession(),
                'competences' => $profil->getCompetences(),
                'linkedin' => $profil->getLinkedin(),
                'date_creation' => $profil->getDate_creation(),
                'date_modification' => $profil->getDate_modification()
            ]);
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    // update with USER id
    public function updateProfile(Profil $profil, $user_id) {
        $sql = "UPDATE profil SET
                photo_profil = :photo_profil,
                bio = :bio,
                ville = :ville,
                pays = :pays,
                profession = :profession,
                competences = :competences,
                linkedin = :linkedin,
                date_modification = :date_modification
            WHERE Id_utilisateur = :user_id";
    
    $db = config::getConnexion();
    try {
        $query = $db->prepare($sql);
        $query->execute([
            'user_id' => $user_id,
            'photo_profil' => $profil->getPhoto_profil(),
            'bio' => $profil->getBio(),
            'ville' => $profil->getVille(),
            'pays' => $profil->getPays(),
            'profession' => $profil->getProfession(),
            'competences' => $profil->getCompetences(),
            'linkedin' => $profil->getLinkedin(),
            'date_modification' => date("Y-m-d H:i:s")
        ]);
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    
}
?>
