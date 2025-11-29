<?php
include_once(__DIR__ . '/../config.php');

class DonController {
    public function faireDon($id_campagne, $montant, $message, $methode_paiment, $email, $nom, $numero_reçu = '') {
        $db = config::getConnexion();
        try {
            error_log("=== DÉBUT faireDon ===");
            
            if (!$db) {
                error_log("❌ Erreur: Connexion DB échouée");
                return false;
            }

            // Génération numéro reçu
            if (empty($numero_reçu)) {
                $numero_reçu = 'DON' . date('YmdHis') . rand(100, 999);
            }
            
            // RÉCUPÉRER UN UTILISATEUR EXISTANT
            $id_utilisateur = $this->getUtilisateurExistant($db);
            if (!$id_utilisateur) {
                error_log("❌ Aucun utilisateur trouvé dans la base");
                return false;
            }
            
            error_log("Données: Campagne=$id_campagne, User=$id_utilisateur, Montant=$montant, Méthode=$methode_paiment");

            // Vérifier si la campagne existe et récupérer son montant actuel
            $checkCampagne = $db->prepare("SELECT montant_actuel FROM campagnecollecte WHERE Id_campagne = ?");
            $checkCampagne->execute([$id_campagne]);
            $campagneData = $checkCampagne->fetch(PDO::FETCH_ASSOC);
            
            if (!$campagneData) {
                error_log("❌ Campagne $id_campagne n'existe pas");
                return false;
            }

            // COMMENCER LA TRANSACTION
            $db->beginTransaction();

            // 1. INSÉRER LE DON
            $sql = "INSERT INTO don (
                id_campagne, 
                id_utilisateur,
                montant, 
                message, 
                methode_paiment, 
                email_donateur, 
                nom_donateur, 
                numero_reçu, 
                statut
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'confirmé')";
            
            $stmt = $db->prepare($sql);
            $params = [
                $id_campagne, 
                $id_utilisateur,
                $montant, 
                $message, 
                $methode_paiment,
                $email, 
                $nom, 
                $numero_reçu
            ];
            
            error_log("Params: " . print_r($params, true));
            
            $success = $stmt->execute($params);
            
            if (!$success) {
                $db->rollBack();
                $errorInfo = $stmt->errorInfo();
                error_log("❌ Erreur insertion don: " . implode(", ", $errorInfo));
                return false;
            }
            
            $don_id = $db->lastInsertId();
            error_log("✅ Don inséré avec ID: $don_id");
            
            // 2. METTRE À JOUR LE MONTANT DE LA CAMPAGNE
            $nouveau_montant = $campagneData['montant_actuel'] + $montant;
            
            $sql_update = "UPDATE campagnecollecte SET montant_actuel = ? WHERE Id_campagne = ?";
            $update_stmt = $db->prepare($sql_update);
            $update_success = $update_stmt->execute([$nouveau_montant, $id_campagne]);
            
            if (!$update_success) {
                $db->rollBack();
                error_log("❌ Erreur mise à jour campagne");
                return false;
            }
            
            // VALIDER LA TRANSACTION
            $db->commit();
            
            error_log("✅ Campagne mise à jour: {$campagneData['montant_actuel']} + $montant = $nouveau_montant TND");
            error_log("✅ Transaction complétée avec succès");
            
            return $don_id;
            
        } catch (Exception $e) {
            // ANNULER EN CAS D'ERREUR
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            error_log("❌ Exception: " . $e->getMessage());
            return false;
        }
    }
    
    private function getUtilisateurExistant($db) {
        // Récupérer le premier utilisateur existant
        $stmt = $db->query("SELECT Id_utilisateur FROM utilisateur LIMIT 1");
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ? $user['Id_utilisateur'] : null;
    }
}
?>