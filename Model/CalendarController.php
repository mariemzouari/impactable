<?php
class CalendarController {
    
    private $db;
    
    public function __construct() {
        // Connection à la base de données
        try {
            $this->db = new PDO('mysql:host=localhost;dbname=impactable', 'root', '');
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            error_log("CalendarController DB Error: " . $e->getMessage());
        }
    }
    
    // Méthode pour FullCalendar
    public function getCalendarEvents() {
        $campaigns = $this->getCampaignsWithDeadlines();
        
        $events = [];
        foreach ($campaigns as $campaign) {
            $events[] = [
                'title' => $campaign['titre'],
                'start' => $campaign['date_fin'],
                'end' => $campaign['date_fin'],
                'color' => $this->getEventColor($campaign['statut']),
                'extendedProps' => [
                    'campagne_id' => $campaign['Id_campagne'],
                    'statut' => $campaign['statut']
                ]
            ];
        }
        
        // Ajouter les événements fixes
        $events[] = [
            'title' => 'Rapport Mensuel',
            'start' => date('Y-m-01'),
            'color' => '#6f42c1'
        ];
        
        return $events;
    }
    
    // Méthode alternative
    public function getEvents() {
        return $this->getCalendarEvents();
    }
    
    // Récupérer les campagnes avec deadlines
    public function getCampaignsWithDeadlines() {
        if (!$this->db) {
            return [];
        }
        
        try {
            $sql = "SELECT Id_campagne, titre, date_fin, statut 
                    FROM campagnecollecte 
                    WHERE date_fin >= CURDATE() 
                    AND statut IN ('active', 'pending')
                    ORDER BY date_fin ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("CalendarController Error: " . $e->getMessage());
            return [];
        }
    }
    
    // Couleurs selon statut
    private function getEventColor($statut) {
        switch($statut) {
            case 'active': return '#28a745';
            case 'pending': return '#ffc107';
            case 'completed': return '#17a2b8';
            default: return '#3788d8';
        }
    }
    
    // Récupérer les échéances urgentes (moins de 7 jours)
    public function getUrgentDeadlines() {
        if (!$this->db) {
            return [];
        }
        
        try {
            $sql = "SELECT Id_campagne, titre, date_fin, statut 
                    FROM campagnecollecte 
                    WHERE date_fin BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
                    AND statut = 'active'
                    ORDER BY date_fin ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("CalendarController Error: " . $e->getMessage());
            return [];
        }
    }
}
?>