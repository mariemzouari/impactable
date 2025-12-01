<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>BackOffice Admin - ImpactAble</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <style>
        * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Inter', sans-serif;
    background: linear-gradient(135deg, #F4ECDD 0%, #FFF4F5 100%);
    min-height: 100vh;
    display: flex;
    overflow-x: hidden;
}

/* Sidebar */
.sidebar {
    width: 280px;
    background: linear-gradient(180deg, #4B2E16 0%, #5E6D38 100%);
    color: white;
    height: 100vh;
    position: fixed;
    left: 0;
    top: 0;
    display: flex;
    flex-direction: column;
    box-shadow: 4px 0 15px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    z-index: 1000;
}

.sidebar.collapsed {
    transform: translateX(-280px);
}

.sidebar-header {
    padding: 25px 20px;
    border-bottom: 2px solid rgba(255, 255, 255, 0.1);
    text-align: center;
}

.sidebar-header h1 {
    font-size: 1.8em;
    font-weight: 700;
    color: #F4ECDD;
    margin-bottom: 5px;
}

.sidebar-header h2 {
    font-size: 1.1em;
    font-weight: 600;
    color: #A9B57D;
    margin: 0;
}

.sidebar-nav {
    flex: 1;
    padding: 20px 0;
    overflow-y: auto;
}

.nav-item {
    display: flex;
    align-items: center;
    padding: 15px 25px;
    color: #F4ECDD;
    text-decoration: none;
    transition: all 0.3s ease;
    position: relative;
    gap: 15px;
    cursor: pointer;
}

.nav-item:hover {
    background: rgba(255, 255, 255, 0.1);
    padding-left: 30px;
}

.nav-item.active {
    background: rgba(255, 255, 255, 0.2);
    border-left: 4px solid #F4ECDD;
    font-weight: 600;
}

.nav-item i {
    font-size: 1.2em;
    min-width: 25px;
}

.nav-item .badge {
    margin-left: auto;
    background: #D94839;
    color: white;
    padding: 3px 10px;
    border-radius: 12px;
    font-size: 0.85em;
    font-weight: 600;
}

.sidebar-footer {
    border-top: 2px solid rgba(255, 255, 255, 0.1);
    padding: 20px;
}

.admin-profile {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 15px;
    padding: 10px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 12px;
}

.admin-avatar {
    width: 45px;
    height: 45px;
    background: #A9B57D;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5em;
}

.admin-info {
    flex: 1;
}

.admin-name {
    font-weight: 600;
    color: #F4ECDD;
}

.admin-role {
    font-size: 0.85em;
    color: #A9B57D;
}

.logout-btn {
    width: 100%;
    padding: 12px;
    background: rgba(217, 72, 57, 0.2);
    border: 2px solid #D94839;
    color: #F4ECDD;
    border-radius: 10px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s;
}

.logout-btn:hover {
    background: #D94839;
    transform: translateY(-2px);
}

/* Main Content */
.main-content {
    flex: 1;
    margin-left: 280px;
    padding: 0;
    transition: margin-left 0.3s ease;
}

.main-content.expanded {
    margin-left: 0;
}

/* Top Header */
.top-header {
    background: white;
    padding: 20px 30px;
    box-shadow: 0 2px 10px rgba(75, 46, 22, 0.1);
    display: flex;
    align-items: center;
    gap: 20px;
    position: sticky;
    top: 0;
    z-index: 100;
}

.menu-toggle {
    display: none;
    background: none;
    border: none;
    font-size: 1.5em;
    color: #4B2E16;
    cursor: pointer;
    padding: 8px;
}

.page-title {
    flex: 1;
    color: #4B2E16;
    font-size: 1.8em;
    font-weight: 700;
}

.header-actions {
    display: flex;
    gap: 15px;
}

.notification-btn,
.refresh-btn {
    position: relative;
    background: #F4ECDD;
    border: 2px solid #A9B57D;
    width: 45px;
    height: 45px;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s;
    font-size: 1.2em;
    color: #5E6D38;
}

.notification-btn:hover,
.refresh-btn:hover {
    background: #5E6D38;
    color: white;
    transform: translateY(-2px);
}

.notification-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #D94839;
    color: white;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    font-size: 0.7em;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Pages */
.page {
    display: none;
    padding: 30px;
    animation: fadeIn 0.5s;
}

.page.active {
    display: block;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 25px;
    margin-bottom: 40px;
}

.stat-card {
    background: white;
    border: 3px solid #F4ECDD;
    border-radius: 20px;
    padding: 25px;
    display: flex;
    align-items: center;
    gap: 20px;
    box-shadow: 0 4px 15px rgba(75, 46, 22, 0.1);
    transition: all 0.3s;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(75, 46, 22, 0.15);
}

.stat-icon {
    width: 70px;
    height: 70px;
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2em;
    color: white;
}

.stat-content {
    flex: 1;
}

.stat-value {
    font-size: 2.5em;
    font-weight: 700;
    color: #4B2E16;
    line-height: 1;
}

.stat-label {
    color: #5E6D38;
    font-size: 1em;
    margin-top: 5px;
}

/* Section Title */
.section-title {
    color: #4B2E16;
    font-size: 1.6em;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    gap: 12px;
}

/* Dashboard Section */
.dashboard-section {
    background: white;
    border: 3px solid #F4ECDD;
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 4px 15px rgba(75, 46, 22, 0.1);
}

.activity-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.activity-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    background: #FFF4F5;
    border-radius: 12px;
    border-left: 4px solid #A9B57D;
}

