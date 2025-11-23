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

// Routeur simple
$action = $_GET['action'] ?? 'offres';

try {
    switch ($action) {
        case 'connexion':
            $controller = new AuthController();
            $controller->connexion();
            break;
            
        case 'deconnexion':
            $controller = new AuthController();
            $controller->deconnexion();
            break;
            
        case 'offres':
            $controller = new OffreController();
            $controller->liste();
            break;
            
        case 'details-offre':
            $controller = new OffreController();
            $controller->details();
            break;
            
        case 'poster-offre':
            $controller = new OffreController();
            $controller->poster();
            break;
            
        case 'mes-offres':
            $controller = new OffreController();
            $controller->mesOffres();
            break;

        case 'modifier-offre':
            $controller = new OffreController();
            $controller->modifier();
            break;

        case 'supprimer-offre':
            $controller = new OffreController();
            $controller->supprimer();
            break;

        case 'gestion-offre':
            $controller = new OffreController();
            $controller->gestion();
            break;
            
        case 'postuler':
            $controller = new CandidatureController();
            $controller->postuler();
            break;

        case 'mes-candidatures':
            $controller = new CandidatureController();
            $controller->mesCandidatures();
            break;


case 'admin-dashboard':
    require_once 'controllers/AdminController.php';
    $controller = new AdminController();
    $controller->dashboard();
    break;

case 'admin-gestion-offres':
    require_once 'controllers/AdminController.php';
    $controller = new AdminController();
    $controller->gestionOffres();
    break;

case 'admin-voir-offre':
    require_once 'controllers/AdminController.php';
    $controller = new AdminController();
    $controller->voirOffre();
    break;

case 'admin-modifier-offre':
    require_once 'controllers/AdminController.php';
    $controller = new AdminController();
    $controller->modifierOffre();
    break;

case 'admin-supprimer-offre':
    require_once 'controllers/AdminController.php';
    $controller = new AdminController();
    $controller->supprimerOffre();
    break;

case 'admin-gestion-candidatures':
    require_once 'controllers/AdminController.php';
    $controller = new AdminController();
    $controller->gestionCandidatures();
    break;

case 'admin-gestion-utilisateurs':
    require_once 'controllers/AdminController.php';
    $controller = new AdminController();
    $controller->gestionUtilisateurs();
    break;

case 'admin-voir-candidature':
    $adminController = new AdminController();
    $adminController->voirCandidature();
    break;
            
            
        default:
            $controller = new OffreController();
            $controller->liste();
            break;
    }
} catch (Exception $e) {
    echo "Une erreur est survenue. Veuillez réessayer.";
    error_log("Erreur dans le routeur: " . $e->getMessage());
}