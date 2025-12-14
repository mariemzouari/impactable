<?php
// StatsController.php - Version corrigée
include_once(__DIR__ . '/../config.php');

class StatsController {
    private $db;
    
    public function __construct() {
        $this->db = config::getConnexion();
    }
    
    // Statistiques générales
    public function getGlobalStats() {
        $stats = [];
        
        // Total des dons
        $sql = "SELECT COUNT(*) as total_dons, SUM(montant) as montant_total 
                FROM don WHERE statut = 'confirmé'";
        $stmt = $this->db->query($sql);
        $stats['dons'] = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Campagnes
        $sql = "SELECT COUNT(*) as total_campagnes, 
                SUM(CASE WHEN statut = 'active' THEN 1 ELSE 0 END) as actives,
                SUM(CASE WHEN statut = 'terminée' THEN 1 ELSE 0 END) as terminees
                FROM campagnecollecte";
        $stmt = $this->db->query($sql);
        $stats['campagnes'] = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Dons par heure
        $sql = "SELECT HOUR(date_don) as heure, COUNT(*) as nombre_dons 
                FROM don 
                WHERE DATE(date_don) = CURDATE() 
                GROUP BY HOUR(date_don) 
                ORDER BY heure";
        $stmt = $this->db->query($sql);
        $stats['dons_par_heure'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $stats;
    }
    
    // Statistiques par campagne
    public function getCampaignStats($campagneId = null) {
        if ($campagneId) {
            // Stats pour une campagne spécifique
            $sql = "SELECT c.*, 
                    COUNT(d.Id_don) as nombre_dons,
                    AVG(d.montant) as moyenne_don,
                    MAX(d.montant) as don_max,
                    MIN(d.montant) as don_min
                    FROM campagnecollecte c
                    LEFT JOIN don d ON c.Id_campagne = d.id_campagne 
                    WHERE c.Id_campagne = ? 
                    GROUP BY c.Id_campagne";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$campagneId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            // Toutes les campagnes classées
            $sql = "SELECT c.Id_campagne, c.titre, c.categorie_impact,
                    c.objectif_montant, c.montant_actuel,
                    COUNT(d.Id_don) as nombre_dons,
                    ROUND((c.montant_actuel / c.objectif_montant) * 100, 1) as progression
                    FROM campagnecollecte c
                    LEFT JOIN don d ON c.Id_campagne = d.id_campagne
                    GROUP BY c.Id_campagne, c.titre
                    ORDER BY progression DESC, nombre_dons DESC";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
    
    // Dons par méthode de paiement
    public function getPaymentMethodStats() {
        $sql = "SELECT methode_paiment, 
                COUNT(*) as nombre_dons, 
                SUM(montant) as montant_total,
                ROUND(AVG(montant), 2) as moyenne
                FROM don 
                WHERE statut = 'confirmé'
                GROUP BY methode_paiment 
                ORDER BY montant_total DESC";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Évolution des dons sur 30 jours
    public function getDonationEvolution($days = 30) {
        $sql = "SELECT DATE(date_don) as date, 
                COUNT(*) as nombre_dons, 
                SUM(montant) as montant_total
                FROM don 
                WHERE date_don >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
                AND statut = 'confirmé'
                GROUP BY DATE(date_don) 
                ORDER BY date";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$days]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Top donateurs
    public function getTopDonors($limit = 10) {
        $sql = "SELECT nom_donateur, 
                COUNT(*) as nombre_dons, 
                SUM(montant) as montant_total,
                MAX(date_don) as dernier_don
                FROM don 
                WHERE statut = 'confirmé'
                GROUP BY nom_donateur, email_donateur
                ORDER BY montant_total DESC 
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>