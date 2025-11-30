<?php
// Get event ID from URL
$eventId = isset($_GET['id']) ? intval($_GET['id']) : 1;

// Sample event data - in production, fetch from EventModel
$eventData = [
    1 => ['titre' => 'Formation Langue des Signes Française (LSF)', 'date_event' => '2025-01-20 09:00:00', 'categorie' => 'Formation'],
    2 => ['titre' => 'Nettoyage de Plage Solidaire', 'date_event' => '2025-01-05 10:00:00', 'categorie' => 'Événement Environnemental'],
    3 => ['titre' => 'Forum de l\'Innovation Sociale', 'date_event' => '2025-01-25 14:00:00', 'categorie' => 'Forum'],
];

$event = $eventData[$eventId] ?? $eventData[1];

// Sample participants data - in production, fetch from ParticipationModel
$participants = [
    [
        'id' => 1,
        'nom' => 'Dupont',
        'prenom' => 'Jean',
        'email' => 'jean.dupont@email.com',
        'date_inscription' => '2024-12-10',
        'statut' => 'confirmé',
        'nombre_accompagnants' => 1,
        'besoins_accessibilite' => 'PMR, LSF',
        'message' => 'Merci pour cette opportunité'
    ],
    [
        'id' => 2,
        'nom' => 'Martin',
        'prenom' => 'Sophie',
        'email' => 'sophie.martin@email.com',
        'date_inscription' => '2024-12-11',
        'statut' => 'confirmé',
        'nombre_accompagnants' => 0,
        'besoins_accessibilite' => 'Repos',
        'message' => 'Très intéressé par cette formation'
    ],
    [
        'id' => 3,
        'nom' => 'Colbert',
        'prenom' => 'Luc',
        'email' => 'luc.colbert@email.com',
        'date_inscription' => '2024-12-12',
        'statut' => 'inscrit',
        'nombre_accompagnants' => 2,
        'besoins_accessibilite' => 'Sous-titres, Parking accessible',
        'message' => 'J\'aurai besoin d\'informations supplémentaires'
    ],
];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Participants - <?= htmlspecialchars($event['titre']) ?> - Backoffice</title>
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
            transition: all 0.2s;
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
            cursor: pointer;
            transition: all 0.2s;
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
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            align-items: center;
            justify-content: center;
            z-index: 1000;
            padding: 20px;
        }

        .modal.active {
            display: flex;
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
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            border-bottom: 1px solid rgba(75,46,22,0.1);
            padding-bottom: 16px;
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
    <!-- Header Section -->
    <div class="header-section">
        <h1><i class="fas fa-users"></i> Participants de l'événement</h1>
        <p><?= htmlspecialchars($event['titre']) ?> - <?= date('d M Y', strtotime($event['date_event'])) ?></p>
        
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
            <?php if(count($participants) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th style="width: 40px;"><input type="checkbox" id="selectAll" onchange="toggleSelectAll()"></th>
                            <th>Participant</th>
                            <th>Email</th>
                            <th>Date d'inscription</th>
                            <th>Statut</th>
                            <th>Accompagnants</th>
                            <th>Besoins</th>
                            <th style="width: 120px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="participantsTable">
                        <?php foreach($participants as $p): ?>
                            <tr class="participant-row" data-id="<?= $p['id'] ?>" data-status="<?= $p['statut'] ?>">
                                <td><input type="checkbox" class="row-checkbox"></td>
                                <td>
                                    <div class="user-badge">
                                        <div class="user-avatar"><?= strtoupper(substr($p['prenom'], 0, 1) . substr($p['nom'], 0, 1)) ?></div>
                                        <div>
                                            <div class="user-name"><?= htmlspecialchars($p['prenom'] . ' ' . $p['nom']) ?></div>
                                            <div class="user-email"><?= htmlspecialchars($p['email']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td><a href="mailto:<?= htmlspecialchars($p['email']) ?>" style="color: var(--moss); text-decoration: none;"><?= htmlspecialchars($p['email']) ?></a></td>
                                <td><?= date('d/m/Y', strtotime($p['date_inscription'])) ?></td>
                                <td>
                                    <span class="status-badge status-<?= $p['statut'] ?>">
                                        <?php if($p['statut'] === 'confirmé'): ?>
                                            <i class="fas fa-check-circle"></i> Confirmé
                                        <?php elseif($p['statut'] === 'inscrit'): ?>
                                            <i class="fas fa-hourglass-half"></i> En attente
                                        <?php else: ?>
                                            <i class="fas fa-times-circle"></i> Annulé
                                        <?php endif; ?>
                                    </span>
                                </td>
                                <td><strong><?= $p['nombre_accompagnants'] ?></strong></td>
                                <td>
                                    <span style="display: inline-block; background: rgba(94,109,59,0.1); color: var(--moss); padding: 4px 8px; border-radius: 4px; font-size: 0.85rem;">
                                        <?= htmlspecialchars($p['besoins_accessibilite']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="actions-cell">
                                        <button class="btn-action" onclick="openViewModal(<?= $p['id'] ?>)" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn-action" onclick="openEditModal(<?= $p['id'] ?>)" title="Éditer">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn-action delete" onclick="deleteParticipant(<?= $p['id'] ?>)" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h3>Aucun participant</h3>
                    <p>Il n'y a actuellement aucun participant inscrit à cet événement.</p>
                    <button class="btn btn-primary" onclick="openAddModal()" style="margin-top: 16px;">
                        <i class="fas fa-plus"></i> Ajouter le premier participant
                    </button>
                </div>
            <?php endif; ?>
        </div>

        <!-- Footer Actions -->
        <?php if(count($participants) > 0): ?>
            <div class="footer-actions">
                <button class="btn btn-ghost" onclick="bulkDelete()">
                    <i class="fas fa-trash"></i> Supprimer sélection
                </button>
                <div>
                    <button class="btn btn-secondary" onclick="bulkStatusChange('inscrit')">
                        <i class="fas fa-hourglass-half"></i> Marquer en attente
                    </button>
                    <button class="btn btn-primary" onclick="bulkStatusChange('confirmé')" style="margin-left: 8px;">
                        <i class="fas fa-check-circle"></i> Confirmer sélection
                    </button>
                </div>
            </div>
        <?php endif; ?>
    </div>
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
                <input type="text" id="editPrenom">
            </div>
            <div class="form-group">
                <label>Nom</label>
                <input type="text" id="editNom">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="text" id="editEmail">
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
                <input type="text" id="addPrenom">
            </div>
            <div class="form-group">
                <label>Nom</label>
                <input type="text" id="addNom">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="text" id="addEmail">
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
        const json = await fetchJson('/test/Controller/ParticipationController.php?action=get_event_participants&id=' + eventId);
        if(json && json.success){
            allParticipants = json.data.map(p => ({
                id: parseInt(p.id),
                prenom: p.prenom || '',
                nom: p.nom || '',
                email: p.email || '',
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
                <div style="font-size: 1.1rem; color: var(--brown); margin-top: 4px;">${p.besoins_accessibilite}</div>
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
    payload.append('prenom', document.getElementById('editPrenom').value);
    payload.append('nom', document.getElementById('editNom').value);
    payload.append('email', document.getElementById('editEmail').value);
    payload.append('nombre_accompagnants', document.getElementById('editAccompagnants').value);
    payload.append('besoins_accessibilite', document.getElementById('editBesoins').value);
    payload.append('statut', document.getElementById('editStatut').value);
    payload.append('message', document.getElementById('editMessage').value);

    try{
        const res = await fetch('/test/Controller/ParticipationController.php?action=edit_participant', { method: 'POST', body: payload });
        const json = await res.json();
        if(json.success){
            await loadParticipants();
            closeModal('editModal');
            alert('Participant mis à jour avec succès!');
        } else {
            alert(json.error || 'Erreur lors de la mise à jour');
        }
    } catch(err){
        console.error(err);
        alert('Erreur réseau');
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
    payload.append('prenom', document.getElementById('addPrenom').value);
    payload.append('nom', document.getElementById('addNom').value);
    payload.append('email', document.getElementById('addEmail').value);
    payload.append('nombre_accompagnants', document.getElementById('addAccompagnants').value);
    payload.append('besoins_accessibilite', document.getElementById('addBesoins').value);
    payload.append('statut', document.getElementById('addStatut').value);
    payload.append('message', document.getElementById('addMessage').value);

    try{
        const res = await fetch('/test/Controller/ParticipationController.php?action=add_participant', { method: 'POST', body: payload });
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
        const res = await fetch('/test/Controller/ParticipationController.php?action=delete_participant', { method: 'POST', body: payload });
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
        const res = await fetch('/test/Controller/ParticipationController.php?action=bulk_action', { method: 'POST', body: payload });
        const json = await res.json();
        if(json.success){ await loadParticipants(); alert(`${json.deleted || 0} participant(s) supprimé(s) avec succès!`); }
        else alert(json.error || 'Erreur lors de la suppression');
    } catch(err){ console.error(err); alert('Erreur réseau'); }
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
        if(json.success){ await loadParticipants(); alert(`${json.updated || 0} participant(s) mis à jour!`); }
        else alert(json.error || 'Erreur lors de la mise à jour');
    } catch(err){ console.error(err); alert('Erreur réseau'); }
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

// Export CSV
function exportCSV() {
    let csv = 'Prénom,Nom,Email,Date d\'inscription,Statut,Accompagnants,Besoins d\'accessibilité\n';
    
    allParticipants.forEach(p => {
        csv += `"${p.prenom}","${p.nom}","${p.email}","${p.date_inscription}","${p.statut}",${p.nombre_accompagnants},"${p.besoins_accessibilite}"\n`;
    });
    
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `participants-event-${eventId}.csv`;
    a.click();
    window.URL.revokeObjectURL(url);
}

// Update table and stats after modifications
function updateTableAndStats() {
    // Update statistics
    document.getElementById('totalStats').textContent = allParticipants.length;
    document.getElementById('confirmedStats').textContent = allParticipants.filter(p => p.statut === 'confirmé').length;
    document.getElementById('pendingStats').textContent = allParticipants.filter(p => p.statut === 'inscrit').length;
    document.getElementById('cancelledStats').textContent = allParticipants.filter(p => p.statut === 'annulé').length;
    
    // Rebuild table
    const tbody = document.getElementById('participantsTable');
    if(allParticipants.length === 0) {
        tbody.parentElement.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h3>Aucun participant</h3>
                <p>Il n'y a actuellement aucun participant inscrit à cet événement.</p>
                <button class="btn btn-primary" onclick="openAddModal()" style="margin-top: 16px;">
                    <i class="fas fa-plus"></i> Ajouter le premier participant
                </button>
            </div>
        `;
        return;
    }
    
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
    
    document.getElementById('selectAll').checked = false;
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
