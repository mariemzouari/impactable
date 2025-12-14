<?php
include_once __DIR__ . '/../../controller/DonController.php';
include_once __DIR__ . '/../../controller/FrontCampagneController.php';

$donController = new DonController();
$campagneController = new FrontCampagneController();

if (!isset($_GET['id'])) {
    header('Location: list-don.php');
    exit;
}

$don_id = $_GET['id'];

try {
    // Récupérer d'abord l'ID de la campagne avant suppression
    $db = config::getConnexion();
    $query = "SELECT id_campagne FROM don WHERE Id_don = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$don_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        $id_campagne = $result['id_campagne'];
        
        // Supprimer le don
        $deleteQuery = "DELETE FROM don WHERE Id_don = ?";
        $deleteStmt = $db->prepare($deleteQuery);
        $success = $deleteStmt->execute([$don_id]);
        
        if ($success) {
            // Actualiser le montant de la campagne
            $campagneController->actualiserMontantCampagne($id_campagne);
            
            header('Location: list-don.php?success=delete');
            exit;
        } else {
            header('Location: list-don.php?error=delete_failed');
            exit;
        }
    } else {
        header('Location: list-don.php?error=not_found');
        exit;
    }
} catch (PDOException $e) {
    error_log("Erreur suppression don: " . $e->getMessage());
    header('Location: list-don.php?error=database_error');
    exit;
}
?>