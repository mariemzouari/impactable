<?php
require_once __DIR__ . '/../../Config.php';
require_once __DIR__ . '/../../Model/EventModel.php';

$eventId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$event = null;
$error_message = null;

if ($eventId <= 0) {
    $error_message = "ID d'événement non valide fourni.";
} else {
    try {
        $db = Config::getPDO();
        $eventModel = new EventModel($db);
        $event = $eventModel->getById($eventId);

        if (!$event) {
            $error_message = "Événement avec l'ID $eventId non trouvé.";
        }
    } catch (Exception $e) {
        $error_message = "Erreur de base de données : " . $e->getMessage();
    }
}

// Initialize participants as an empty array to prevent errors on initial load.
// The data will be loaded asynchronously via JavaScript.
$participants = [];

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Participants - <?= $event ? htmlspecialchars($event['titre']) : 'Erreur' ?> - Backoffice</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        :root {
            --brown: #4b2e16;
            --copper: #b47b47;
            --moss: #5e6d3b;
            --sage: #a9b97d;
            --sand: #f4ecdd;
            --white: #fffaf5;
            --light-sage: #e1e8c9;
            --muted: #6b4b44;
            --radius: 16px;
            --shadow: 0 8px 22px rgba(75,46,22,0.08);
        }

        body {
            background: linear-gradient(180deg, var(--sand), #fbf7ef);
            color: var(--brown);
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }

        .backoffice-wrapper {
            max-width: 1400px;
            margin: 0 auto;
            padding: 24px;
        }

        .header-section {
            background: var(--white);
            border-radius: var(--radius);
            padding: 32px;
            margin-bottom: 32px;
            box-shadow: var(--shadow);
            border-left: 4px solid var(--moss);
        }

        .header-section h1 {
            font-size: 2rem;
            margin: 0 0 8px 0;
            color: var(--brown);
        }

        .header-section p {
            color: var(--muted);
            margin: 0;
            font-size: 1rem;
        }

        .header-actions {
            display: flex;
            gap: 12px;
            margin-top: 20px;
            justify-content: space-between;
            align-items: center;
        }

        .search-box {
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--light-sage);
            border-radius: 8px;
            padding: 10px 16px;
            width: 300px;
        }

        .search-box input {
            border: none;
            background: transparent;
            color: var(--brown);
            font-size: 1rem;
            width: 100%;
        }

        .search-box input::placeholder {
            color: rgba(75,46,22,0.4);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 24px 0;
        }

        .stat-card {
            background: var(--white);
            border-radius: var(--radius);
            padding: 20px;
            border-left: 4px solid var(--moss);
            box-shadow: var(--shadow);
        }

        .stat-card-label {
            font-size: 0.9rem;
            color: var(--muted);
            font-weight: 600;
            margin-bottom: 10px;
        }

        .stat-card-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--moss);
        }

        .participants-card {
            background: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .card-header {
            padding: 24px;
            border-bottom: 1px solid rgba(75,46,22,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-header h2 {
            margin: 0;
            font-size: 1.3rem;
            color: var(--brown);
        }

        .card-body {
            padding: 24px;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead tr {
            background: var(--light-sage);
            border-bottom: 2px solid rgba(75,46,22,0.1);
        }

        th {
            padding: 12px 16px;
            text-align: left;
            font-weight: 600;
            color: var(--brown);
            font-size: 0.9rem;
        }

        td {
            padding: 12px 16px;
            border-bottom: 1px solid rgba(75,46,22,0.05);
            color: var(--brown);
        }

        tbody tr:hover {
            background: rgba(94,109,59,0.02);
        }

        .user-badge {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--moss), var(--sage));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.85rem;
        }

        .user-name {
            font-weight: 600;
            color: var(--brown);
        }

        .user-email {
            font-size: 0.85rem;
            color: var(--muted);
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .status-confirmé {
            background: rgba(39, 174, 96, 0.15);
            color: #27ae60;
        }

        .status-inscrit {
            background: rgba(243, 156, 18, 0.15);
            color: #f39c12;
        }

        .status-annulé {
            background: rgba(231, 76, 60, 0.15);
            color: #e74c3c;
        }

        .actions-cell {
            display: flex;
            gap: 6px;
        }

        .btn-action {
            padding: 6px 12px;
            border: none;
            border-radius: 6px;
            background: var(--light-sage);
            color: var(--moss);
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn-action:hover {
            background: var(--moss);
            color: white;
        }

        .btn-action.delete:hover {
            background: #e74c3c;
            color: white;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.95rem;
        }

        .btn-primary {
            background: var(--moss);
            color: white;
        }

        .btn-primary:hover {
            background: #4a5830;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(94,109,59,0.3);
        }

        .btn-secondary {
            background: var(--copper);
            color: white;
        }

        .btn-secondary:hover {
            background: #9d6a3b;
            transform: translateY(-2px);
        }

        .btn-ghost {
            background: transparent;
            color: var(--moss);
            border: 1px solid var(--moss);
        }

        .btn-ghost:hover {
            background: var(--moss);
            color: white;
        }

        .footer-actions {
            display: flex;
            gap: 12px;
            justify-content: space-between;
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid rgba(75,46,22,0.1);
        }

        /* Modal Styles */
        .modal {
            display: flex; /* Changed from 'none' to 'flex' for initial state, will be hidden by opacity/visibility */
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            align-items: center;
            justify-content: center;
            z-index: 1000;
            padding: 20px;
            opacity: 0; /* Hidden by default */
            visibility: hidden; /* Hidden by default */
            transition: opacity 0.3s ease, visibility 0.3s ease; /* Smooth transition */
        }

        .modal.active {
            opacity: 1;
            visibility: visible;
        }

        .modal-content {
            background: var(--white);
            border-radius: var(--radius);
            padding: 32px;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-height: 90vh;
            overflow-y: auto;
            transform: translateY(-20px); /* Initial state for slide effect */
            transition: transform 0.3s ease, opacity 0.3s ease; /* Add transition for content */
            opacity: 0;
        }

        .modal.active .modal-content {
            transform: translateY(0); /* Final state for slide effect */
            opacity: 1;
        }

        .modal.active .modal-content {
            transform: translateY(0); /* Final state for slide effect */
            opacity: 1;
        }

        .modal-header h2 {
            margin: 0;
            font-size: 1.5rem;
            color: var(--brown);
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--muted);
            cursor: pointer;
            padding: 0;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-close:hover {
            color: var(--brown);
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--brown);
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid rgba(75,46,22,0.2);
            border-radius: 6px;
            font-family: inherit;
            font-size: 1rem;
            color: var(--brown);
            transition: border-color 0.2s ease, box-shadow 0.2s ease; /* Add transition for form elements */
        }

        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--moss);
            box-shadow: 0 0 0 3px rgba(94,109,59,0.1);
        }

        .modal-footer {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            margin-top: 24px;
            padding-top: 16px;
            border-top: 1px solid rgba(75,46,22,0.1);
        }

        .empty-state {
            text-align: center;
            padding: 60px 24px;
            color: var(--muted);
        }

        .empty-state i {
            font-size: 3rem;
            color: var(--light-sage);
            margin-bottom: 16px;
        }

        tbody tr:nth-child(even) {
            background-color: rgba(94,109,59,0.02);
        }

        tbody tr:hover {
            background-color: rgba(94,109,59,0.06);
        }

        tfoot {
            background: var(--white);
            font-weight: 600;
            border-top: 2px solid var(--moss);
        }
        tfoot td {
            padding: 16px 24px;
            color: var(--brown);
        }
        .footer-stat {
            display: inline-flex;
            align-items: center;
            margin-left: 24px;
            font-size: 0.95rem;
        }
        .footer-stat .value {
            font-size: 1.2rem;
            margin-left: 8px;
            font-weight: 700;
        }
        .footer-stat .confirmé { color: #27ae60; }
        .footer-stat .inscrit { color: #f39c12; }
        .footer-stat .annulé { color: #e74c3c; }

        @media (max-width: 768px) {
            .backoffice-wrapper {
                padding: 16px;
            }

            .header-section {
                padding: 24px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .search-box {
                width: 100%;
            }

            table {
                font-size: 0.85rem;
            }

            th, td {
                padding: 10px 8px;
            }

            .modal-content {
                padding: 24px;
            }
        }
    </style>
</head>
<body>

<div class="backoffice-wrapper">
    <?php if ($error_message): ?>
        <div class="header-section">
            <h1><i class="fas fa-exclamation-triangle"></i> Erreur</h1>
            <p><?= htmlspecialchars($error_message) ?></p>
            <div class="header-actions">
                <a href="index.php" class="btn btn-ghost" style="text-decoration: none;">
                    <i class="fas fa-arrow-left"></i> Retour à la liste
                </a>
            </div>
        </div>
    <?php elseif ($event): ?>
    <!-- Header Section -->
    <div class="header-section">
        <h1><i class="fas fa-users"></i> Participants de l'événement</h1>
        <p>Du <?= htmlspecialchars($event['titre']) ?> - <?= date('d M Y', strtotime($event['date_debut'])) ?> au <?= date('d M Y', strtotime($event['date_fin'])) ?></p>
        
        <div class="header-actions">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput">
            </div>
            <div style="display: flex; gap: 12px;">
                <button class="btn btn-ghost" onclick="exportCSV()">
                    <i class="fas fa-download"></i> Exporter CSV
                </button>
                <a href="index.php" class="btn btn-ghost" style="text-decoration: none;">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-card-label"><i class="fas fa-users"></i> Total</div>
            <div class="stat-card-value" id="totalStats"><?= count($participants) ?></div>
        </div>
        <div class="stat-card" style="border-left-color: #27ae60;">
            <div class="stat-card-label"><i class="fas fa-check-circle"></i> Confirmés</div>
            <div class="stat-card-value" style="color: #27ae60;" id="confirmedStats"><?= count(array_filter($participants, fn($p) => $p['statut'] === 'confirmé')) ?></div>
        </div>
        <div class="stat-card" style="border-left-color: #f39c12;">
            <div class="stat-card-label"><i class="fas fa-hourglass-half"></i> En attente</div>
            <div class="stat-card-value" style="color: #f39c12;" id="pendingStats"><?= count(array_filter($participants, fn($p) => $p['statut'] === 'inscrit')) ?></div>
        </div>
        <div class="stat-card" style="border-left-color: #e74c3c;">
            <div class="stat-card-label"><i class="fas fa-times-circle"></i> Annulés</div>
            <div class="stat-card-value" style="color: #e74c3c;" id="cancelledStats"><?= count(array_filter($participants, fn($p) => $p['statut'] === 'annulé')) ?></div>
        </div>
    </div>

    <!-- Participants Table -->
    <div class="participants-card">
        <div class="card-header">
            <h2>Liste des participants</h2>
            <button class="btn btn-primary" onclick="openAddModal()">
                <i class="fas fa-plus"></i> Ajouter participant
            </button>
        </div>
        <div class="card-body">
            <table>
                <thead>
                    <tr>
                        <th style="width: 40px;"><input type="checkbox" id="selectAll" onchange="toggleSelectAll()"></th>
                        <th>Participant <i class="fas fa-sort sort-icon" data-sort-by="prenom"></i></th>
                        <th>Email <i class="fas fa-sort sort-icon" data-sort-by="email"></i></th>
                        <th>Téléphone <i class="fas fa-sort sort-icon" data-sort-by="num_tel"></i></th>
                        <th>Identité <i class="fas fa-sort sort-icon" data-sort-by="num_identite"></i></th>
                        <th>Date d'inscription <i class="fas fa-sort sort-icon" data-sort-by="date_inscription"></i></th>
                        <th>Statut <i class="fas fa-sort sort-icon" data-sort-by="statut"></i></th>
                        <th>Accompagnants</th>
                        <th>Besoins</th>
                        <th style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody id="participantsTable">
                    
                </tbody>
                <tfoot id="participantsTableFooter">
                    <!-- Les statistiques du pied de page seront injectées ici par JavaScript -->
                </tfoot>
            </table>
        </div>

        <!-- Footer Actions -->
        <div class="footer-actions">
            <div>
                <a href="index.php" class="btn btn-ghost" style="text-decoration: none;">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>
            <div>
                <button class="btn btn-secondary" onclick="bulkStatusChange('inscrit')">
                    <i class="fas fa-hourglass-half"></i> Marquer en attente
                </button>
                <button class="btn btn-primary" onclick="bulkStatusChange('confirmé')" style="margin-left: 8px;">
                    <i class="fas fa-check-circle"></i> Confirmer sélection
                </button>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- View Modal -->
<div id="viewModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Détails du participant</h2>
            <button class="modal-close" onclick="closeModal('viewModal')"><i class="fas fa-times"></i></button>
        </div>
        <div id="viewModalBody"></div>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Modifier le participant</h2>
            <button class="modal-close" onclick="closeModal('editModal')"><i class="fas fa-times"></i></button>
        </div>
        <form id="editForm" onsubmit="saveParticipant(event)">
            <input type="hidden" id="editId">
            <div class="form-group">
                <label>Prénom</label>
                <input type="text" id="editPrenom" placeholder="Ex: Jean">
            </div>
            <div class="form-group">
                <label>Nom</label>
                <input type="text" id="editNom" placeholder="Ex: Dupont">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="text" id="editEmail">
            </div>
            <div class="form-group">
                <label>Numéro de téléphone</label>
                <input type="text" id="editNumTel">
            </div>
            <div class="form-group">
                <label>Numéro d'identité</label>
                <input type="text" id="editNumIdentite">
            </div>
            <div class="form-group">
                <label>Nombre d'accompagnants</label>
                <input type="text" id="editAccompagnants">
            </div>
            <div class="form-group">
                <label>Besoins d'accessibilité</label>
                <textarea id="editBesoins"></textarea>
            </div>
            <div class="form-group">
                <label>Statut</label>
                <select id="editStatut">
                    <option value="inscrit">En attente</option>
                    <option value="confirmé">Confirmé</option>
                    <option value="annulé">Annulé</option>
                </select>
            </div>
            <div class="form-group">
                <label>Message/Notes</label>
                <textarea id="editMessage"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal('editModal')">Annuler</button>
                <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Modal -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Ajouter un participant</h2>
            <button class="modal-close" onclick="closeModal('addModal')"><i class="fas fa-times"></i></button>
        </div>
        <form id="addForm" onsubmit="addParticipant(event)">
            <div class="form-group">
                <label>Prénom</label>
                <input type="text" id="addPrenom" placeholder="Ex: Jean">
            </div>
            <div class="form-group">
                <label>Nom</label>
                <input type="text" id="addNom" placeholder="Ex: Dupont">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="text" id="addEmail">
            </div>
            <div class="form-group">
                <label>Numéro de téléphone</label>
                <input type="text" id="addNumTel">
            </div>
            <div class="form-group">
                <label>Numéro d'identité</label>
                <input type="text" id="addNumIdentite">
            </div>
            <div class="form-group">
                <label>Nombre d'accompagnants</label>
                <input type="text" id="addAccompagnants" value="0">
            </div>
            <div class="form-group">
                <label>Besoins d'accessibilité</label>
                <textarea id="addBesoins"></textarea>
            </div>
            <div class="form-group">
                <label>Statut</label>
                <select id="addStatut">
                    <option value="inscrit">En attente</option>
                    <option value="confirmé">Confirmé</option>
                </select>
            </div>
            <div class="form-group">
                <label>Message/Notes</label>
                <textarea id="addMessage"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal('addModal')">Annuler</button>
                <button type="submit" class="btn btn-primary">Ajouter participant</button>
            </div>
        </form>
    </div>
</div>

<script>
// Data storage (loaded from backend)
let allParticipants = [];
const eventId = <?= intval($eventId) ?>;

// Sorting state
let currentSort = { column: 'date_inscription', direction: 'asc' }; // Default sort

// Function to handle sorting
function sortParticipants(column) {
    if (currentSort.column === column) {
        currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
    } else {
        currentSort.column = column;
        currentSort.direction = 'asc';
    }

    allParticipants.sort((a, b) => {
        const aValue = a[column];
        const bValue = b[column];

        if (typeof aValue === 'string' && typeof bValue === 'string') {
            return currentSort.direction === 'asc' ? aValue.localeCompare(bValue) : bValue.localeCompare(aValue);
        }
        if (typeof aValue === 'number' && typeof bValue === 'number') {
            return currentSort.direction === 'asc' ? aValue - bValue : bValue - aValue;
        }
        // Fallback for other types or mixed types
        if (aValue < bValue) return currentSort.direction === 'asc' ? -1 : 1;
        if (aValue > bValue) return currentSort.direction === 'asc' ? 1 : -1;
        return 0;
    });

    updateTableAndStats();
}

// Helper to fetch JSON and produce helpful error messages
async function fetchJson(url, options = {}){
    const res = await fetch(url, options);
    if(!res.ok){
        // try to read response text for debugging
        let txt = '';
        try { txt = await res.text(); } catch(e) { txt = '<unable to read response body>'; }
        const err = new Error(`HTTP ${res.status} ${res.statusText}: ${txt.substring(0,200)}`);
        err.status = res.status;
        err.body = txt;
        throw err;
    }
    // parse JSON
    const json = await res.json();
    return json;
}

async function loadParticipants(){
    try{
        // Add a cache-busting parameter to ensure fresh data
        const cacheBuster = new Date().getTime();
        const json = await fetchJson(`../../Controller/ParticipationController.php?action=get_event_participants&id=${eventId}&_=${cacheBuster}`);
        if(json && json.success){
            allParticipants = json.data.map(p => ({
                id: parseInt(p.id),
                prenom: p.prenom || '',
                nom: p.nom || '',
                email: p.email || '',
                num_tel: p.num_tel || '', // Added num_tel
                num_identite: p.num_identite || '', // Added num_identite
                date_inscription: p.date_inscription || '',
                statut: p.statut || 'inscrit',
                nombre_accompagnants: parseInt(p.nombre_accompagnants || 0),
                besoins_accessibilite: p.besoins_accessibilite || '',
                message: p.message || ''
            }));
            updateTableAndStats();
        } else {
            console.error('Failed to load participants', json);
            alert('Erreur: réponse invalide du serveur');
        }
    } catch(err){
        console.error('Load participants error:', err);
        alert('Erreur réseau lors du chargement — ' + (err.message || err));
    }
}

// Initial load
loadParticipants();

// Add event listeners for sorting after the DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.sort-icon').forEach(icon => {
        icon.addEventListener('click', (e) => {
            const column = e.target.dataset.sortBy;
            sortParticipants(column);
        });
    });
});

// Modal functions
function openModal(modalId) {
    document.getElementById(modalId).classList.add('active');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('active');
}

// View participant
function openViewModal(id) {
    const p = allParticipants.find(x => x.id === id);
    if(!p) return;
    
    const html = `
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <div style="margin-bottom: 16px;">
                    <div style="color: var(--muted); font-size: 0.9rem; font-weight: 600;">Prénom</div>
                    <div style="font-size: 1.1rem; color: var(--brown); margin-top: 4px;">${p.prenom}</div>
                </div>
                <div style="margin-bottom: 16px;">
                    <div style="color: var(--muted); font-size: 0.9rem; font-weight: 600;">Nom</div>
                    <div style="font-size: 1.1rem; color: var(--brown); margin-top: 4px;">${p.nom}</div>
                </div>
                <div style="margin-bottom: 16px;">
                    <div style="color: var(--muted); font-size: 0.9rem; font-weight: 600;">Email</div>
                    <div style="font-size: 1.1rem; color: var(--brown); margin-top: 4px;"><a href="mailto:${p.email}">${p.email}</a></div>
                </div>
                <div style="margin-bottom: 16px;">
                    <div style="color: var(--muted); font-size: 0.9rem; font-weight: 600;">Numéro de téléphone</div>
                    <div style="font-size: 1.1rem; color: var(--brown); margin-top: 4px;">${p.num_tel || 'N/A'}</div>
                </div>
                <div style="margin-bottom: 16px;">
                    <div style="color: var(--muted); font-size: 0.9rem; font-weight: 600;">Numéro d'identité</div>
                    <div style="font-size: 1.1rem; color: var(--brown); margin-top: 4px;">${p.num_identite || 'N/A'}</div>
                </div>
            </div>
            <div>
                <div style="margin-bottom: 16px;">
                    <div style="color: var(--muted); font-size: 0.9rem; font-weight: 600;">Date d'inscription</div>
                    <div style="font-size: 1.1rem; color: var(--brown); margin-top: 4px;">${new Date(p.date_inscription).toLocaleDateString('fr-FR')}</div>
                </div>
                <div style="margin-bottom: 16px;">
                    <div style="color: var(--muted); font-size: 0.9rem; font-weight: 600;">Statut</div>
                    <div style="margin-top: 4px;">
                        <span class="status-badge status-${p.statut}">
                            ${p.statut === 'confirmé' ? '<i class="fas fa-check-circle"></i> Confirmé' : 
                              p.statut === 'inscrit' ? '<i class="fas fa-hourglass-half"></i> En attente' :
                              '<i class="fas fa-times-circle"></i> Annulé'}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid rgba(75,46,22,0.1);">
            <div style="margin-bottom: 16px;">
                <div style="color: var(--muted); font-size: 0.9rem; font-weight: 600;">Accompagnants</div>
                <div style="font-size: 1.1rem; color: var(--brown); margin-top: 4px;">${p.nombre_accompagnants}</div>
            </div>
            <div style="margin-bottom: 16px;">
                <div style="color: var(--muted); font-size: 0.9rem; font-weight: 600;">Besoins d'accessibilité</div>
                <div style="font-size: 1.1rem; color: var(--brown);
            </div>
            <div>
                <div style="color: var(--muted); font-size: 0.9rem; font-weight: 600;">Message</div>
                <div style="font-size: 1rem; color: var(--brown); margin-top: 4px; background: var(--light-sage); padding: 12px; border-radius: 6px;">${p.message}</div>
            </div>
        </div>
        <div class="modal-footer" style="margin-top: 24px;">
            <button type="button" class="btn btn-ghost" onclick="closeModal('viewModal')">Fermer</button>
            <button type="button" class="btn btn-primary" onclick="openEditModal(${p.id}); closeModal('viewModal');">Éditer</button>
        </div>
    `;
    
    document.getElementById('viewModalBody').innerHTML = html;
    openModal('viewModal');
}

// Edit participant
function openEditModal(id) {
    const p = allParticipants.find(x => x.id === id);
    if(!p) return;
    
    document.getElementById('editId').value = id;
    document.getElementById('editPrenom').value = p.prenom;
    document.getElementById('editNom').value = p.nom;
    document.getElementById('editEmail').value = p.email;
    document.getElementById('editNumTel').value = p.num_tel;
    document.getElementById('editNumIdentite').value = p.num_identite;
    document.getElementById('editAccompagnants').value = p.nombre_accompagnants;
    document.getElementById('editBesoins').value = p.besoins_accessibilite;
    document.getElementById('editStatut').value = p.statut;
    document.getElementById('editMessage').value = p.message;
    
    openModal('editModal');
}

// Save participant
async function saveParticipant(e) {
    e.preventDefault();
    const id = parseInt(document.getElementById('editId').value);
    const payload = new FormData();
    payload.append('id', id);
    payload.append('prenom', document.getElementById('editPrenom').value.trim());
    payload.append('nom', document.getElementById('editNom').value.trim());
    payload.append('email', document.getElementById('editEmail').value);
    payload.append('num_tel', document.getElementById('editNumTel').value);
    payload.append('num_identite', document.getElementById('editNumIdentite').value);
    payload.append('nombre_accompagnants', document.getElementById('editAccompagnants').value);
    payload.append('besoins_accessibilite', document.getElementById('editBesoins').value);
    payload.append('statut', document.getElementById('editStatut').value);
    payload.append('message', document.getElementById('editMessage').value);

    console.log('Attempting to save participant with ID:', id, 'Payload:', Object.fromEntries(payload.entries())); // Debugging line

    try{
        const res = await fetch('../../Controller/ParticipationController.php?action=edit_participant', { method: 'POST', body: payload });
        const text = await res.text();
        console.log('Server response (raw text) for saveParticipant:', text); // Debugging line
        const json = JSON.parse(text);

        if(json.success){
            closeModal('editModal');
            alert('Participant mis à jour avec succès!');
            location.reload(); // Force full page reload
        } else {
            alert(json.error || 'Erreur lors de la mise à jour: Réponse serveur non réussie');
        }
    } catch(err){
        console.error('Error in saveParticipant:', err); // Enhanced error logging
        alert('Erreur réseau ou parsing JSON: ' + (err.message || err));
    }
}

// Add participant
function openAddModal() {
    document.getElementById('addForm').reset();
    openModal('addModal');
}

async function addParticipant(e) {
    e.preventDefault();
    const payload = new FormData();
    payload.append('id_evenement', eventId);
    payload.append('prenom', document.getElementById('addPrenom').value.trim());
    payload.append('nom', document.getElementById('addNom').value.trim());
    payload.append('email', document.getElementById('addEmail').value);
    payload.append('num_tel', document.getElementById('addNumTel').value);
    payload.append('num_identite', document.getElementById('addNumIdentite').value);
    payload.append('nombre_accompagnants', document.getElementById('addAccompagnants').value);
    payload.append('besoins_accessibilite', document.getElementById('addBesoins').value);
    payload.append('statut', document.getElementById('addStatut').value);
    payload.append('message', document.getElementById('addMessage').value);

    try{
        const res = await fetch('../../Controller/ParticipationController.php?action=add_participant', { method: 'POST', body: payload });
        const json = await res.json();
        if(json.success){
            await loadParticipants();
            closeModal('addModal');
            alert('Participant ajouté avec succès!');
        } else {
            alert(json.error || 'Erreur lors de l\'ajout');
        }
    } catch(err){
        console.error(err);
        alert('Erreur réseau');
    }
}

// Delete participant
async function deleteParticipant(id) {
    if(!confirm('Êtes-vous sûr de vouloir supprimer ce participant?')) return;
    const payload = new FormData();
    payload.append('id', id);
    try{
        const res = await fetch('../../Controller/ParticipationController.php?action=delete_participant', { method: 'POST', body: payload });
        const json = await res.json();
        if(json.success){
            await loadParticipants();
            alert('Participant supprimé avec succès!');
        } else {
            alert(json.error || 'Erreur lors de la suppression');
        }
    } catch(err){
        console.error(err);
        alert('Erreur réseau');
    }
}

// Bulk actions
function toggleSelectAll() {
    const checkboxes = document.querySelectorAll('.row-checkbox');
    const isChecked = document.getElementById('selectAll').checked;
    checkboxes.forEach(cb => cb.checked = isChecked);
}

async function bulkDelete() {
    const selected = Array.from(document.querySelectorAll('.row-checkbox:checked'))
        .map(cb => parseInt(cb.closest('tr').dataset.id));
    if(selected.length === 0) { alert('Veuillez sélectionner au moins un participant'); return; }
    if(!confirm(`Êtes-vous sûr de vouloir supprimer ${selected.length} participant(s)?`)) return;
    const payload = new FormData();
    payload.append('type','delete');
    selected.forEach(id => payload.append('ids[]', id));
    try{
        const res = await fetch('../../Controller/ParticipationController.php?action=bulk_action', { method: 'POST', body: payload });
        const json = await res.json();
        if(json.success){ 
            alert(`${json.deleted || 0} participant(s) supprimé(s) avec succès!`);
            location.reload(); 
        }
        else {
            alert(json.error || 'Erreur lors de la suppression');
        }
    } catch(err){ 
        console.error(err); 
        alert('Erreur réseau'); 
    }
}

async function bulkStatusChange(newStatus) {
    const selected = Array.from(document.querySelectorAll('.row-checkbox:checked'))
        .map(cb => parseInt(cb.closest('tr').dataset.id));
    if(selected.length === 0) { alert('Veuillez sélectionner au moins un participant'); return; }
    const payload = new FormData();
    payload.append('type','status');
    payload.append('status', newStatus);
    selected.forEach(id => payload.append('ids[]', id));

    try{
        const res = await fetch('../../Controller/ParticipationController.php?action=bulk_action', { method: 'POST', body: payload });
        const json = await res.json();
        if(json.success){
            alert(`${json.updated || 0} participant(s) mis à jour!`);
            location.reload(); // Force full page reload
        } else {
            alert(json.error || 'Erreur lors de la mise à jour');
        }
    } catch(err){ 
        console.error('Error in bulkStatusChange:', err);
        alert('Erreur réseau ou parsing JSON pour la mise à jour par lot: ' + (err.message || err)); 
    }
}

// Search functionality
document.getElementById('searchInput').addEventListener('keyup', function() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('.participant-row');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? 'table-row' : 'none';
    });
});

