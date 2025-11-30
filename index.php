<?php

require_once 'config/config.php';
require_once 'models/Database.php';
require_once 'models/Utils.php';
require_once 'models/Utilisateur.php';
require_once 'models/Offre.php';
require_once 'models/Candidature.php';
require_once 'controllers/AuthController.php';
require_once 'controllers/OffreController.php';
require_once 'controllers/CandidatureController.php';
require_once 'controllers/AdminController.php';

// Démarrer la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Instanciation des contrôleurs
$authController = new AuthController();
$offreController = new OffreController();
$candidatureController = new CandidatureController();
$adminController = new AdminController();

// Routeur simple
$action = $_GET['action'] ?? 'offres';

try {
    switch ($action) {
        case 'connexion':
            $authController->connexion();
            break;
            
        case 'deconnexion':
            $authController->deconnexion();
            break;
            
        case 'offres':
            $offreController->liste();
            break;
            
        case 'details-offre':
            $offreController->details();
            break;
            
        case 'poster-offre':
            $offreController->poster();
            break;
            
        case 'mes-offres':
            $offreController->mesOffres();
            break;

        case 'modifier-offre':
            $offreController->modifier();
            break;

        case 'supprimer-offre':
            $offreController->supprimer();
            break;

        case 'gestion-offre':
            $offreController->gestion();
            break;
            
        case 'postuler':
            $candidatureController->postuler();
            break;

        case 'mes-candidatures':
            $candidatureController->mesCandidatures();
            break;

        // Routes Admin
        case 'admin-dashboard':
            $adminController->dashboard();
            break;

        case 'admin-gestion-offres':
            $adminController->gestionOffres();
            break;

        case 'admin-voir-offre':
            $adminController->voirOffre();
            break;

        case 'admin-modifier-offre':
            $adminController->modifierOffre();
            break;

        case 'admin-supprimer-offre':
            $adminController->supprimerOffre();
            break;

        case 'admin-gestion-candidatures':
            $adminController->gestionCandidatures();
            break;

        case 'admin-gestion-utilisateurs':
            $adminController->gestionUtilisateurs();
            break;

        case 'admin-voir-candidature':
            $adminController->voirCandidature();
            break;
            
        case 'admin-modifier-candidature':
            $adminController->modifierCandidature();
            break;
            
        case 'admin-modifier-candidature-traitement':
            $adminController->modifierCandidatureTraitement();
            break;
            
        case 'admin-supprimer-candidature':
            $adminController->supprimerCandidature();
            break; 

case 'admin-candidatures-offre':
    $adminController->candidaturesOffre();
    break;        
            
        default:
            $offreController->liste();
            break;
    }
    
} catch (Exception $e) {
    echo "Une erreur est survenue. Veuillez réessayer.";
    error_log("Erreur dans le routeur: " . $e->getMessage());
}