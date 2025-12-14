<?php
// DonController.php - Version avec recommandations
include_once(__DIR__ . '/../config.php');
require_once(__DIR__ . '/../model/Don.php');

class DonController {
    
    public function faireDon($id_campagne, $montant, $message, $methode_paiment, $email, $nom, $telephone = '', $whatsapp_code = '') {
        $db = config::getConnexion();
        try {
            error_log("=== DÉBUT DON WhatsApp ===");
            
            if (!empty($telephone)) {
                if (session_status() == PHP_SESSION_NONE) {
                    session_start();
                }
                
                if (empty($whatsapp_code)) {
                    $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
                    
                    require_once __DIR__ . '/WhatsAppController.php';
                    $whatsapp = new WhatsAppController();
                    $result = $whatsapp->sendVerificationCode($telephone, $code);
                    
                    if ($result['success']) {
                        $_SESSION['don_whatsapp_code'] = $code;
                        $_SESSION['don_whatsapp_phone'] = $telephone;
                        $_SESSION['don_whatsapp_time'] = time();
                        
                        if (isset($result['message_sid'])) {
                            $_SESSION['whatsapp_message_sid'] = $result['message_sid'];
                        }
                        
                        error_log("✅ WhatsApp envoyé à $telephone, code: $code");
                        return "whatsapp_code_required";
                        
                    } else {
                        error_log("❌ Échec WhatsApp: " . ($result['error'] ?? ''));
                        return false;
                    }
                    
                } else {
                    $code_attendu = $_SESSION['don_whatsapp_code'] ?? '';
                    $temps_code = $_SESSION['don_whatsapp_time'] ?? 0;
                    
                    if ($whatsapp_code !== $code_attendu) {
                        error_log("❌ Code incorrect: $whatsapp_code (attendu: $code_attendu)");
                        return false;
                    }
                    
                    if ((time() - $temps_code) > 600) {
                        error_log("❌ Code expiré");
                        return false;
                    }
                    
                    error_log("✅ Code WhatsApp vérifié");
                    
                    unset($_SESSION['don_whatsapp_code']);
                    unset($_SESSION['don_whatsapp_phone']);
                    unset($_SESSION['don_whatsapp_time']);
                    unset($_SESSION['whatsapp_message_sid']);
                }
            }
            
            if (!$db) {
                error_log("❌ Erreur: Connexion DB échouée");
                return false;
            }

            $numero_reçu = 'DON' . date('YmdHis') . rand(100, 999);
            
            $id_utilisateur = $this->getUtilisateurExistant($db);
            if (!$id_utilisateur) {
                error_log("❌ Aucun utilisateur trouvé dans la base");
                return false;
            }
            
            error_log("Données: Campagne=$id_campagne, User=$id_utilisateur, Montant=$montant, Méthode=$methode_paiment");

            $checkCampagne = $db->prepare("SELECT montant_actuel, titre FROM campagnecollecte WHERE Id_campagne = ?");
            $checkCampagne->execute([$id_campagne]);
            $campagneData = $checkCampagne->fetch(PDO::FETCH_ASSOC);
            
            if (!$campagneData) {
                error_log("❌ Campagne $id_campagne n'existe pas");
                return false;
            }

            $db->beginTransaction();

            $sql = "INSERT INTO don (
                id_campagne, 
                id_utilisateur,
                montant, 
                message, 
                methode_paiment, 
                email_donateur, 
                nom_donateur, 
                telephone,
                numero_reçu, 
                statut
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'confirmé')";
            
            $stmt = $db->prepare($sql);
            $params = [
                $id_campagne, 
                $id_utilisateur,
                $montant, 
                $message, 
                $methode_paiment,
                $email, 
                $nom, 
                $telephone,
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
            
            $nouveau_montant = $campagneData['montant_actuel'] + $montant;
            
            $sql_update = "UPDATE campagnecollecte SET montant_actuel = ? WHERE Id_campagne = ?";
            $update_stmt = $db->prepare($sql_update);
            $update_success = $update_stmt->execute([$nouveau_montant, $id_campagne]);
            
            if (!$update_success) {
                $db->rollBack();
                error_log("❌ Erreur mise à jour campagne");
                return false;
            }
            
            $db->commit();
            
            error_log("✅ Campagne mise à jour: {$campagneData['montant_actuel']} + $montant = $nouveau_montant TND");
            error_log("✅ Transaction complétée avec succès");
            
            $this->envoyerEmailConfirmation($email, $nom, $montant, $don_id, $campagneData['titre']);
            
            return $don_id;
            
        } catch (Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            error_log("❌ Exception: " . $e->getMessage());
            return false;
        }
    }
    
