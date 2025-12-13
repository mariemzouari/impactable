<?php require_once __DIR__ . '/../templates/header.php'; ?>

<style>
/* ==================== CANDIDATURES OFFRE STYLES ==================== */
:root {
    /* Même palette de couleurs */
    --brown: #4b2e16;
    --copper: #b47b47;
    --moss: #5e6d3b;
    --sage: #a9b97d;
    --sand: #f4ecdd;
    --white: #fffaf5;
    --light-sage: #e1e8c9;
    --dark-green: #3a4a2a;
    --muted: #6b4b44;
    
    /* UI variables */
    --radius: 16px;
    --radius-sm: 10px;
    --shadow: 0 8px 22px rgba(75,46,22,0.08);
    --shadow-lg: 0 12px 30px rgba(75,46,22,0.12);
}

.candidatures-container {
    background: var(--sand);
    min-height: 100vh;
    padding: 2rem 0;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid var(--copper);
}

.page-title {
    color: var(--brown);
    font-weight: 700;
    margin: 0;
}

.page-subtitle {
    color: var(--muted);
    font-size: 1.1rem;
    margin-top: 0.5rem;
}

.offre-info {
    background: var(--white);
    border-radius: var(--radius);
    padding: 1.5rem;
    margin-bottom: 2rem;
    box-shadow: var(--shadow);
    border-left: 4px solid var(--copper);
}

.offre-info h5 {
    color: var(--brown);
    margin-bottom: 1rem;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
}

.info-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--brown);
}

.info-item i {
    color: var(--copper);
    width: 20px;
}

/* Cartes de candidatures */
.candidature-card {
    background: var(--white);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    border: 1px solid rgba(75,46,22,0.05);
    transition: all 0.3s ease;
}

.candidature-card:hover {
    box-shadow: var(--shadow-lg);
    transform: translateY(-2px);
}

.candidature-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid rgba(75,46,22,0.1);
}

.candidat-info h5 {
    color: var(--brown);
    margin: 0 0 0.25rem 0;
}

.candidat-info .email {
    color: var(--muted);
    font-size: 0.9rem;
}

