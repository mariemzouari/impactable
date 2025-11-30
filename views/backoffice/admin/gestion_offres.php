<?php require_once __DIR__ . '/../templates/header.php'; ?>

<style>
/* ==================== GESTION DES OFFRES STYLES ==================== */
:root {
    /* Color palette - Identique à votre backoffice */
    --brown: #4b2e16;
    --copper: #b47b47;
    --moss: #5e6d3b;
    --sage: #a9b97d;
    --sand: #f4ecdd;
    --white: #fffaf5;
    --light-sage: #e1e8c9;
    --dark-green: #3a4a2a;
    
    /* Derived shades */
    --brown-600: rgba(75,46,22,0.9);
    --brown-300: rgba(75,46,22,0.2);
    --muted: #6b4b44;
    
    /* UI variables */
    --card-bg: #ffffff;
    --radius: 16px;
    --radius-sm: 10px;
    --shadow: 0 8px 22px rgba(75,46,22,0.08);
    --shadow-lg: 0 12px 30px rgba(75,46,22,0.12);
    --maxw: 1400px;
    --focus: 0 0 0 3px rgba(180,123,71,0.18);
    --input-height: 48px;
    --gap: 1rem;
    
    /* Transitions */
    --ease-s: 200ms cubic-bezier(.2,.9,.2,1);
    --ease-l: 350ms cubic-bezier(.2,.9,.2,1);
    
    /* Typography */
    --font-sans: "Inter", system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
}

/* Styles spécifiques à la gestion des offres */
.gestion-container {
    background: var(--sand);
    min-height: 100vh;
    padding: 2rem 0;
}

.page-title {
    color: var(--brown);
    font-weight: 700;
    margin-bottom: 2rem;
    position: relative;
    padding-bottom: 1rem;
}

.page-title::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 80px;
    height: 4px;
    background: var(--copper);
    border-radius: 2px;
}

/* Cartes améliorées */
.gestion-card {
    background: var(--white);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    transition: all var(--ease-l);
    border: 1px solid rgba(75,46,22,0.05);
    overflow: hidden;
    margin-bottom: 2rem;
}

.gestion-card:hover {
    box-shadow: var(--shadow-lg);
    transform: translateY(-2px);
}