    // NOUVELLE MÉTHODE POUR LES RECOMMANDATIONS
    public function faireDonAvecRecommandations($id_campagne, $montant, $message, $methode_paiment, $email, $nom, $telephone = '', $whatsapp_code = '') {
        try {
            $don_id = $this->faireDon($id_campagne, $montant, $message, $methode_paiment, $email, $nom, $telephone, $whatsapp_code);
            
            if ($don_id) {
                $this->mettreAJourRecommandations($email, $nom, $id_campagne);
                
                if (session_status() == PHP_SESSION_NONE) {
                    session_start();
                }
                $_SESSION['donateur_email'] = $email;
                $_SESSION['donateur_nom'] = $nom;
                $_SESSION['dernier_don_campagne'] = $id_campagne;
                $_SESSION['dernier_don_montant'] = $montant;
                
                error_log("✅ Recommandations mises à jour pour: $email");
            }
            
            return $don_id;
            
        } catch (Exception $e) {
            error_log("❌ Erreur faireDonAvecRecommandations: " . $e->getMessage());
            return $this->faireDon($id_campagne, $montant, $message, $methode_paiment, $email, $nom, $telephone, $whatsapp_code);
        }
    }
    
    private function mettreAJourRecommandations($email, $nom, $campagneId) {
        try {
            include_once __DIR__ . '/FrontRecommandationController.php';
            $recommandationC = new FrontRecommandationController();
            
            return $recommandationC->enregistrerPreferences($email, $nom, $campagneId);
            
        } catch (Exception $e) {
            error_log("❌ Erreur mettreAJourRecommandations: " . $e->getMessage());
            return false;
        }
    }
    
