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

require_once(__DIR__ . '/../../controller/ReclamationController.php');
require_once(__DIR__ . '/../../MODEL/Reclamation.php');
require_once(__DIR__ . '/../../CONFIGRRATION/config.php');

header('Content-Type: application/json');

$response = ['success' => false, 'message' => '', 'id' => null];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
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
        
        // Normaliser la priorité (majuscule première lettre)
        $priorite = ucfirst(strtolower($priorite));
        if (!in_array($priorite, ['Faible', 'Moyenne', 'Urgente'])) {
            $priorite = 'Moyenne';
        }
        
        // Normaliser la catégorie (première lettre en majuscule)
        $categorie = ucfirst(strtolower($categorie));
        
        // Gérer l'upload d'image (doit être fait avant la création de l'objet)
        $imagePath = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../uploads/reclamations/';
            
            // Créer le dossier s'il n'existe pas
            if (!file_exists($uploadDir)) {
                if (!mkdir($uploadDir, 0777, true)) {
                    $response['message'] = 'Impossible de créer le dossier d\'upload.';
                    echo json_encode($response);
                    exit;
                }
                // Créer le fichier .htaccess pour la protection
                file_put_contents($uploadDir . '.htaccess', 'Options -Indexes');
                file_put_contents($uploadDir . 'index.php', '<?php header("HTTP/1.0 403 Forbidden"); exit; ?>');
            }
            
            $fileExtension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            
            // Vérifier l'extension
            if (in_array($fileExtension, $allowedExtensions)) {
                // Vérifier la taille (max 5MB)
                if ($_FILES['image']['size'] <= 5 * 1024 * 1024) {
                    // Générer un nom unique
                    $fileName = uniqid('reclamation_', true) . '.' . $fileExtension;
                    $targetPath = $uploadDir . $fileName;
                    
                    // Déplacer le fichier
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                        // Chemin relatif depuis la racine du projet (accessible via URL)
                        $imagePath = 'uploads/reclamations/' . $fileName;
                    } else {
                        $response['message'] = 'Erreur lors de l\'upload de l\'image.';
                        echo json_encode($response);
                        exit;
                    }
                } else {
                    $response['message'] = 'L\'image est trop volumineuse (max 5MB).';
                    echo json_encode($response);
                    exit;
                }
            } else {
                $response['message'] = 'Format d\'image non supporté. Utilisez JPG, PNG ou GIF.';
                echo json_encode($response);
                exit;
            }
        }
        
        // Convertir la date de l'incident en DateTime
        $dateIncidentObj = null;
        if ($dateIncident) {
            try {
                $dateIncidentObj = new DateTime($dateIncident);
            } catch (Exception $e) {
                $dateIncidentObj = null;
            }
        }
        
        // Créer l'objet Reclamation avec tous les attributs
        $reclamation = new Reclamation(
            null,                    // id
            $sujet,                  // sujet
            $description,            // description
            $categorie,             // categorie
            $priorite,              // priorite
            'En attente',           // statut (par défaut)
            new DateTime(),         // dateCreation
            new DateTime(),         // derniereModification
            $utilisateurId,         // utilisateurId
            null,                   // agentAttribue (optionnel)
            $imagePath,             // image
            $nom,                   // nom
            $prenom,                // prenom
            $email,                 // email
            $telephone,             // telephone
            $lieu,                  // lieu
            $dateIncidentObj,       // dateIncident
            $typeHandicap,          // typeHandicap
            $personnesImpliquees,   // personnesImpliquees
            $temoins,               // temoins
            $actionsPrecedentes,    // actionsPrecedentes
            $solutionSouhaitee      // solutionSouhaitee
        );
        
        // Ajouter la réclamation
        try {
            $controller = new ReclamationController();
            $controller->addReclamation($reclamation);
            
            // Récupérer l'ID de la réclamation créée
            $db = config::getConnexion();
            $lastId = $db->lastInsertId();
            
            $response['success'] = true;
            $response['message'] = 'Réclamation envoyée avec succès !';
            $response['id'] = $lastId;
        } catch (PDOException $e) {
            // Erreur SQL spécifique
            $errorCode = $e->getCode();
            $errorMessage = $e->getMessage();
            
            if (strpos($errorMessage, 'Unknown column') !== false) {
                $response['message'] = 'Erreur: La base de données n\'est pas à jour. Veuillez exécuter le script SQL: add_fields_simple.sql dans phpMyAdmin. Détails: ' . $errorMessage;
            } else {
                $response['message'] = 'Erreur de base de données: ' . $errorMessage;
            }
            throw $e; // Re-lancer pour être capturé par le catch principal
        }
        
    } catch (Exception $e) {
        // Log l'erreur pour le débogage
        error_log('Erreur réclamation: ' . $e->getMessage());
        error_log('Stack trace: ' . $e->getTraceAsString());
        
        // Message d'erreur plus détaillé
        $errorMessage = $e->getMessage();
        
        // Vérifier si c'est une erreur SQL (champs manquants)
        if (strpos($errorMessage, 'Unknown column') !== false) {
            $response['message'] = 'Erreur: Les champs de la base de données ne sont pas à jour. Veuillez exécuter le script SQL: add_fields_simple.sql dans phpMyAdmin.';
        } else {
            $response['message'] = 'Erreur lors de l\'envoi de la réclamation: ' . $errorMessage;
        }
    } catch (PDOException $e) {
        error_log('Erreur PDO réclamation: ' . $e->getMessage());
        $errorMessage = $e->getMessage();
        
        if (strpos($errorMessage, 'Unknown column') !== false) {
            $response['message'] = 'Erreur: Les champs de la base de données ne sont pas à jour. Veuillez exécuter le script SQL: add_fields_simple.sql dans phpMyAdmin.';
        } else {
            $response['message'] = 'Erreur de base de données: ' . $errorMessage;
        }
    }
} else {
    $response['message'] = 'Méthode non autorisée.';
}

echo json_encode($response);
?>

