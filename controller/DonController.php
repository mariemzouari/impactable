<?php
// projet/controller/DonController.php
include_once(__DIR__ . '/../config.php');
require_once(__DIR__ . '/../model/Don.php');

class DonController {
    
    // Faire un don avec téléphone
    public function faireDon($id_campagne, $montant, $message, $methode_paiment, $email, $nom, $telephone, $numero_reçu = '') {
        $db = config::getConnexion();
        try {
            if (!$db) {
                throw new Exception("Connexion DB échouée");
            }

            // Validation du téléphone
            $telephone = $this->nettoyerTelephone($telephone);
            if (!$this->validerTelephone($telephone)) {
                throw new Exception("Numéro de téléphone invalide. Format attendu: 0X XXX XXX");
            }

            // Validation du montant
            if ($montant <= 0) {
                throw new Exception("Le montant doit être supérieur à 0");
            }

            // Génération numéro reçu
            if (empty($numero_reçu)) {
                $numero_reçu = 'DON' . date('YmdHis') . rand(100, 999);
            }
            
            // Récupérer un utilisateur existant
            $id_utilisateur = $this->getUtilisateurExistant($db);
            if (!$id_utilisateur) {
                throw new Exception("Aucun utilisateur administrateur trouvé");
            }

            // Vérifier si la campagne existe
            $campagneData = $this->getCampagneData($db, $id_campagne);
            if (!$campagneData) {
                throw new Exception("Campagne non trouvée");
            }

            // Générer le code de vérification
            $code_verification = $this->genererCodeVerification();

            // COMMENCER LA TRANSACTION
            $db->beginTransaction();

            // Insérer le don
            $don_id = $this->insererDon($db, [
                'id_campagne' => $id_campagne,
                'id_utilisateur' => $id_utilisateur,
                'montant' => $montant,
                'message' => $message,
                'methode_paiment' => $methode_paiment,
                'email_donateur' => $email,
                'nom_donateur' => $nom,
                'telephone' => $telephone,
                'code_verification' => $code_verification,
                'numero_reçu' => $numero_reçu
            ]);

            if (!$don_id) {
                throw new Exception("Erreur lors de l'insertion du don");
            }

            // VALIDER LA TRANSACTION
            $db->commit();

            // Envoyer le code de vérification
            $this->envoyerCodeVerification($telephone, $code_verification, $don_id);

            return [
                'success' => true,
                'don_id' => $don_id,
                'code' => $code_verification, // Pour le développement seulement
                'message' => 'Don créé avec succès. Un code de vérification a été envoyé à votre téléphone.'
            ];

        } catch (Exception $e) {
            if ($db && $db->inTransaction()) {
                $db->rollBack();
            }
            error_log("❌ Erreur faireDon: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    // Vérifier le code de confirmation
    public function verifierDon($don_id, $code_saisi, $ip_address = null, $user_agent = null) {
        $db = config::getConnexion();
        try {
            if (!$db) {
                throw new Exception("Connexion DB échouée");
            }

            // Récupérer le don
            $don = $this->getDonById($don_id);
            if (!$don) {
                throw new Exception("Don non trouvé");
            }

            // Vérifier le statut
            if ($don->getStatut() === 'confirmé') {
                throw new Exception("Ce don a déjà été confirmé");
            }

            if ($don->getStatut() === 'annulé') {
                throw new Exception("Ce don a été annulé");
            }

            // Vérifier le code
            if ($don->getCodeVerification() !== $code_saisi) {
                // Enregistrer la tentative échouée
                $this->enregistrerTentative($db, $don_id, $code_saisi, $ip_address, $user_agent, 'echec');
                
                // Vérifier le nombre de tentatives
                $tentatives = $this->compterTentatives($db, $don_id);
                if ($tentatives >= 3) {
                    $this->bloquerDon($db, $don_id);
                    throw new Exception("Trop de tentatives échouées. Le don a été bloqué.");
                }
                
                throw new Exception("Code incorrect. Il vous reste " . (3 - $tentatives) . " tentative(s).");
            }

            // Code correct - confirmer le don
            $db->beginTransaction();

            // Mettre à jour le statut
            $updateQuery = "UPDATE don SET 
                           statut = 'confirmé',
                           date_verification = NOW(),
                           date_confirmation = NOW()
                           WHERE Id_don = ?";
            $stmt = $db->prepare($updateQuery);
            $stmt->execute([$don_id]);

            // Mettre à jour le montant de la campagne
            $this->actualiserMontantCampagne($don->getIdCampagne());

            // Enregistrer la tentative réussie
            $this->enregistrerTentative($db, $don_id, $code_saisi, $ip_address, $user_agent, 'succes');

            // Envoyer l'email de confirmation
            $this->envoyerEmailConfirmationFinale($don);

            $db->commit();

            return [
                'success' => true,
                'message' => 'Don confirmé avec succès!',
                'don' => $don
            ];

        } catch (Exception $e) {
            if ($db && $db->inTransaction()) {
                $db->rollBack();
            }
            error_log("❌ Erreur verifierDon: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    // Renvoyer le code de vérification
    public function renvoyerCode($don_id) {
        $db = config::getConnexion();
        try {
            // Récupérer le don
            $query = "SELECT telephone, code_verification FROM don WHERE Id_don = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$don_id]);
            $don = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$don) {
                throw new Exception("Don non trouvé");
            }

            // Générer un nouveau code
            $nouveau_code = $this->genererCodeVerification();

            // Mettre à jour le code
            $updateQuery = "UPDATE don SET code_verification = ? WHERE Id_don = ?";
            $updateStmt = $db->prepare($updateQuery);
            $updateStmt->execute([$nouveau_code, $don_id]);

            // Renvoyer le code
            $this->envoyerCodeVerification($don['telephone'], $nouveau_code, $don_id);

            return [
                'success' => true,
                'message' => 'Nouveau code envoyé avec succès'
            ];

        } catch (Exception $e) {
            error_log("❌ Erreur renvoyerCode: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    // ============ MÉTHODES PRIVÉES ============

    private function validerTelephone($telephone) {
        // Nettoyer
        $telephone = preg_replace('/[^0-9]/', '', $telephone);
        
        // Vérifier la longueur
        if (strlen($telephone) < 8) {
            return false;
        }
        
        // Format Tunisien: 0X XXX XXX
        if (!preg_match('/^0[0-9]{8}$/', $telephone)) {
            return false;
        }
        
        return true;
    }

    private function nettoyerTelephone($telephone) {
        return preg_replace('/[^0-9]/', '', $telephone);
    }

    private function formaterTelephone($telephone) {
        $telephone = $this->nettoyerTelephone($telephone);
        if (strlen($telephone) === 8) {
            return '0' . $telephone;
        }
        return $telephone;
    }

    private function genererCodeVerification() {
        return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    private function getUtilisateurExistant($db) {
        $stmt = $db->query("SELECT Id_utilisateur FROM utilisateur LIMIT 1");
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ? $user['Id_utilisateur'] : null;
    }

    private function getCampagneData($db, $id_campagne) {
        $stmt = $db->prepare("SELECT titre, montant_actuel FROM campagnecollecte WHERE Id_campagne = ?");
        $stmt->execute([$id_campagne]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function insererDon($db, $data) {
        $sql = "INSERT INTO don (
            id_campagne, id_utilisateur, montant, message, methode_paiment,
            email_donateur, nom_donateur, telephone, code_verification, numero_reçu, statut
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'en_attente')";
        
        $stmt = $db->prepare($sql);
        $success = $stmt->execute([
            $data['id_campagne'],
            $data['id_utilisateur'],
            $data['montant'],
            $data['message'],
            $data['methode_paiment'],
            $data['email_donateur'],
            $data['nom_donateur'],
            $data['telephone'],
            $data['code_verification'],
            $data['numero_reçu']
        ]);
        
        return $success ? $db->lastInsertId() : false;
    }

    private function envoyerCodeVerification($telephone, $code, $don_id) {
        // Simulation d'envoi SMS
        $message = "Votre code de vérification pour le don #$don_id est: $code";
        
        // Pour le développement, on log seulement
        error_log("📱 SMS à $telephone: $message");
        
        // En production, intégrez un service SMS comme:
        // - Twilio
        // - Nexmo/Vonage
        // - Orange SMS API
        // - ClickSend
        
        return true;
    }

    private function envoyerEmailConfirmationFinale(Don $don) {
        include_once 'EmailController.php';
        $emailController = new EmailController();
        
        // Récupérer le titre de la campagne
        $db = config::getConnexion();
        $stmt = $db->prepare("SELECT titre FROM campagnecollecte WHERE Id_campagne = ?");
        $stmt->execute([$don->getIdCampagne()]);
        $campagne = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $emailController->envoyerRecuDon(
            $don->getEmailDonateur(),
            $don->getNomDonateur(),
            $don->getMontant(),
            $don->getIdDon(),
            $campagne['titre']
        );
    }

    private function enregistrerTentative($db, $don_id, $code, $ip, $user_agent, $resultat) {
        $sql = "INSERT INTO verification_tentatives (don_id, code_saisi, ip_address, user_agent, resultat) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        return $stmt->execute([$don_id, $code, $ip, $user_agent, $resultat]);
    }

    private function compterTentatives($db, $don_id) {
        $sql = "SELECT COUNT(*) as count FROM verification_tentatives 
                WHERE don_id = ? AND resultat = 'echec' 
                AND date_tentative > DATE_SUB(NOW(), INTERVAL 1 HOUR)";
        $stmt = $db->prepare($sql);
        $stmt->execute([$don_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }

    private function bloquerDon($db, $don_id) {
        $sql = "UPDATE don SET statut = 'bloque' WHERE Id_don = ?";
        $stmt = $db->prepare($sql);
        return $stmt->execute([$don_id]);
    }

    private function actualiserMontantCampagne($id_campagne) {
        try {
            $db = config::getConnexion();
            
            // Calculer la somme des dons confirmés
            $query = "SELECT COALESCE(SUM(montant), 0) as total_dons 
                     FROM don 
                     WHERE id_campagne = ? AND statut = 'confirmé'";
            $stmt = $db->prepare($query);
            $stmt->execute([$id_campagne]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $total_dons = $result['total_dons'];
            
            // Mettre à jour la campagne
            $updateQuery = "UPDATE campagnecollecte SET montant_actuel = ? WHERE Id_campagne = ?";
            $updateStmt = $db->prepare($updateQuery);
            return $updateStmt->execute([$total_dons, $id_campagne]);
            
        } catch (PDOException $e) {
            error_log("Erreur actualiserMontantCampagne: " . $e->getMessage());
            return false;
        }
    }

    // ============ MÉTHODES PUBLIQUES SUPPLEMENTAIRES ============

    public function getDonById($id) {
        try {
            $db = config::getConnexion();
            
            $query = "SELECT d.*, c.titre as campagne_titre 
                     FROM don d 
                     JOIN campagnecollecte c ON d.id_campagne = c.Id_campagne 
                     WHERE d.Id_don = ?";
            
            $stmt = $db->prepare($query);
            $stmt->execute([$id]);
            $donData = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($donData) {
                return new Don(
                    $donData['Id_don'],
                    $donData['id_campagne'],
                    $donData['id_utilisateur'],
                    $donData['montant'],
                    $donData['message'],
                    $donData['methode_paiment'],
                    $donData['email_donateur'],
                    $donData['nom_donateur'],
                    $donData['telephone'],
                    $donData['code_verification'],
                    $donData['numero_reçu'],
                    $donData['date_don'],
                    $donData['date_verification'],
                    $donData['date_confirmation'],
                    $donData['statut']
                );
            }
            return null;
            
        } catch (PDOException $e) {
            error_log("Erreur getDonById: " . $e->getMessage());
            return null;
        }
    }

    public function getHistoriqueDonsComplet() {
        try {
            $db = config::getConnexion();
            
            $query = "SELECT 
                        d.*,
                        c.titre as campagne_titre,
                        c.categorie_impact
                     FROM don d
                     JOIN campagnecollecte c ON d.id_campagne = c.Id_campagne
                     ORDER BY d.date_don DESC";
            
            $stmt = $db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Erreur getHistoriqueDonsComplet: " . $e->getMessage());
            return [];
        }
    }

    public function getDonsEnAttente() {
        try {
            $db = config::getConnexion();
            
            $query = "SELECT 
                        d.*,
                        c.titre as campagne_titre
                     FROM don d
                     JOIN campagnecollecte c ON d.id_campagne = c.Id_campagne
                     WHERE d.statut = 'en_attente'
                     ORDER BY d.date_don DESC";
            
            $stmt = $db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Erreur getDonsEnAttente: " . $e->getMessage());
            return [];
        }
    }

    public function getStatistiques() {
        try {
            $db = config::getConnexion();
            
            $stats = [];
            
            // Totaux
            $query1 = "SELECT 
                        COUNT(*) as total_dons,
                        SUM(CASE WHEN statut = 'confirmé' THEN montant ELSE 0 END) as montant_confirme,
                        SUM(CASE WHEN statut = 'en_attente' THEN montant ELSE 0 END) as montant_attente,
                        COUNT(CASE WHEN statut = 'en_attente' THEN 1 END) as dons_attente
                     FROM don";
            $stmt1 = $db->query($query1);
            $stats['totaux'] = $stmt1->fetch(PDO::FETCH_ASSOC);
            
            // Par campagne
            $query2 = "SELECT 
                        c.titre,
                        COUNT(d.Id_don) as nombre_dons,
                        SUM(CASE WHEN d.statut = 'confirmé' THEN d.montant ELSE 0 END) as montant_confirme
                     FROM campagnecollecte c
                     LEFT JOIN don d ON c.Id_campagne = d.id_campagne
                     GROUP BY c.Id_campagne, c.titre";
            $stmt2 = $db->query($query2);
            $stats['par_campagne'] = $stmt2->fetchAll(PDO::FETCH_ASSOC);
            
            return $stats;
            
        } catch (PDOException $e) {
            error_log("Erreur getStatistiques: " . $e->getMessage());
            return [];
        }
    }
}
?>