.candidature-status {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.status-badge {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    border: 1px solid transparent;
}

.status-en_attente { 
    background: rgba(255, 243, 205, 0.8);
    color: var(--brown);
    border-color: rgba(180, 123, 71, 0.3);
}
.status-en_revue { 
    background: rgba(169, 185, 125, 0.3);
    color: var(--dark-green);
    border-color: rgba(94, 109, 59, 0.3);
}
.status-entretien { 
    background: rgba(180, 123, 71, 0.2);
    color: var(--brown);
    border-color: rgba(180, 123, 71, 0.3);
}
.status-retenu { 
    background: rgba(94, 109, 59, 0.2);
    color: var(--dark-green);
    border-color: rgba(94, 109, 59, 0.3);
}
.status-refuse { 
    background: rgba(107, 75, 68, 0.2);
    color: var(--muted);
    border-color: rgba(107, 75, 68, 0.3);
}

.candidature-details {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin-bottom: 1rem;
}

.detail-item {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.detail-label {
    font-size: 0.8rem;
    color: var(--muted);
    font-weight: 600;
    text-transform: uppercase;
}

.detail-value {
    color: var(--brown);
    font-weight: 500;
}

.lettre-motivation {
    background: rgba(169,185,125,0.05);
    padding: 1rem;
    border-radius: var(--radius-sm);
    border-left: 3px solid var(--sage);
    margin-top: 1rem;
}

.lettre-motivation .detail-label {
    margin-bottom: 0.5rem;
}

.lettre-content {
    color: var(--brown);
    line-height: 1.5;
    max-height: 100px;
    overflow-y: auto;
}

.candidature-actions {
    display: flex;
    gap: 0.75rem;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid rgba(75,46,22,0.1);
}

.btn-small {
    padding: 0.5rem 1rem;
    border-radius: var(--radius-sm);
    font-size: 0.8rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-primary {
    background: var(--sage);
    color: var(--brown);
}

.btn-primary:hover {
    background: var(--moss);
    color: var(--white);
    transform: translateY(-1px);
}

.btn-secondary {
    background: transparent;
    border: 1px solid var(--brown);
    color: var(--brown);
}

.btn-secondary:hover {
    background: var(--brown);
    color: var(--white);
}

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

/* Responsive */
@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        gap: 1rem;
    }
    
    .candidature-header {
        flex-direction: column;
        gap: 1rem;
    }
    
    .candidature-details {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .candidature-actions {
        flex-wrap: wrap;
    }
}
</style>

<div class="candidatures-container">
    <div class="container-fluid">
        <!-- En-tête -->
        <div class="page-header">
            <div>
                <h1 class="page-title">
                    <i class="fas fa-users me-2"></i>
                    Candidatures pour l'offre
                </h1>
                <div class="page-subtitle">
                    <strong><?= htmlspecialchars($offre['titre']) ?></strong> - 
                    <?= count($candidatures) ?> candidature(s) reçue(s)
                </div>
            </div>
            <a href="index.php?action=admin-gestion-offres" class="btn-secondary btn-small">
                <i class="fas fa-arrow-left me-2"></i>Retour aux offres
            </a>
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

        <!-- Informations de l'offre -->
        <div class="offre-info">
            <h5><i class="fas fa-info-circle me-2"></i>Informations de l'offre</h5>
            <div class="info-grid">
                <div class="info-item">
                    <i class="fas fa-user"></i>
                    <span><strong>Créateur:</strong> <?= htmlspecialchars($createur['prenom'] . ' ' . $createur['nom']) ?></span>
                </div>
                <div class="info-item">
                    <i class="fas fa-tag"></i>
                    <span><strong>Type:</strong> <?= $offre['type_offre'] ?></span>
                </div>
                <div class="info-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <span><strong>Lieu:</strong> <?= htmlspecialchars($offre['lieu'] ?? 'Non spécifié') ?></span>
                </div>
                <div class="info-item">
                    <i class="fas fa-calendar"></i>
                    <span><strong>Expire le:</strong> <?= $offre['date_expiration'] ? date('d/m/Y', strtotime($offre['date_expiration'])) : 'Non définie' ?></span>
                </div>
            </div>
        </div>

        <!-- Liste des candidatures -->
        <?php if (!empty($candidatures)): ?>
            <div class="candidatures-list">
                <?php foreach ($candidatures as $candidature): ?>
                    <?php 
                    $candidat = $this->utilisateurManager->getById($candidature['Id_utilisateur']);
                    ?>
                    <div class="candidature-card">
                        <div class="candidature-header">
                            <div class="candidat-info">
                                <h5><?= htmlspecialchars($candidat['prenom'] . ' ' . $candidat['nom']) ?></h5>
                                <div class="email"><?= htmlspecialchars($candidat['email']) ?></div>
                                <?php if ($candidat['numero_tel']): ?>
                                    <div class="text-muted small">
                                        <i class="fas fa-phone"></i> <?= htmlspecialchars($candidat['numero_tel']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="candidature-status">
                                <span class="status-badge status-<?= $candidature['status'] ?>">
                                    <?php
                                    $statusLabels = [
                                        'en_attente' => 'En attente',
                                        'en_revue' => 'En revue',
                                        'entretien' => 'Entretien',
                                        'retenu' => 'Retenu',
                                        'refuse' => 'Refusé'
                                    ];
                                    echo $statusLabels[$candidature['status']] ?? $candidature['status'];
                                    ?>
                                </span>
                                <div class="text-muted small">
                                    <i class="fas fa-clock"></i> 
                                    <?= date('d/m/Y H:i', strtotime($candidature['date_candidature'])) ?>
                                </div>
                            </div>
                        </div>

                        <div class="candidature-details">
                            <div class="detail-item">
                                <span class="detail-label">CV</span>
                                <span class="detail-value">
                                    <?php if (!empty($candidature['cv'])): ?>
                                        <a href="<?= htmlspecialchars($candidature['cv']) ?>" target="_blank" class="text-primary">
                                            <i class="fas fa-external-link-alt"></i> Voir le CV
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">Non fourni</span>
                                    <?php endif; ?>
                                </span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">LinkedIn</span>
                                <span class="detail-value">
                                    <?php if (!empty($candidature['linkedin'])): ?>
                                        <a href="<?= htmlspecialchars($candidature['linkedin']) ?>" target="_blank" class="text-primary">
                                            <i class="fab fa-linkedin"></i> Voir le profil
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">Non fourni</span>
                                    <?php endif; ?>
                                </span>
                            </div>
                        </div>

                        <?php if (!empty($candidature['lettre_motivation'])): ?>
                            <div class="lettre-motivation">
                                <span class="detail-label">Lettre de motivation</span>
                                <div class="lettre-content">
                                    <?= nl2br(htmlspecialchars($candidature['lettre_motivation'])) ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($candidature['notes'])): ?>
                            <div class="detail-item">
                                <span class="detail-label">Notes</span>
                                <span class="detail-value"><?= htmlspecialchars($candidature['notes']) ?></span>
                            </div>
                        <?php endif; ?>

                        <div class="candidature-actions">
                            <a href="index.php?action=admin-voir-candidature&id=<?= $candidature['Id_candidature'] ?>" 
                               class="btn-primary btn-small">
                                <i class="fas fa-eye"></i> Voir détails
                            </a>
                            <a href="index.php?action=admin-modifier-candidature&id=<?= $candidature['Id_candidature'] ?>" 
                               class="btn-secondary btn-small">
                                <i class="fas fa-edit"></i> Modifier
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-users"></i>
                <h4>Aucune candidature</h4>
                <p>Aucune candidature n'a été reçue pour cette offre pour le moment.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>