.gestion-card .card-header {
    background: linear-gradient(135deg, var(--white) 0%, var(--light-sage) 100%);
    padding: 1.5rem 2rem;
    border-bottom: 1px solid rgba(75,46,22,0.08);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.gestion-card .card-header h6 {
    color: var(--brown);
    font-weight: 700;
    font-size: 1.2rem;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

/* Badges avec la palette existante */
.badge {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    border: 1px solid transparent;
}

.badge-primary {
    background: var(--copper);
    color: var(--white);
}

.badge-info {
    background: var(--sage);
    color: var(--brown);
}

.badge-success {
    background: var(--moss);
    color: var(--white);
}

.badge-secondary {
    background: var(--light-sage);
    color: var(--dark-green);
}

.badge-danger {
    background: var(--muted);
    color: var(--white);
}

.badge-warning {
    background: var(--copper);
    color: var(--white);
}

/* Tableau amélioré */
.table-container {
    padding: 0;
}

.admin-table {
    width: 100%;
    border-collapse: collapse;
    margin: 0;
}

.admin-table thead {
    background: linear-gradient(135deg, var(--light-sage) 0%, rgba(169,185,125,0.3) 100%);
}
/* Style pour le lien candidatures */
.candidature-link {
    text-decoration: none;
    transition: all var(--ease-s);
}

.candidature-link:hover {
    transform: scale(1.05);
}

.candidature-link .badge {
    transition: all var(--ease-s);
}

.candidature-link:hover .badge-warning {
    background: var(--brown);
    color: var(--white);
}

.candidature-link:hover .badge-secondary {
    background: var(--copper);
    color: var(--white);
}

.admin-table th {
    padding: 1rem 1.5rem;
    color: var(--dark-green);
    font-weight: 700;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 2px solid var(--sage);
    text-align: left;
}

.admin-table td {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid rgba(75,46,22,0.08);
    color: var(--brown);
    font-size: 0.95rem;
    transition: all var(--ease-s);
}

.admin-table tbody tr {
    transition: all var(--ease-s);
    background: var(--white);
}

.admin-table tbody tr:hover {
    background: rgba(169,185,125,0.08);
    transform: translateX(4px);
}

.admin-table tbody tr:last-child td {
    border-bottom: none;
}

/* Actions */
.actions-group {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.action-btn {
    width: 36px;
    height: 36px;
    border-radius: var(--radius-sm);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: all var(--ease-s);
    border: 1px solid transparent;
    font-size: 0.9rem;
}

.action-btn.view {
    background: rgba(169,185,125,0.2);
    color: var(--moss);
    border-color: rgba(94,109,59,0.2);
}

.action-btn.edit {
    background: rgba(180,123,71,0.2);
    color: var(--copper);
    border-color: rgba(180,123,71,0.3);
}

.action-btn.delete {
    background: rgba(107,75,68,0.2);
    color: var(--muted);
    border-color: rgba(107,75,68,0.3);
}

.action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(75,46,22,0.15);
}

.action-btn.view:hover {
    background: var(--moss);
    color: var(--white);
}

.action-btn.edit:hover {
    background: var(--copper);
    color: var(--white);
}

.action-btn.delete:hover {
    background: var(--muted);
    color: var(--white);
}

/* Alertes */
.alert {
    border: none;
    border-radius: var(--radius-sm);
    padding: 1rem 1.5rem;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-weight: 500;
}

.alert-success {
    background: rgba(94,109,59,0.1);
    color: var(--dark-green);
    border-left: 4px solid var(--moss);
}

.alert-danger {
    background: rgba(107,75,68,0.1);
    color: var(--muted);
    border-left: 4px solid var(--muted);
}

.alert-info {
    background: rgba(169,185,125,0.1);
    color: var(--dark-green);
    border-left: 4px solid var(--sage);
}

.alert .close {
    color: inherit;
    opacity: 0.7;
    margin-left: auto;
}

.alert .close:hover {
    opacity: 1;
}

/* État vide */
.empty-state {
    text-align: center;
    padding: 3rem 2rem;
    color: var(--muted);
}

.empty-state i {
    font-size: 3rem;
    color: var(--light-sage);
    margin-bottom: 1rem;
}

.empty-state h4 {
    color: var(--brown);
    margin-bottom: 0.5rem;
}

/* Statut expiré */
.expired-badge {
    animation: pulse-expired 2s infinite;
}

@keyframes pulse-expired {
    0% {
        box-shadow: 0 0 0 0 rgba(107,75,68,0.4);
    }
    70% {
        box-shadow: 0 0 0 6px rgba(107,75,68,0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(107,75,68,0);
    }
}

/* Responsive */
@media (max-width: 768px) {
    .gestion-container {
        padding: 1rem 0;
    }
    
    .table-container {
        overflow-x: auto;
    }
    
    .admin-table {
        min-width: 800px;
    }
    
    .admin-table th,
    .admin-table td {
        padding: 0.875rem 1rem;
        font-size: 0.9rem;
    }
    
    .actions-group {
        flex-direction: column;
        gap: 0.25rem;
    }
    
    .action-btn {
        width: 32px;
        height: 32px;
        font-size: 0.8rem;
    }
    
    .page-title {
        font-size: 1.5rem;
    }
}

@media (max-width: 480px) {
    .gestion-card .card-header {
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start;
    }
    
    .badge {
        font-size: 0.75rem;
        padding: 0.4rem 0.8rem;
    }
}

/* Amélioration de l'accessibilité */
.action-btn:focus,
.badge:focus {
    outline: 2px solid var(--copper);
    outline-offset: 2px;
}

/* Effets de brillance */
.card-shine {
    position: relative;
    overflow: hidden;
}

.card-shine::after {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(
        to bottom right,
        rgba(255,255,255,0) 0%,
        rgba(255,255,255,0.1) 50%,
        rgba(255,255,255,0) 100%
    );
    transform: rotate(30deg);
    transition: all 0.6s;
}

.card-shine:hover::after {
    left: 100%;
}
</style>

<div class="gestion-container">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h1 class="page-title">Gestion des Offres</h1>
            </div>
        </div>

        <!-- Messages -->
        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i>
                <?= $success ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i>
                <?= $error ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

<div class="gestion-card card-shine">
    <div class="card-header">
        <h6><i class="fas fa-list-alt"></i>Liste des Offres</h6>
        <span class="badge badge-primary">
            <i class="fas fa-briefcase"></i>
            <?= count($offres) ?> offre(s)
        </span>
    </div>
    <div class="card-body p-0">
        <?php if (!empty($offres)): ?>
            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Titre</th>
                            <th>Type</th>
                            <th>Candidatures</th>
                            <th>Créateur</th>
                            <th>Date Publication</th>
                            <th>Date Expiration</th>
                            <th>Accessible</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($offres as $offre): ?>
                            <tr>
                                <td>
                                    <span class="text-muted">#<?= $offre['Id_offre'] ?></span>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($offre['titre']) ?></strong>
                                </td>
                                <td>
                                    <span class="badge badge-info">
                                        <i class="fas fa-tag"></i>
                                        <?= $offre['type_offre'] ?>
                                    </span>
                                </td>
                                <td>
                                    <?php 
                                    $nbCandidatures = $this->candidatureManager->getCountByOffre($offre['Id_offre']);
                                    ?>
                                    <a href="index.php?action=admin-candidatures-offre&id=<?= $offre['Id_offre'] ?>" 
                                       class="candidature-link" 
                                       title="Voir les <?= $nbCandidatures ?> candidature(s)">
                                        <span class="badge <?= $nbCandidatures > 0 ? 'badge-warning' : 'badge-secondary' ?>">
                                            <i class="fas fa-users"></i>
                                            <?= $nbCandidatures ?>
                                        </span>
                                    </a>
                                </td>
                                <td>
                                    <?php 
                                        $createur = $this->utilisateurManager->getById($offre['Id_utilisateur']);
                                        echo htmlspecialchars($createur['prenom'] . ' ' . $createur['nom']);
                                    ?>
                                </td>
                                <td>
                                    <i class="fas fa-calendar text-muted mr-1"></i>
                                    <?= date('d/m/Y', strtotime($offre['date_publication'])) ?>
                                </td>
                                <td>
                                    <?php if ($offre['date_expiration']): ?>
                                        <i class="fas fa-clock text-muted mr-1"></i>
                                        <?= date('d/m/Y', strtotime($offre['date_expiration'])) ?>
                                        <?php if (strtotime($offre['date_expiration']) < time()): ?>
                                            <span class="badge badge-danger ml-1 expired-badge">
                                                <i class="fas fa-exclamation-triangle"></i>Expiré
                                            </span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-muted">Non définie</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($offre['disability_friendly']): ?>
                                        <span class="badge badge-success">
                                            <i class="fas fa-check"></i>Oui
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">
                                            <i class="fas fa-times"></i>Non
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="actions-group">
                                        <a href="index.php?action=admin-voir-offre&id=<?= $offre['Id_offre'] ?>" 
                                           class="action-btn view" 
                                           title="Voir les détails">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="index.php?action=admin-modifier-offre&id=<?= $offre['Id_offre'] ?>" 
                                           class="action-btn edit" 
                                           title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="index.php?action=admin-candidatures-offre&id=<?= $offre['Id_offre'] ?>" 
                                           class="action-btn view" 
                                           title="Voir les candidatures">
                                            <i class="fas fa-users"></i>
                                        </a>
                                        <a href="index.php?action=admin-supprimer-offre&id=<?= $offre['Id_offre'] ?>" 
                                           class="action-btn delete" 
                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette offre ?')"
                                           title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h4>Aucune offre trouvée</h4>
                <p>Il n'y a actuellement aucune offre à afficher.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php require_once __DIR__ . '/../templates/footer.php'; ?>