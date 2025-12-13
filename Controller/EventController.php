<?php
require_once "../../Config.php";
require_once "../../Model/EventModel.php";

class EventController {
    private $eventModel;

    public function __construct() {
        $Config = new Config();
        $db = $Config->getPDO();
        $this->eventModel = new EventModel($db);
    }

    // Liste tous les événements
    public function evenment_back() {
        $searchQuery = $_GET['search_query'] ?? '';

        if (!empty($searchQuery)) {
            $events = $this->eventModel->searchByTitle($searchQuery);
        } else {
            $events = $this->eventModel->getAll();
        }

                foreach ($events as &$event) {

                    $event['date_event'] = $event['date_debut'];

                }

                $stats = [

                    'total_events' => $this->eventModel->countAll(),

                    'total_participations' => $this->eventModel->countParticipations(),

                    'upcoming_events' => $this->eventModel->getUpcomingEvents(),

                    'participations_by_event' => $this->eventModel->getParticipationsByEvent(),

                    'events_no_participants' => $this->eventModel->countEventsWithNoParticipations(), // New stat

                    'avg_participations_per_event' => $this->eventModel->getAverageParticipationsPerEvent(), // New stat

                    'events_by_category' => $this->eventModel->getEventsCountByCategory(), // Data for new chart
                    
                    'events_finished' => $this->eventModel->countFinishedEvents()

                ];

                include "../../View/Backoffice/header.php"; // <-- inclus la vue
    }

    // Création d'un événement
    public function create() {
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $data = array_map('trim', $_POST);
            $errors = [];
            if(empty($data['titre'])) $errors[] = 'Le titre est requis.';
            if(empty($data['date_debut'])) $errors[] = 'La date de début est requise.';
            if(empty($data['date_fin'])) $errors[] = 'La date de fin est requise.';
            if(!empty($data['date_debut']) && !empty($data['date_fin']) && strtotime($data['date_debut']) > strtotime($data['date_fin'])) {
                $errors[] = 'La date de début ne peut pas être postérieure à la date de fin.';
            }
            if(empty($data['categorie'])) $errors[] = 'La catégorie est requise.';
            if(empty($data['description'])) $errors[] = 'La description est requise.';
            if(empty($data['location'])) $errors[] = 'Le lieu est requis.';
            if(empty($data['capacite_max']) || !is_numeric($data['capacite_max']) || $data['capacite_max'] <= 0) {
                $errors[] = 'La capacité maximale doit être un nombre entier positif.';
            }




            if(empty($errors)){
                $this->eventModel->create($data);
                header("Location: evenment_back.php");
                exit;
            } else {
                $action = 'create';
                $event = $data; // prefill form with submitted values
                include "../../View/Backoffice/form_events.php";
                return;
            }
        }
        $action = 'create';
        include "../../View/Backoffice/form_events.php";
    }

    // Modification
    public function edit($id) {
        $event = $this->eventModel->getById($id);
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $data = array_map('trim', $_POST);
            $errors = [];
            if(empty($data['titre'])) $errors[] = 'Le titre est requis.';
            if(empty($data['date_debut'])) $errors[] = 'La date de début est requise.';
            if(empty($data['date_fin'])) $errors[] = 'La date de fin est requise.';
            if(!empty($data['date_debut']) && !empty($data['date_fin']) && strtotime($data['date_debut']) > strtotime($data['date_fin'])) {
                $errors[] = 'La date de début ne peut pas être postérieure à la date de fin.';
            }
            if(empty($data['categorie'])) $errors[] = 'La catégorie est requise.';
            if(empty($data['description'])) $errors[] = 'La description est requise.';
            if(empty($data['location'])) $errors[] = 'Le lieu est requis.';
            if(empty($data['capacite_max']) || !is_numeric($data['capacite_max']) || $data['capacite_max'] <= 0) {
                $errors[] = 'La capacité maximale doit être un nombre entier positif.';
            }




            if(empty($errors)){
                $this->eventModel->update($id, $data);
                header("Location: evenment_back.php");
                exit;
            } else {
                $action = 'edit';
                $event = $data; // prefill form with submitted values
                include "../../View/Backoffice/form_events.php";
                return;
            }
        }
        $action = 'edit';
        include "../../View/Backoffice/form_events.php";
    }

    // Suppression
    public function delete($id) {
        $ok = $this->eventModel->delete($id);
        if($ok){
            header("Location: evenment_back.php");
            exit;
        } else {
            // Couldn't delete (likely dependent records); show error in list view
            $events = $this->eventModel->getAll();
            $error = "Impossible de supprimer l'événement : il existe des participations liées.";
            include "../../View/Backoffice/header.php";
            return;
        }
    }


}


