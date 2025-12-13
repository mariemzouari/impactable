<?php 
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Model/OtpClass.php';


class OtpController {

    // récupérer  otp
    public function showOTP($user_id) {
        $sql = "SELECT * FROM onetimepassword WHERE Id_utilisateur = :user_id ORDER BY creation_time DESC LIMIT 1";
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        try {
            $query->execute(['user_id' => $user_id]);
            $otp = $query->fetch(PDO::FETCH_ASSOC);
            return $otp;
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    // ajouter un nouveau otp
    public function addOTP(OTP $otp) {
        $sql = "INSERT INTO onetimepassword 
                (Id_utilisateur, code, expires_at, used, creation_time)
                VALUES 
                (:Id_utilisateur, :code, :expires_at, :used, :creation_time)";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'Id_utilisateur' => $otp->getId_utilisateur(),
                'code' => $otp->getCode(),
                'expires_at' => $otp->getExpires_at(),
                'used' => $otp->getUsed(),
                'creation_time' => $otp->getCreation_time()
            ]);
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    // modifier un otp comme used
    public function updateOTP($otp_id) {
        $sql = "UPDATE onetimepassword SET used = 1 WHERE Id_password = :otp_id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['otp_id' => $otp_id]);
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }


}





?>