function exportCSV() {
    let csv = '\uFEFF'; // Add BOM for UTF-8 compatibility with Excel
    
    // Define headers
    const headers = [
        'Prénom', 'Nom', 'Email', 'Numéro de téléphone', 'Numéro d\'identité',
        'Date d\'inscription', 'Statut', 'Accompagnants', 'Besoins d\'accessibilité', 'Message'
    ];
    csv += headers.join(';') + '\n';

    // Helper function to safely wrap values in quotes
    const safeCsv = (val) => {
        const str = String(val === null || val === undefined ? '' : val);
        // Escape double quotes by doubling them, then wrap the whole string in quotes
        return `"${str.replace(/"/g, '""')}"`;
    };

    // Add data rows
    allParticipants.forEach(p => {
        const row = [
            safeCsv(p.prenom),
            safeCsv(p.nom),
            safeCsv(p.email),
            safeCsv(p.num_tel),
            safeCsv(p.num_identite),
            safeCsv(p.date_inscription),
            safeCsv(p.statut),
            safeCsv(p.nombre_accompagnants),
            safeCsv(p.besoins_accessibilite),
            safeCsv(p.message)
        ].join(';');
        csv += row + '\n';
    });
    
    // Calculate and add statistics to the CSV
    const confirmed = allParticipants.filter(p => p.statut === 'confirmé').length;
    const pending = allParticipants.filter(p => p.statut === 'inscrit').length;
    const cancelled = allParticipants.filter(p => p.statut === 'annulé').length;

    csv += '\n'; // Add a blank line for separation
    csv += 'Statistiques\n';
    csv += `Confirmés;${confirmed}\n`;
    csv += `En attente;${pending}\n`;
    csv += `Annulés;${cancelled}\n`;
    csv += `Total;${allParticipants.length}\n`;

    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `participants-event-${eventId}.csv`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
}


