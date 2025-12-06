<?php
// Désactiver l'affichage des erreurs pour éviter de casser le JSON
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Gestionnaire d'erreur personnalisé
set_error_handler(function($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});

// Gestionnaire d'exception non capturée
set_exception_handler(function($exception) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Erreur fatale: ' . $exception->getMessage(),
        'id' => null
    ]);
    exit;
});

require_once(__DIR__ . '/../../CONFIGRRATION/config.php');
require_once(__DIR__ . '/../../SERVICE/PrioriteIntelligente.php');

header('Content-Type: application/json');

$response = ['success' => false, 'message' => '', 'id' => null];

/**
 * Fonction pour ajouter les colonnes manquantes automatiquement
 */
function ensureColumnsExist($db) {
    $colonnesRequises = [
        'image' => 'VARCHAR(255) DEFAULT NULL',
        'nom' => 'VARCHAR(100) DEFAULT NULL',
        'prenom' => 'VARCHAR(100) DEFAULT NULL',
        'email' => 'VARCHAR(255) DEFAULT NULL',
        'telephone' => 'VARCHAR(20) DEFAULT NULL',
        'lieu' => 'VARCHAR(255) DEFAULT NULL',
        'dateIncident' => 'DATE DEFAULT NULL',
        'typeHandicap' => 'VARCHAR(100) DEFAULT NULL',
        'personnesImpliquees' => 'TEXT DEFAULT NULL',
        'temoins' => 'TEXT DEFAULT NULL',
        'actionsPrecedentes' => 'TEXT DEFAULT NULL',
        'solutionSouhaitee' => 'TEXT DEFAULT NULL'
    ];
    
    // Récupérer les colonnes existantes
    $existingColumns = [];
    try {
        $result = $db->query("DESCRIBE reclamation");
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $existingColumns[] = $row['Field'];
        }
    } catch (Exception $e) {
        return false;
    }
    
    // Ajouter les colonnes manquantes
    foreach ($colonnesRequises as $colonne => $type) {
        if (!in_array($colonne, $existingColumns)) {
            try {
                $db->exec("ALTER TABLE reclamation ADD COLUMN `$colonne` $type");
            } catch (Exception $e) {
                // Ignorer l'erreur si la colonne existe déjà
            }
        }
    }
    
    return true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Connexion à la base de données
        $db = config::getConnexion();
        
        // S'assurer que toutes les colonnes existent
        ensureColumnsExist($db);
        
        // Récupérer les données du formulaire
        $sujet = isset($_POST['sujet']) ? trim($_POST['sujet']) : '';
        $description = isset($_POST['description']) ? trim($_POST['description']) : '';
        $categorie = isset($_POST['categorie']) ? trim($_POST['categorie']) : '';
        $priorite = isset($_POST['priorite']) ? trim($_POST['priorite']) : '';
        $utilisateurId = isset($_POST['utilisateurId']) ? intval($_POST['utilisateurId']) : 1;
        
        // Informations personnelles
        $nom = isset($_POST['nom']) ? trim($_POST['nom']) : '';
        $prenom = isset($_POST['prenom']) ? trim($_POST['prenom']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $telephone = isset($_POST['telephone']) ? trim($_POST['telephone']) : '';
        
        // Détails de l'incident
        $lieu = isset($_POST['lieu']) ? trim($_POST['lieu']) : '';
        $dateIncident = isset($_POST['dateIncident']) ? trim($_POST['dateIncident']) : null;
        $typeHandicap = isset($_POST['typeHandicap']) ? trim($_POST['typeHandicap']) : null;
        
        // Personnes impliquées
        $personnesImpliquees = isset($_POST['personnesImpliquees']) ? trim($_POST['personnesImpliquees']) : null;
        $temoins = isset($_POST['temoins']) ? trim($_POST['temoins']) : null;
        
        // Actions et solutions
        $actionsPrecedentes = isset($_POST['actionsPrecedentes']) ? trim($_POST['actionsPrecedentes']) : null;
        $solutionSouhaitee = isset($_POST['solutionSouhaitee']) ? trim($_POST['solutionSouhaitee']) : '';
        
        // Valider les champs requis
        if (empty($sujet) || empty($description) || empty($categorie) || empty($priorite) || 
            empty($nom) || empty($prenom) || empty($email) || empty($telephone) || 
            empty($lieu) || empty($dateIncident) || empty($solutionSouhaitee)) {
            $response['message'] = 'Veuillez remplir tous les champs obligatoires.';
            echo json_encode($response);
            exit;
        }
        
        // ====== PRIORISATION INTELLIGENTE ======
        $texteAnalyse = $sujet . ' ' . $description . ' ' . $solutionSouhaitee;
        $analyseIA = PrioriteIntelligente::analyser($texteAnalyse, $categorie);
        
        // Si l'utilisateur a choisi "Moyenne" par défaut, vérifier si l'IA suggère autre chose
        $prioriteOriginale = ucfirst(strtolower($priorite));
        if ($prioriteOriginale === 'Moyenne' && $analyseIA['priorite'] === 'Urgente') {
            $priorite = 'Urgente';
        } else {
            $priorite = $prioriteOriginale;
        }
        
        // Normaliser la priorité finale
        if (!in_array($priorite, ['Faible', 'Moyenne', 'Urgente'])) {
            $priorite = 'Moyenne';
        }
        
        // Normaliser la catégorie
        $categorie = ucfirst(strtolower($categorie));
        
        // Gérer l'upload d'image
        $imagePath = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../uploads/reclamations/';
            
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $fileExtension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            
            if (in_array($fileExtension, $allowedExtensions)) {
                if ($_FILES['image']['size'] <= 5 * 1024 * 1024) {
                    $fileName = uniqid('reclamation_', true) . '.' . $fileExtension;
                    $targetPath = $uploadDir . $fileName;
                    
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                        $imagePath = 'uploads/reclamations/' . $fileName;
                    }
                }
            }
        }
        
        // Préparer la requête d'insertion
        $sql = "INSERT INTO reclamation (
                    sujet, description, categorie, priorite, statut, 
                    dateCreation, derniereModification, utilisateurId, agentAttribue,
                    image, nom, prenom, email, telephone, lieu, 
                    dateIncident, typeHandicap, personnesImpliquees, temoins, 
                    actionsPrecedentes, solutionSouhaitee
                ) VALUES (
                    :sujet, :description, :categorie, :priorite, :statut,
                    :dateCreation, :derniereModification, :utilisateurId, :agentAttribue,
                    :image, :nom, :prenom, :email, :telephone, :lieu,
                    :dateIncident, :typeHandicap, :personnesImpliquees, :temoins,
                    :actionsPrecedentes, :solutionSouhaitee
                )";
        
        $now = date('Y-m-d H:i:s');
        
        $query = $db->prepare($sql);
        $query->execute([
            'sujet' => $sujet,
            'description' => $description,
            'categorie' => $categorie,
            'priorite' => $priorite,
            'statut' => 'En attente',
            'dateCreation' => $now,
            'derniereModification' => $now,
            'utilisateurId' => $utilisateurId,
            'agentAttribue' => null,
            'image' => $imagePath,
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'telephone' => $telephone,
            'lieu' => $lieu,
            'dateIncident' => $dateIncident ?: null,
            'typeHandicap' => $typeHandicap ?: null,
            'personnesImpliquees' => $personnesImpliquees ?: null,
            'temoins' => $temoins ?: null,
            'actionsPrecedentes' => $actionsPrecedentes ?: null,
            'solutionSouhaitee' => $solutionSouhaitee
        ]);
        
        $lastId = $db->lastInsertId();
        
        $response['success'] = true;
        $response['message'] = 'Réclamation envoyée avec succès !';
        $response['id'] = $lastId;
        $response['analyse_ia'] = [
            'priorite_suggeree' => $analyseIA['priorite'],
            'priorite_finale' => $priorite,
            'confiance' => $analyseIA['confiance'],
            'score' => $analyseIA['score']
        ];
        
    } catch (PDOException $e) {
        error_log('Erreur PDO réclamation: ' . $e->getMessage());
        $response['message'] = 'Erreur de base de données: ' . $e->getMessage();
    } catch (Exception $e) {
        error_log('Erreur réclamation: ' . $e->getMessage());
        $response['message'] = 'Erreur: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'Méthode non autorisée.';
}

echo json_encode($response);
?>