.activity-icon {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2em;
    color: white;
}

.activity-content {
    flex: 1;
}

.activity-title {
    font-weight: 600;
    color: #4B2E16;
    margin-bottom: 3px;
}

.activity-meta {
    font-size: 0.9em;
    color: #5E6D38;
}

/* Filters Section */
.filters-section {
    background: white;
    border: 3px solid #F4ECDD;
    border-radius: 20px;
    padding: 25px;
    margin-bottom: 25px;
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
    box-shadow: 0 4px 15px rgba(75, 46, 22, 0.1);
}

.search-box {
    flex: 1;
    min-width: 250px;
    position: relative;
}

.search-box i {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #5E6D38;
    font-size: 1.1em;
}

.search-box input {
    width: 100%;
    padding: 12px 15px 12px 45px;
    border: 2px solid #A9B57D;
    border-radius: 12px;
    font-size: 1em;
    font-family: 'Inter', sans-serif;
}

.search-box input:focus {
    outline: none;
    border-color: #5E6D38;
    box-shadow: 0 0 0 4px rgba(94, 109, 56, 0.1);
}

.filter-group {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.filter-select {
    padding: 12px 15px;
    border: 2px solid #A9B57D;
    border-radius: 12px;
    font-size: 0.95em;
    font-family: 'Inter', sans-serif;
    color: #4B2E16;
    cursor: pointer;
    background: white;
}

/* Table */
.table-container {
    background: white;
    border: 3px solid #F4ECDD;
    border-radius: 20px;
    padding: 0;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(75, 46, 22, 0.1);
}

.reclamations-table {
    width: 100%;
    border-collapse: collapse;
}

.reclamations-table thead {
    background: linear-gradient(135deg, #5E6D38, #4B2E16);
    color: white;
}

.reclamations-table th {
    padding: 18px 15px;
    text-align: left;
    font-weight: 600;
    font-size: 0.95em;
}

.reclamations-table tbody tr {
    border-bottom: 1px solid #F4ECDD;
    transition: all 0.3s;
}

.reclamations-table tbody tr:hover {
    background: #FFF4F5;
}

.reclamations-table td {
    padding: 15px;
    color: #4B2E16;
}

.priority-badge {
    padding: 5px 12px;
    border-radius: 15px;
    font-size: 0.85em;
    font-weight: 600;
    display: inline-block;
}

.priority-urgente {
    background: #F8D7DA;
    color: #721C24;
}

.priority-haute {
    background: #FFE082;
    color: #856404;
}

.priority-moyenne {
    background: #CCE5FF;
    color: #004085;
}

.priority-basse {
    background: #D4EDDA;
    color: #155724;
}

.status-badge-table {
    padding: 5px 12px;
    border-radius: 15px;
    font-size: 0.85em;
    font-weight: 600;
    display: inline-block;
}

.status-en_attente {
    background: #FFF3CD;
    color: #856404;
}

.status-en_cours {
    background: #CCE5FF;
    color: #004085;
}

.status-resolu {
    background: #D4EDDA;
    color: #155724;
}

.status-rejete {
    background: #F8D7DA;
    color: #721C24;
}

.action-buttons {
    display: flex;
    gap: 8px;
}

.btn-view,
.btn-edit,
.btn-delete {
    padding: 8px 12px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 0.9em;
    transition: all 0.3s;
    color: white;
    font-weight: 600;
}

.btn-view {
    background: linear-gradient(135deg, #5E6D38, #4B2E16);
}

.btn-view:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(94, 109, 56, 0.4);
}

.btn-edit {
    background: linear-gradient(135deg, #0277BD, #01579B);
}

.btn-delete {
    background: linear-gradient(135deg, #D32F2F, #B71C1C);
}

/* Modal */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(75, 46, 22, 0.5);
    z-index: 2000;
    align-items: center;
    justify-content: center;
}

.modal.active {
    display: flex;
}

.modal-content {
    background: white;
    border-radius: 20px;
    width: 90%;
    max-width: 800px;
    max-height: 90vh;
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(75, 46, 22, 0.3);
    animation: modalSlide 0.3s ease-out;
}

@keyframes modalSlide {
    from { transform: translateY(-50px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

.modal-header {
    background: linear-gradient(135deg, #5E6D38, #4B2E16);
    color: white;
    padding: 25px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h2 {
    margin: 0;
    font-size: 1.5em;
}

.close-modal {
    background: none;
    border: none;
    color: white;
    font-size: 1.5em;
    cursor: pointer;
    padding: 5px;
    transition: transform 0.3s;
}

.close-modal:hover {
    transform: rotate(90deg);
}

.modal-body {
    padding: 30px;
    max-height: calc(90vh - 180px);
    overflow-y: auto;
}

.modal-footer {
    padding: 20px 30px;
    border-top: 2px solid #F4ECDD;
    display: flex;
    justify-content: flex-end;
    gap: 15px;
}

.btn-secondary {
    padding: 12px 30px;
    background: #A9B57D;
    color: white;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s;
}

.btn-secondary:hover {
    background: #5E6D38;
    transform: translateY(-2px);
}

/* Responsive */
@media (max-width: 1024px) {
    .sidebar {
        transform: translateX(-280px);
    }

    .sidebar.active {
        transform: translateX(0);
    }

    .main-content {
        margin-left: 0;
    }

    .menu-toggle {
        display: block;
    }
}

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }

    .table-container {
        overflow-x: auto;
    }

    .reclamations-table {
        min-width: 900px;
    }
}
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h1>🎯 ImpactAble</h1>
            <h2>Admin Panel</h2>
        </div>

        <nav class="sidebar-nav">
            <a class="nav-item active" data-page="dashboard">
                <i class="fas fa-chart-line"></i>
                <span>Tableau de bord</span>
            </a>
            <a class="nav-item" data-page="reclamations">
                <i class="fas fa-inbox"></i>
                <span>Réclamations</span>
                <span class="badge">8</span>
            </a>
            <a class="nav-item" data-page="statistiques">
                <i class="fas fa-chart-bar"></i>
                <span>Statistiques</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="admin-profile">
                <div class="admin-avatar">👤</div>
                <div class="admin-info">
                    <div class="admin-name">Admin</div>
                    <div class="admin-role">Administrateur</div>
                </div>
            </div>
            <button class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Déconnexion
            </button>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <header class="top-header">
            <button class="menu-toggle" id="menuToggle">
                <i class="fas fa-bars"></i>
            </button>
            <h1 class="page-title">Tableau de bord</h1>
            <div class="header-actions">
                <button class="notification-btn">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">5</span>
                </button>
                <button class="refresh-btn" id="refreshBtn">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
        </header>

        <!-- Dashboard Page -->
        <div class="page active" id="dashboard-page">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #FFF3CD, #FFE082);">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value" id="stat-attente">3</div>
                        <div class="stat-label">En attente</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #CCE5FF, #90CAF9);">
                        <i class="fas fa-spinner"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value" id="stat-cours">3</div>
                        <div class="stat-label">En cours</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #D4EDDA, #A5D6A7);">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value" id="stat-resolu">1</div>
                        <div class="stat-label">Résolues</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #F8D7DA, #EF9A9A);">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value" id="stat-urgent">2</div>
                        <div class="stat-label">Urgentes</div>
                    </div>
                </div>
            </div>

            <div class="dashboard-section">
                <h2 class="section-title">
                    <i class="fas fa-history"></i> Activité récente
                </h2>
                <div class="activity-list" id="activityList"></div>
            </div>
        </div>

        <!-- Reclamations Page -->
        <div class="page" id="reclamations-page">
            <div class="filters-section">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchReclamations" placeholder="Rechercher par ID, nom, sujet...">
                </div>
                
                <div class="filter-group">
                    <select id="filterStatus" class="filter-select">
                        <option value="">Tous les statuts</option>
                        <option value="en_attente">En attente</option>
                        <option value="en_cours">En cours</option>
                        <option value="resolu">Résolu</option>
                        <option value="rejete">Rejeté</option>
                    </select>

                    <select id="filterPriorite" class="filter-select">
                        <option value="">Toutes priorités</option>
                        <option value="urgente">Urgente</option>
                        <option value="haute">Haute</option>
                        <option value="moyenne">Moyenne</option>
                        <option value="basse">Basse</option>
                    </select>
                </div>
            </div>

            <div class="table-container">
                <table class="reclamations-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Client</th>
                            <th>Sujet</th>
                            <th>Catégorie</th>
                            <th>Priorité</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="reclamationsTableBody"></tbody>
                </table>
            </div>
        </div>

        <!-- Statistiques Page -->
        <div class="page" id="statistiques-page">
            <h2 class="section-title">
                <i class="fas fa-chart-pie"></i> Statistiques détaillées
            </h2>
            <p style="color: #5E6D38; padding: 20px;">Section en cours de développement...</p>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal" id="detailsModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Détails de la réclamation</h2>
                <button class="close-modal" id="closeModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body" id="modalBody"></div>
            <div class="modal-footer">
                <button class="btn-secondary" id="closeModalBtn">Fermer</button>
            </div>
        </div>
    </div>

    <script>
        // MODEL
const adminModel = {
    reclamations: [
        {
            id: 'REC-12A4B5C',
            sujet: 'Problème d\'accessibilité rampe',
            description: 'Rampe trop raide pour fauteuils roulants',
            categorie: 'accessibilite',
            lieu: 'Clinique La Rose, Tunis',
            dateIncident: '2024-11-10',
            dateCreation: '2024-11-12',
            status: 'en_cours',
            priorite: 'haute',
            nom: 'Ben Salah',
            prenom: 'Ahmed',
            email: 'ahmed.bs@email.com',
            telephone: '+216 98 123 456'
        },
        {
            id: 'REC-78X9Y2Z',
            sujet: 'Refus d\'accès au service',
            description: 'Discrimination handicap invisible',
            categorie: 'discrimination',
            lieu: 'Mairie de Sfax',
            dateIncident: '2024-10-28',
            dateCreation: '2024-10-29',
            status: 'en_attente',
            priorite: 'urgente',
            nom: 'Trabelsi',
            prenom: 'Fatima',
            email: 'fatima.t@email.com',
            telephone: '+216 50 987 654'
        },
        {
            id: 'REC-45K6L7M',
            sujet: 'Transport inaccessible',
            description: 'Bus sans rampe d\'accès',
            categorie: 'transport',
            lieu: 'Tunis Centre-Ville',
            dateIncident: '2024-11-05',
            dateCreation: '2024-11-06',
            status: 'resolu',
            priorite: 'moyenne',
            nom: 'Karray',
            prenom: 'Mohamed',
            email: 'mohamed.k@email.com',
            telephone: '+216 22 456 789'
        },
        {
            id: 'REC-89P3Q4R',
            sujet: 'Discrimination embauche',
            description: 'Refus candidature handicap visuel',
            categorie: 'emploi',
            lieu: 'Société TechCorp, Ariana',
            dateIncident: '2024-11-01',
            dateCreation: '2024-11-02',
            status: 'en_cours',
            priorite: 'haute',
            nom: 'Mansouri',
            prenom: 'Leila',
            email: 'leila.m@email.com',
            telephone: '+216 28 567 890'
        },
        {
            id: 'REC-56T7U8V',
            sujet: 'École non adaptée',
            description: 'Pas d\'ascenseur ni toilettes adaptées',
            categorie: 'education',
            lieu: 'École Ibn Khaldoun, Sousse',
            dateIncident: '2024-10-15',
            dateCreation: '2024-10-16',
            status: 'en_attente',
            priorite: 'urgente',
            nom: 'Gharbi',
            prenom: 'Sami',
            email: 'sami.g@email.com',
            telephone: '+216 55 234 567'
        },
        {
            id: 'REC-23W4X5Y',
            sujet: 'Refus de soins',
            description: 'Médecin refusant consultation',
            categorie: 'sante',
            lieu: 'Cabinet Dr. Mansour, Monastir',
            dateIncident: '2024-11-08',
            dateCreation: '2024-11-09',
            status: 'en_attente',
            priorite: 'haute',
            nom: 'Bouzid',
            prenom: 'Nadia',
            email: 'nadia.b@email.com',
            telephone: '+216 29 876 543'
        },
        {
            id: 'REC-67Z8A9B',
            sujet: 'Site web inaccessible',
            description: 'Non compatible lecteurs écran',
            categorie: 'administration',
            lieu: 'Site impots.gov.tn',
            dateIncident: '2024-11-11',
            dateCreation: '2024-11-12',
            status: 'en_cours',
            priorite: 'moyenne',
            nom: 'Jebali',
            prenom: 'Youssef',
            email: 'youssef.j@email.com',
            telephone: '+216 26 345 678'
        },
        {
            id: 'REC-90C1D2E',
            sujet: 'Harcèlement au travail',
            description: 'Harcèlement handicap psychique',
            categorie: 'emploi',
            lieu: 'Entreprise XYZ, La Marsa',
            dateIncident: '2024-10-25',
            dateCreation: '2024-10-26',
            status: 'rejete',
            priorite: 'haute',
            nom: 'Sassi',
            prenom: 'Rim',
            email: 'rim.s@email.com',
            telephone: '+216 24 789 012'
        }
    ],
    
    getStats() {
        return {
            enAttente: this.reclamations.filter(r => r.status === 'en_attente').length,
            enCours: this.reclamations.filter(r => r.status === 'en_cours').length,
            resolues: this.reclamations.filter(r => r.status === 'resolu').length,
            urgentes: this.reclamations.filter(r => r.priorite === 'urgente').length
        };
    },
    
    filterReclamations(filters) {
        let results = [...this.reclamations];
        if (filters.search) {
            const s = filters.search.toLowerCase();
            results = results.filter(r =>
                r.id.toLowerCase().includes(s) ||
                r.nom.toLowerCase().includes(s) ||
                r.sujet.toLowerCase().includes(s)
            );
        }
        if (filters.status) results = results.filter(r => r.status === filters.status);
        if (filters.priorite) results = results.filter(r => r.priorite === filters.priorite);
        return results;
    }
};

// VIEW
const adminView = {
    showReclamations(reclamations) {
        const tbody = document.getElementById('reclamationsTableBody');
        tbody.innerHTML = '';
        
        reclamations.forEach(r => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><strong>${r.id}</strong></td>
                <td>${new Date(r.dateCreation).toLocaleDateString('fr')}</td>
                <td>${r.prenom} ${r.nom}</td>
                <td>${r.sujet.substring(0, 30)}...</td>
                <td>${r.categorie}</td>
                <td><span class="priority-badge priority-${r.priorite}">${r.priorite}</span></td>
                <td><span class="status-badge-table status-${r.status}">${r.status.replace('_', ' ')}</span></td>
                <td>
                    <div class="action-buttons">
                        <button class="btn-view" onclick="viewDetails('${r.id}')">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn-edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn-delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            `;
            tbody.appendChild(tr);
        });
    },
    
    showActivities() {
        const list = document.getElementById('activityList');
        const activities = adminModel.reclamations.slice(0, 5);
        list.innerHTML = '';
        
        activities.forEach(r => {
            const colors = {
                en_attente: 'background: linear-gradient(135deg, #FFF3CD, #FFE082);',
                en_cours: 'background: linear-gradient(135deg, #CCE5FF, #90CAF9);',
                resolu: 'background: linear-gradient(135deg, #D4EDDA, #A5D6A7);'
            };
            
            const div = document.createElement('div');
            div.className = 'activity-item';
            div.innerHTML = `
                <div class="activity-icon" style="${colors[r.status] || ''}">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="activity-content">
                    <div class="activity-title">${r.id}</div>
                    <div class="activity-meta">${r.prenom} ${r.nom} - ${r.sujet}</div>
                </div>
                <div class="activity-meta">${new Date(r.dateCreation).toLocaleDateString('fr')}</div>
            `;
            list.appendChild(div);
        });
    },
    
    showModal(rec) {
        const modal = document.getElementById('detailsModal');
        const body = document.getElementById('modalBody');
        body.innerHTML = `
            <div style="display: grid; gap: 20px;">
                <div style="background: #FFF4F5; padding: 20px; border-radius: 12px;">
                    <h3 style="color: #4B2E16; margin-bottom: 15px;">
                        <i class="fas fa-info-circle"></i> Informations
                    </h3>
                    <p><strong>ID:</strong> ${rec.id}</p>
                    <p><strong>Date:</strong> ${new Date(rec.dateCreation).toLocaleDateString('fr')}</p>
                    <p><strong>Statut:</strong> <span class="status-badge-table status-${rec.status}">${rec.status}</span></p>
                    <p><strong>Priorité:</strong> <span class="priority-badge priority-${rec.priorite}">${rec.priorite}</span></p>
                </div>
                <div style="background: #FFF4F5; padding: 20px; border-radius: 12px;">
                    <h3 style="color: #4B2E16; margin-bottom: 15px;">
                        <i class="fas fa-user"></i> Client
                    </h3>
                    <p><strong>Nom:</strong> ${rec.prenom} ${rec.nom}</p>
                    <p><strong>Email:</strong> ${rec.email}</p>
                    <p><strong>Téléphone:</strong> ${rec.telephone}</p>
                </div>
                <div style="background: #FFF4F5; padding: 20px; border-radius: 12px;">
                    <h3 style="color: #4B2E16; margin-bottom: 15px;">
                        <i class="fas fa-file-alt"></i> Réclamation
                    </h3>
                    <p><strong>Sujet:</strong> ${rec.sujet}</p>
                    <p><strong>Catégorie:</strong> ${rec.categorie}</p>
                    <p><strong>Lieu:</strong> ${rec.lieu}</p>
                    <p><strong>Description:</strong></p>
                    <p style="background: white; padding: 15px; border-radius: 8px; margin-top: 10px;">
                        ${rec.description}
                    </p>
                </div>
            </div>
        `;
        modal.classList.add('active');
    }
};

// CONTROLLER
const adminController = {
    currentFilters: { search: '', status: '', priorite: '' },
    
    init() {
        this.loadDashboard();
        this.loadReclamations();
        this.setupEvents();
    },
    
    setupEvents() {
        document.querySelectorAll('.nav-item').forEach(item => {
            item.addEventListener('click', (e) => {
                e.preventDefault();
                const page = e.currentTarget.dataset.page;
                this.switchPage(page);
            });
        });
        
        document.getElementById('searchReclamations').addEventListener('input', (e) => {
            this.currentFilters.search = e.target.value;
            this.applyFilters();
        });
        
        document.getElementById('filterStatus').addEventListener('change', (e) => {
            this.currentFilters.status = e.target.value;
            this.applyFilters();
        });
        
        document.getElementById('filterPriorite').addEventListener('change', (e) => {
            this.currentFilters.priorite = e.target.value;
            this.applyFilters();
        });
        
        document.getElementById('closeModal').addEventListener('click', () => {
            document.getElementById('detailsModal').classList.remove('active');
        });
        
        document.getElementById('closeModalBtn').addEventListener('click', () => {
            document.getElementById('detailsModal').classList.remove('active');
        });
        
        document.getElementById('menuToggle').addEventListener('click', () => {
            document.getElementById('sidebar').classList.toggle('active');
        });
    },
    
    switchPage(page) {
        document.querySelectorAll('.nav-item').forEach(i => i.classList.remove('active'));
        document.querySelector(`[data-page="${page}"]`).classList.add('active');
        
        document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
        document.getElementById(`${page}-page`).classList.add('active');
        
        const titles = {
            dashboard: 'Tableau de bord',
            reclamations: 'Gestion des Réclamations',
            statistiques: 'Statistiques'
        };
        document.querySelector('.page-title').textContent = titles[page];
    },
    
    loadDashboard() {
        const stats = adminModel.getStats();
        document.getElementById('stat-attente').textContent = stats.enAttente;
        document.getElementById('stat-cours').textContent = stats.enCours;
        document.getElementById('stat-resolu').textContent = stats.resolues;
        document.getElementById('stat-urgent').textContent = stats.urgentes;
        
        adminView.showActivities();
    },
    
    loadReclamations() {
        adminView.showReclamations(adminModel.reclamations);
    },
    
    applyFilters() {
        const filtered = adminModel.filterReclamations(this.currentFilters);
        adminView.showReclamations(filtered);
    }
};

function viewDetails(id) {
    const rec = adminModel.reclamations.find(r => r.id === id);
    if (rec) adminView.showModal(rec);
}

// INIT
document.addEventListener('DOMContentLoaded', () => {
    adminController.init();
    console.log('✅ BackOffice ImpactAble chargé!');
});
    </script>
</body>
</html>