// Update table and stats after modifications
function updateTableAndStats() {
    // Calculate statistics
    const confirmed = allParticipants.filter(p => p.statut === 'confirmé').length;
    const pending = allParticipants.filter(p => p.statut === 'inscrit').length;
    const cancelled = allParticipants.filter(p => p.statut === 'annulé').length;
    
    // Update top statistics cards
    document.getElementById('totalStats').textContent = allParticipants.length;
    document.getElementById('confirmedStats').textContent = confirmed;
    document.getElementById('pendingStats').textContent = pending;
    document.getElementById('cancelledStats').textContent = cancelled;
    
    const tbody = document.getElementById('participantsTable');
    const tfoot = document.getElementById('participantsTableFooter');
    const footerActions = document.querySelector('.footer-actions');

    if (allParticipants.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="10">
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <h3>Aucun participant</h3>
                        <p>Il n'y a actuellement aucun participant inscrit à cet événement.</p>
                        <button class="btn btn-primary" onclick="openAddModal()" style="margin-top: 16px;">
                            <i class="fas fa-plus"></i> Ajouter le premier participant
                        </button>
                    </div>
                </td>
            </tr>`;
        if (footerActions) footerActions.style.display = 'none';
        tfoot.innerHTML = ''; // Clear footer if no participants
    } else {
        tbody.innerHTML = allParticipants.map(p => `
            <tr class="participant-row" data-id="${p.id}" data-status="${p.statut}">
                <td><input type="checkbox" class="row-checkbox"></td>
                <td>
                    <div class="user-badge">
                        <div class="user-avatar">${(p.prenom ? p.prenom.charAt(0).toUpperCase() : '')}${(p.nom ? p.nom.charAt(0).toUpperCase() : '')}</div>
                        <div>
                            <div class="user-name">${p.prenom} ${p.nom}</div>
                            <div class="user-email">${p.email}</div>
                        </div>
                    </div>
                </td>
                <td><a href="mailto:${p.email}" style="color: var(--moss); text-decoration: none;">${p.email}</a></td>
                <td>${p.num_tel || 'N/A'}</td>
                <td>${p.num_identite || 'N/A'}</td>
                <td>${new Date(p.date_inscription).toLocaleDateString('fr-FR')}</td>
                <td>
                    <span class="status-badge status-${p.statut}">
                        ${p.statut === 'confirmé' ? '<i class="fas fa-check-circle"></i> Confirmé' : 
                          p.statut === 'inscrit' ? '<i class="fas fa-hourglass-half"></i> En attente' :
                          '<i class="fas fa-times-circle"></i> Annulé'}
                    </span>
                </td>
                <td><strong>${p.nombre_accompagnants}</strong></td>
                <td>
                    <span style="display: inline-block; background: rgba(94,109,59,0.1); color: var(--moss); padding: 4px 8px; border-radius: 4px; font-size: 0.85rem;">
                        ${p.besoins_accessibilite}
                    </span>
                </td>
                <td>
                    <div class="actions-cell">
                        <button class="btn-action" onclick="openViewModal(${p.id})" title="Voir">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn-action" onclick="openEditModal(${p.id})" title="Éditer">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn-action delete" onclick="deleteParticipant(${p.id})" title="Supprimer">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
        
        // Populate and show the table footer with stats
        tfoot.innerHTML = `
            <tr>
                <td colspan="10" style="text-align: right;">
                    <span class="footer-stat"><i class="fas fa-check-circle" style="color:#27ae60;"></i>&nbsp;Confirmés: <span class="value confirmé">${confirmed}</span></span>
                    <span class="footer-stat"><i class="fas fa-hourglass-half" style="color:#f39c12;"></i>&nbsp;En attente: <span class="value inscrit">${pending}</span></span>
                    <span class="footer-stat"><i class="fas fa-times-circle" style="color:#e74c3c;"></i>&nbsp;Annulés: <span class="value annulé">${cancelled}</span></span>
                    <span class="footer-stat"><strong>Total:</strong> <span class="value">${allParticipants.length}</span></span>
                </td>
            </tr>
        `;
        
        if (footerActions) footerActions.style.display = 'flex';
        document.getElementById('selectAll').checked = false;

        // Update sort icons
        document.querySelectorAll('.sort-icon').forEach(icon => {
            icon.classList.remove('fa-sort-up', 'fa-sort-down', 'fa-sort');
            if (icon.dataset.sortBy === currentSort.column) {
                icon.classList.add(currentSort.direction === 'asc' ? 'fa-sort-up' : 'fa-sort-down');
            } else {
                icon.classList.add('fa-sort');
            }
        });
    }
}

// Close modals on ESC key
document.addEventListener('keydown', function(e) {
    if(e.key === 'Escape') {
        document.querySelectorAll('.modal.active').forEach(m => m.classList.remove('active'));
    }
});

// Close modals on overlay click
document.querySelectorAll('.modal').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if(e.target === this) this.classList.remove('active');
    });
});
</script>

</body>
</html>