    private function getUtilisateurExistant($db) {
        try {
            $stmt = $db->query("SELECT Id_utilisateur FROM utilisateur WHERE role = 'admin' OR role = 'utilisateur' LIMIT 1");
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                return $user['Id_utilisateur'];
            }
            
            error_log("⚠️ Aucun utilisateur trouvé, création d'un test...");
            
            $testEmail = "test@impactable.com";
            $testNom = "Test Utilisateur";
            
            $insertStmt = $db->prepare("INSERT INTO utilisateur (nom, email, role) VALUES (?, ?, 'utilisateur')");
            if ($insertStmt->execute([$testNom, $testEmail])) {
                return $db->lastInsertId();
            }
            
            return null;
            
        } catch (Exception $e) {
            error_log("❌ Erreur getUtilisateurExistant: " . $e->getMessage());
            return 1;
        }
    }
    
    private function envoyerEmailConfirmation($email, $nom, $montant, $don_id, $campagne_titre) {
        try {
            include_once __DIR__ . '/EmailController.php';
            $emailController = new EmailController();
            
            $resultat = $emailController->envoyerRecuDon(
                $email, $nom, $montant, $don_id, $campagne_titre
            );
            
            if ($resultat) {
                error_log("✅ Email envoyé à: $email");
            } else {
                error_log("⚠️ Échec envoi email à: $email");
            }
            
            return $resultat;
        } catch (Exception $e) {
            error_log("❌ Erreur envoi email: " . $e->getMessage());
            return false;
        }
    }
    
    public function getHistoriqueDonsComplet() {
        try {
            $db = config::getConnexion();
            
            $query = "SELECT 
                        d.*,
                        c.titre as campagne_titre,
                        c.categorie_impact,
                        d.nom_donateur as donateur_nom,
                        d.email_donateur as donateur_email
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
    
    public function getDonsParCampagne($id_campagne) {
        try {
            $db = config::getConnexion();
            
            $query = "SELECT 
                        d.*,
                        c.titre as campagne_titre
                     FROM don d
                     JOIN campagnecollecte c ON d.id_campagne = c.Id_campagne
                     WHERE d.id_campagne = ?
                     ORDER BY d.date_don DESC";
            
            $stmt = $db->prepare($query);
            $stmt->execute([$id_campagne]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Erreur getDonsParCampagne: " . $e->getMessage());
            return [];
        }
    }
    
    public function getDonById($id) {
        try {
            $db = config::getConnexion();
            
            $query = "SELECT 
                        d.*,
                        c.titre as campagne_titre,
                        c.categorie_impact
                     FROM don d
                     JOIN campagnecollecte c ON d.id_campagne = c.Id_campagne
                     WHERE d.Id_don = ?";
            
            $stmt = $db->prepare($query);
            $stmt->execute([$id]);
            $donData = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($donData) {
                $don = new Don();
                $don->setIdDon($donData['Id_don']);
                $don->setIdCampagne($donData['id_campagne']);
                $don->setIdUtilisateur($donData['id_utilisateur']);
                $don->setMontant($donData['montant']);
                $don->setMessage($donData['message']);
                $don->setMethodePaiment($donData['methode_paiment']);
                $don->setEmailDonateur($donData['email_donateur']);
                $don->setNomDonateur($donData['nom_donateur']);
                $don->setNumeroReçu($donData['numero_reçu']);
                $don->setDateDon($donData['date_don']);
                $don->setStatut($donData['statut']);
                return $don;
            }
            return null;
            
        } catch (PDOException $e) {
            error_log("Erreur getDonById: " . $e->getMessage());
            return null;
        }
    }
    
    public function updateDon(Don $don) {
        try {
            $db = config::getConnexion();
            
            $query = "UPDATE don SET 
                      montant = :montant,
                      statut = :statut,
                      methode_paiment = :methode,
                      message = :message,
                      email_donateur = :email,
                      nom_donateur = :nom
                      WHERE Id_don = :id";
            
            $stmt = $db->prepare($query);
            
            $result = $stmt->execute([
                ':montant' => $don->getMontant(),
                ':statut' => $don->getStatut(),
                ':methode' => $don->getMethodePaiment(),
                ':message' => $don->getMessage(),
                ':email' => $don->getEmailDonateur(),
                ':nom' => $don->getNomDonateur(),
                ':id' => $don->getIdDon()
            ]);
            
            return $result;
            
        } catch (PDOException $e) {
            error_log("Erreur updateDon: " . $e->getMessage());
            return false;
        }
    }
    
    public function deleteDon($id) {
        try {
            $db = config::getConnexion();
            
            $query = "SELECT id_campagne, montant FROM don WHERE Id_don = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$id]);
            $donData = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$donData) {
                return false;
            }
            
            $deleteQuery = "DELETE FROM don WHERE Id_don = ?";
            $deleteStmt = $db->prepare($deleteQuery);
            $success = $deleteStmt->execute([$id]);
            
            if ($success) {
                $this->actualiserMontantCampagne($donData['id_campagne']);
                return true;
            }
            
            return false;
            
        } catch (PDOException $e) {
            error_log("Erreur deleteDon: " . $e->getMessage());
            return false;
        }
    }
    
    private function actualiserMontantCampagne($id_campagne) {
        try {
            $db = config::getConnexion();
            
            $query = "SELECT COALESCE(SUM(montant), 0) as total_dons 
                     FROM don 
                     WHERE id_campagne = ? AND statut = 'confirmé'";
            $stmt = $db->prepare($query);
            $stmt->execute([$id_campagne]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $total_dons = $result['total_dons'];
            
            $update_query = "UPDATE campagnecollecte SET montant_actuel = ? WHERE Id_campagne = ?";
            $update_stmt = $db->prepare($update_query);
            return $update_stmt->execute([$total_dons, $id_campagne]);
            
        } catch (PDOException $e) {
            error_log("Erreur actualiserMontantCampagne: " . $e->getMessage());
            return false;
        }
    }
    
    public function getStatistiquesDons() {
        try {
            $db = config::getConnexion();
            
            $stats = [];
            
            $query1 = "SELECT COUNT(*) as total_dons, SUM(montant) as montant_total FROM don WHERE statut = 'confirmé'";
            $stmt1 = $db->query($query1);
            $stats['general'] = $stmt1->fetch(PDO::FETCH_ASSOC);
            
            $query2 = "SELECT methode_paiment, COUNT(*) as nombre, SUM(montant) as montant 
                      FROM don WHERE statut = 'confirmé' 
                      GROUP BY methode_paiment";
            $stmt2 = $db->query($query2);
            $stats['par_methode'] = $stmt2->fetchAll(PDO::FETCH_ASSOC);
            
            $query3 = "SELECT c.Id_campagne, c.titre, COUNT(d.Id_don) as nombre_dons, 
                      SUM(d.montant) as montant_total
                      FROM campagnecollecte c
                      LEFT JOIN don d ON c.Id_campagne = d.id_campagne AND d.statut = 'confirmé'
                      GROUP BY c.Id_campagne, c.titre";
            $stmt3 = $db->query($query3);
            $stats['par_campagne'] = $stmt3->fetchAll(PDO::FETCH_ASSOC);
            
            return $stats;
            
        } catch (PDOException $e) {
            error_log("Erreur getStatistiquesDons: " . $e->getMessage());
            return [];
        }
    }
}
?>