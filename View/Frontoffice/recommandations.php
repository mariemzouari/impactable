<?php
// FrontRecommandationController.php
include_once __DIR__ . '/../../config.php';


class FrontRecommandationController
{
    private $db;

    public function __construct()
    {
        $this->db = config::getConnexion();
    }

    public function getOrCreateUserId($email, $nom)
    {
        if (!$email || empty(trim($email))) {
            return null;
        }

        try {
            $sql = "SELECT Id_utilisateur FROM utilisateur WHERE email = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([trim($email)]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                return $user['Id_utilisateur'];
            }

            $sql = "INSERT INTO utilisateur (nom, email, role, date_inscription) VALUES (?, ?, 'donateur', NOW())";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([trim($nom), trim($email)]);

            return $this->db->lastInsertId();

        } catch (PDOException $e) {
            error_log("Erreur getOrCreateUserId: " . $e->getMessage());
            return null;
        }
    }

    public function genererRecommandationsPourDonateur($email, $nom, $limit = 3)
    {
        try {
            $userId = $this->getOrCreateUserId($email, $nom);

            if (!$userId) {
                return $this->getCampagnesPopulaires($limit);
            }

            $sql = "SELECT c.categorie_impact, c.urgence, c.zone_geographique FROM don d JOIN campagnecollecte c ON d.id_campagne = c.Id_campagne WHERE d.id_utilisateur = ? AND d.statut = 'confirmé' AND d.email_donateur = ? GROUP BY c.categorie_impact, c.urgence, c.zone_geographique";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId, trim($email)]);
            $historique = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($historique)) {
                return $this->getCampagnesPopulaires($limit);
            }

            $preferences = $this->analyserPreferences($historique);
            $recommandations = $this->chercherCampagnesSimilaires($preferences, $email, $limit);

            if (count($recommandations) < $limit) {
                $populaires = $this->getCampagnesPopulaires($limit - count($recommandations));
                $recommandations = array_merge($recommandations, $populaires);
            }

            return array_slice($recommandations, 0, $limit);

        } catch (PDOException $e) {
            error_log("Erreur genererRecommandationsPourDonateur: " . $e->getMessage());
            return $this->getCampagnesPopulaires($limit);
        }
    }

    private function analyserPreferences($historique)
    {
        $preferences = ['categories' => [], 'urgences' => [], 'zones' => []];

        foreach ($historique as $item) {
            if (!empty($item['categorie_impact']))
                $preferences['categories'][] = $item['categorie_impact'];
            if (!empty($item['urgence']))
                $preferences['urgences'][] = $item['urgence'];
            if (!empty($item['zone_geographique']))
                $preferences['zones'][] = $item['zone_geographique'];
        }

        $preferences['categories'] = array_count_values($preferences['categories']);
        $preferences['urgences'] = array_count_values($preferences['urgences']);
        $preferences['zones'] = array_count_values($preferences['zones']);

        arsort($preferences['categories']);
        arsort($preferences['urgences']);
        arsort($preferences['zones']);

        return $preferences;
    }

    private function chercherCampagnesSimilaires($preferences, $email, $limit)
    {
        $whereClauses = [];
        $params = [];

        if (!empty($preferences['categories'])) {
            $topCategory = array_key_first($preferences['categories']);
            $whereClauses[] = "c.categorie_impact = ?";
            $params[] = $topCategory;
        }

        if (!empty($preferences['urgences'])) {
            $topUrgence = array_key_first($preferences['urgences']);
            $whereClauses[] = "c.urgence = ?";
            $params[] = $topUrgence;
        }

        $sql = "SELECT DISTINCT c.*, (CASE WHEN c.categorie_impact = ? THEN 3 WHEN c.urgence = ? THEN 2 WHEN c.zone_geographique = ? THEN 1 ELSE 0 END) as score_similarite FROM campagnecollecte c WHERE c.statut = 'active' AND c.Id_campagne NOT IN (SELECT d.id_campagne FROM don d WHERE d.email_donateur = ? AND d.statut = 'confirmé')";

        if (!empty($whereClauses)) {
            $sql .= " AND (" . implode(" OR ", $whereClauses) . ")";
        }

        $sql .= " ORDER BY score_similarite DESC, c.date_debut DESC LIMIT ?";

        $topZone = !empty($preferences['zones']) ? array_key_first($preferences['zones']) : '';
        $params = array_merge([$topCategory ?? '', $topUrgence ?? '', $topZone], $params, [trim($email), $limit]);

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getCampagnesPopulaires($limit)
    {
        $sql = "SELECT c.*, COUNT(d.id_don) as nombre_dons FROM campagnecollecte c LEFT JOIN don d ON c.Id_campagne = d.id_campagne AND d.statut = 'confirmé' WHERE c.statut = 'active' GROUP BY c.Id_campagne ORDER BY nombre_dons DESC, c.date_debut DESC LIMIT ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function enregistrerPreferences($email, $nom, $campagneId)
    {
        try {
            $userId = $this->getOrCreateUserId($email, $nom);

            if (!$userId)
                return false;

            $sql = "SELECT categorie_impact, urgence, zone_geographique FROM campagnecollecte WHERE Id_campagne = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$campagneId]);
            $campagne = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$campagne)
                return false;

            $insertSql = "INSERT INTO user_preferences (user_id, categorie, urgence, zone, score, last_updated) VALUES (?, ?, ?, ?, 1, NOW()) ON DUPLICATE KEY UPDATE score = score + 1, last_updated = NOW()";

            $insertStmt = $this->db->prepare($insertSql);
            $insertStmt->execute([$userId, $campagne['categorie_impact'], $campagne['urgence'], $campagne['zone_geographique']]);

            return true;

        } catch (PDOException $e) {
            error_log("Erreur enregistrerPreferences: " . $e->getMessage());
            return false;
        }
    }

    public function getRecommandationsPourVisiteur($limit = 3)
    {
        return $this->getCampagnesPopulaires($limit);
    }
}

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ImpactAble — Faire un Don Sécurisé</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .recommendations-section {
            margin-top: 40px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 30px;
            border-radius: 15px;
            border-left: 5px solid #667eea;
        }

        .recommendations-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-top: 20px;
        }

        .recommendation-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border: 2px solid transparent;
            position: relative;
        }

        .recommendation-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            border-color: #667eea;
        }

        .recommendation-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: bold;
            z-index: 2;
        }

        .progress-container {
            margin: 15px 0;
        }

        .progress-bar-custom {
            height: 8px;
            background: #e0e0e0;
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #4CAF50, #45a049);
            border-radius: 4px;
            transition: width 0.5s ease;
        }

        .personalized-tag {
            background: #e3f2fd;
            color: #1976d2;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .recommendation-card {
            animation: slideIn 0.5s ease forwards;
        }
    </style>
</head>