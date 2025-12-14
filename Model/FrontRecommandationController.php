<?php
// controller/FrontRecommandationController.php
include_once __DIR__ . '/../config.php';

class FrontRecommandationController
{
    private $db;

    public function __construct()
    {
        $this->db = config::getConnexion();
    }

    public function genererRecommandationsPourDonateur($email, $nom, $limit = 3)
    {
        try {
            // Vérifier si l'email existe dans la base
            $sql = "SELECT Id_utilisateur FROM utilisateur WHERE email = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                return $this->getCampagnesPopulaires($limit);
            }

            // Récupérer l'historique des dons
            $sql = "SELECT c.categorie_impact, c.urgence 
                    FROM don d 
                    JOIN campagnecollecte c ON d.id_campagne = c.Id_campagne 
                    WHERE d.email_donateur = ? 
                    AND d.statut = 'confirmé'
                    GROUP BY c.categorie_impact, c.urgence";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$email]);
            $historique = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($historique)) {
                return $this->getCampagnesPopulaires($limit);
            }

            // Analyser les préférences
            $preferences = $this->analyserPreferences($historique);

            // Chercher des campagnes similaires
            $recommandations = $this->chercherCampagnesSimilaires($preferences, $email, $limit);

            if (count($recommandations) < $limit) {
                $populaires = $this->getCampagnesPopulaires($limit - count($recommandations));
                $recommandations = array_merge($recommandations, $populaires);
            }

            return array_slice($recommandations, 0, $limit);

        } catch (PDOException $e) {
            error_log("Erreur dans genererRecommandationsPourDonateur: " . $e->getMessage());
            return $this->getCampagnesPopulaires($limit);
        }
    }

    private function analyserPreferences($historique)
    {
        $preferences = ['categories' => [], 'urgences' => []];

        foreach ($historique as $item) {
            if (!empty($item['categorie_impact'])) {
                $preferences['categories'][] = $item['categorie_impact'];
            }
            if (!empty($item['urgence'])) {
                $preferences['urgences'][] = $item['urgence'];
            }
        }

        $preferences['categories'] = array_count_values($preferences['categories']);
        $preferences['urgences'] = array_count_values($preferences['urgences']);

        arsort($preferences['categories']);
        arsort($preferences['urgences']);

        return $preferences;
    }

    private function chercherCampagnesSimilaires($preferences, $email, $limit)
    {
        $whereClauses = [];
        $params = [];

        if (!empty($preferences['categories'])) {
            $topCategories = array_keys(array_slice($preferences['categories'], 0, 2));
            $placeholders = implode(',', array_fill(0, count($topCategories), '?'));
            $whereClauses[] = "c.categorie_impact IN ($placeholders)";
            $params = array_fill(0, count($topCategories), '?');
        }

        if (!empty($preferences['urgences'])) {
            $topUrgences = array_keys(array_slice($preferences['urgences'], 0, 2));
            $placeholders = implode(',', array_fill(0, count($topUrgences), '?'));
            $whereClauses[] = "c.urgence IN ($placeholders)";
            $params = array_merge($params, array_fill(0, count($topUrgences), '?'));
        }

        $sql = "SELECT DISTINCT c.* 
                FROM campagnecollecte c 
                WHERE c.statut = 'active' 
                AND c.Id_campagne NOT IN (
                    SELECT d.id_campagne 
                    FROM don d 
                    WHERE d.email_donateur = ? 
                    AND d.statut = 'confirmé'
                )";

        if (!empty($whereClauses)) {
            $sql .= " AND (" . implode(" OR ", $whereClauses) . ")";
        }

        $sql .= " ORDER BY RAND() LIMIT ?";

        $params = array_merge([$email], $params, [$limit]);

        try {
            $stmt = $this->db->prepare($sql);

            // Bind parameters avec types corrects
            $paramTypes = array_fill(0, count($params) - 1, PDO::PARAM_STR);
            $paramTypes[] = PDO::PARAM_INT; // Pour LIMIT

            foreach ($params as $i => $param) {
                $stmt->bindValue($i + 1, $param, $paramTypes[$i]);
            }

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur dans chercherCampagnesSimilaires: " . $e->getMessage());
            return [];
        }
    }

    public function getCampagnesPopulaires($limit = 3)
    {
        try {
            // CORRECTION ICI: LIMIT ? sans quotes
            $sql = "SELECT c.*, COUNT(d.id_don) as nombre_dons 
                    FROM campagnecollecte c 
                    LEFT JOIN don d ON c.Id_campagne = d.id_campagne 
                    WHERE c.statut = 'active' 
                    GROUP BY c.Id_campagne 
                    ORDER BY nombre_dons DESC, c.date_debut DESC 
                    LIMIT ?";

            $stmt = $this->db->prepare($sql);

            // Bien binder le paramètre comme INT
            $stmt->bindValue(1, (int) $limit, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Erreur dans getCampagnesPopulaires: " . $e->getMessage());
            return [];
        }
    }

    public function getRecommandationsPourVisiteur($limit = 3)
    {
        return $this->getCampagnesPopulaires($limit);
    }
    public function enregistrerPreferences($email, $nom, $campagneId)
    {
        // Cette méthode était manquante dans la référence.
        // Implémentation basique pour éviter l'erreur fatale.
        // On pourrait ajouter ici une logique pour sauvegarder les préférences en base de données explicitement
        // si ce n'est pas déjà géré par l'historique des dons.
        return true;
    }
}
?>