<?php
class Utils {
    public static function formatDate($dateString) {
        try {
            $date = new DateTime($dateString);
            $now = new DateTime();
            $interval = $date->diff($now);
            
            if ($interval->days == 0) {
                return "aujourd'hui";
            } elseif ($interval->days == 1) {
                return "il y a 1 jour";
            } else {
                return "il y a " . $interval->days . " jours";
            }
        } catch (Exception $e) {
            return "date inconnue";
        }
    }
    
    public static function isNew($dateString, $days = 3) {
        try {
            $date = new DateTime($dateString);
            $now = new DateTime();
            $interval = $date->diff($now);
            return $interval->days <= $days;
        } catch (Exception $e) {
            return false;
        }
    }
    
    public static function escape($string) {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
    
    public static function getExcerpt($text, $length = 150) {
        if (empty($text)) {
            return "Aucune description disponible";
        }
        if (strlen($text) <= $length) {
            return $text;
        }
        return substr($text, 0, $length) . '...';
    }
    
    public static function formatTypeOffre($type) {
        $types = [
            'emploi' => 'Emploi',
            'stage' => 'Stage',
            'volontariat' => 'Volontariat',
            'formation' => 'Formation',
            'autre' => 'Autre'
        ];
        return $types[$type] ?? $type;
    }
    
    public static function formatMode($mode) {
        $modes = [
            'presentiel' => 'Présentiel',
            'en_ligne' => 'En ligne',
            'hybride' => 'Hybride'
        ];
        return $modes[$mode] ?? $mode;
    }
    
    public static function redirect($url) {
        header("Location: $url");
        exit;
    }
    
    public static function isAuthenticated() {
        return isset($_SESSION['user_id']);
    }
    
    public static function sanitize($data) {
        if (is_array($data)) {
            return array_map([self::class, 'sanitize'], $data);
        }
        return trim(htmlspecialchars(strip_tags($data)));
    }
    
    public static function startSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    public static function deconnecter() {
        $_SESSION = array();
        session_destroy();
    }
    
    public static function formatStatusCandidature($status) {
        $statuses = [
            'en_attente' => 'En attente',
            'en_revue' => 'En revue',
            'entretien' => 'Entretien',
            'retenu' => 'Retenu',
            'refuse' => 'Refusé',
            'retire' => 'Retiré'
        ];
        return $statuses[$status] ?? $status;
    }
}

// Timezone
date_default_timezone_set('Africa/Tunis');