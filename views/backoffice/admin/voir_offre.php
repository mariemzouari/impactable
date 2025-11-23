<?php require_once __DIR__ . '/../templates/header.php'; ?>

<style>
/* ==================== DETAIL OFFRE STYLES ==================== */
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

/* Styles spécifiques à la page détail offre */
.detail-container {
    background: var(--sand);
    min-height: 100vh;
    padding: 2rem 0;
}

/* Cartes améliorées */
.detail-card {
    background: var(--white);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    transition: all var(--ease-l);
    border: 1px solid rgba(75,46,22,0.05);
    overflow: hidden;
    margin-bottom: 2rem;
}

.detail-card:hover {
    box-shadow: var(--shadow-lg);
    transform: translateY(-2px);
}

.detail-card .card-header {
    background: linear-gradient(135deg, var(--white) 0%, var(--light-sage) 100%);
    padding: 1.5rem 2rem;
    border-bottom: 1px solid rgba(75,46,22,0.08);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.detail-card .card-header h6 {
    color: var(--brown);
    font-weight: 700;
    font-size: 1.1rem;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.detail-card .card-body {
    padding: 2rem;
    background: var(--white);
}

/* En-tête de l'offre */
.offer-header {
    background: linear-gradient(135deg, var(--moss) 0%, var(--dark-green) 100%);
    color: var(--white);
    padding: 2.5rem 2rem;
    border-radius: var(--radius);
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
}

.offer-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="none"><path d="M0,0 L100,0 L100,100 Z" fill="rgba(255,255,255,0.1)"/></svg>');
    background-size: cover;
}

.offer-header h4 {
    font-weight: 700;
    margin-bottom: 1rem;
    position: relative;
    z-index: 1;
    font-size: 1.8rem;
}

.offer-meta {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    position: relative;
    z-index: 1;
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

.status-badge {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
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

.badge-info {
    background: var(--sage);
    color: var(--brown);
}

.badge-secondary {
    background: var(--light-sage);
    color: var(--dark-green);
}

.badge-success {
    background: var(--moss);
    color: var(--white);
}

.badge-warning {
    background: var(--copper);
    color: var(--white);
}

.badge-danger {
    background: var(--muted);
    color: var(--white);
}

.badge-primary {
    background: var(--copper);
    color: var(--white);
}

.badge-light {
    background: rgba(255,255,255,0.9);
    color: var(--brown);
    border: 1px solid rgba(75,46,22,0.1);
}

/* Sections d'information */
.info-section {
    margin-bottom: 2rem;
    padding: 1.5rem;
    background: var(--white);
    border-radius: var(--radius-sm);
    border: 1px solid rgba(75,46,22,0.05);
    transition: all var(--ease-s);
}

.info-section:hover {
    border-color: var(--copper);
    box-shadow: 0 4px 12px rgba(180,123,71,0.1);
}

.info-section h6 {
    color: var(--brown);
    margin-bottom: 1rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 1rem;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid var(--copper);
}

.info-section h6 i {
    color: var(--copper);
    font-size: 1.1rem;
}

.info-list {
    margin: 0;
    padding: 0;
}

.info-list li {
    margin-bottom: 0.75rem;
    display: flex;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid rgba(75,46,22,0.05);
}

.info-list li:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.info-list strong {
    min-width: 180px;
    color: var(--brown);
    font-weight: 600;
    font-size: 0.9rem;
}

/* Contenu texte */
.text-content {
    line-height: 1.7;
    color: var(--brown);
    font-size: 0.95rem;
}

.text-content p {
    margin-bottom: 1rem;
    text-align: justify;
}

/* Section créateur */
.creator-card {
    text-align: center;
    transition: all var(--ease-l);
    background: var(--white);
    border: 1px solid rgba(75,46,22,0.05);
}

.creator-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-lg);
}

.creator-avatar {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--copper) 0%, var(--brown) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    color: var(--white);
    font-size: 2.5rem;
    box-shadow: 0 8px 20px rgba(180,123,71,0.3);
    border: 4px solid var(--white);
}

.creator-card h5 {
    font-weight: 700;
    color: var(--brown);
    margin-bottom: 0.5rem;
}

.creator-card .text-muted {
    color: var(--muted) !important;
}

/* Boutons d'action */
.action-buttons {
    padding: 0 !important;
}

.action-buttons .btn {
    margin: 0;
    padding: 1rem 1.5rem;
    border: none;
    border-radius: 0;
    font-weight: 600;
    transition: all var(--ease-s);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    position: relative;
    overflow: hidden;
}

.action-buttons .btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    transition: left 0.5s;
}

.action-buttons .btn:hover::before {
    left: 100%;
}

.action-buttons .btn:first-child {
    border-radius: var(--radius) var(--radius) 0 0;
}

.action-buttons .btn:last-child {
    border-radius: 0 0 var(--radius) var(--radius);
}

.action-buttons .btn-warning {
    background: var(--copper);
    color: var(--white);
}

.action-buttons .btn-secondary {
    background: var(--light-sage);
    color: var(--dark-green);
}

.action-buttons .btn-danger {
    background: var(--muted);
    color: var(--white);
}

.action-buttons .btn:hover {
    transform: none;
    filter: brightness(1.1);
}

/* Badges de handicap */
.handicap-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    margin-top: 1rem;
}

.handicap-badge {
    background: linear-gradient(135deg, var(--sage) 0%, var(--moss) 100%);
    color: var(--white);
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    box-shadow: 0 4px 8px rgba(169,185,125,0.3);
    transition: all var(--ease-s);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.handicap-badge:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(169,185,125,0.4);
}

/* Animation d'expiration */
.expired-badge {
    animation: pulse-glow 2s infinite;
    box-shadow: 0 0 0 0 rgba(107,75,68,0.7);
}

@keyframes pulse-glow {
    0% {
        box-shadow: 0 0 0 0 rgba(107,75,68,0.7);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(107,75,68,0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(107,75,68,0);
    }
}

/* Séparateurs */
.section-divider {
    border: 0;
    height: 2px;
    background: linear-gradient(90deg, transparent, var(--copper), transparent);
    margin: 2.5rem 0;
    opacity: 0.6;
}

/* Responsive */
@media (max-width: 768px) {
    .detail-container {
        padding: 1rem 0;
    }
    
    .info-list li {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    
    .info-list strong {
        min-width: auto;
        margin-bottom: 0;
    }
    
    .action-buttons .btn {
        padding: 0.875rem 1.25rem;
        font-size: 0.9rem;
    }
    
    .creator-avatar {
        width: 80px;
        height: 80px;
        font-size: 2rem;
    }
    
    .offer-header {
        padding: 1.5rem;
    }
    
    .offer-header h4 {
        font-size: 1.5rem;
    }
    
    .card-body {
        padding: 1.5rem;
    }
    
    .offer-meta {
        gap: 0.5rem;
    }
    
    .offer-meta .badge {
        font-size: 0.75rem;
        padding: 0.4rem 0.8rem;
    }
}

/* Amélioration de l'accessibilité */
.btn:focus,
.card:focus {
    outline: 2px solid var(--copper);
    outline-offset: 2px;
}

/* Icônes */
.icon-wrapper {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(180,123,71,0.1);
    margin-right: 10px;
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

<div class="detail-container">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-8">
                <div class="detail-card card-shine">
                    <div class="card-header">
                        <h6><i class="fas fa-info-circle"></i>Informations de l'Offre</h6>
                        <div>
                            <span class="badge badge-<?= $offre['disability_friendly'] ? 'success' : 'secondary' ?>">
                                <i class="fas fa-<?= $offre['disability_friendly'] ? 'check-circle' : 'times-circle' ?> mr-1"></i>
                                <?= $offre['disability_friendly'] ? 'Accessible' : 'Non accessible' ?>
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- En-tête amélioré de l'offre -->
                        <div class="offer-header">
                            <h4><?= htmlspecialchars($offre['titre']) ?></h4>
                            <div class="offer-meta">
                                <span class="badge badge-light">
                                    <i class="fas fa-tag mr-1"></i><?= ucfirst($offre['type_offre']) ?>
                                </span>
                                <span class="badge badge-light">
                                    <i class="fas fa-map-marker-alt mr-1"></i><?= htmlspecialchars($offre['lieu']) ?>
                                </span>
                                <span class="badge badge-light">
                                    <i class="fas fa-clock mr-1"></i><?= $offre['horaire'] ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-section card-shine">
                                    <h6><i class="fas fa-align-left"></i>Description</h6>
                                    <div class="text-content">
                                        <p><?= nl2br(htmlspecialchars($offre['description'])) ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-section card-shine">
                                    <h6><i class="fas fa-heart"></i>Impact Social</h6>
                                    <div class="text-content">
                                        <p><?= nl2br(htmlspecialchars($offre['impact_sociale'])) ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="section-divider">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-section card-shine">
                                    <h6><i class="fas fa-info-circle"></i>Informations Générales</h6>
                                    <ul class="list-unstyled info-list">
                                        <li>
                                            <strong>Type d'offre:</strong> 
                                            <span class="badge badge-info"><?= $offre['type_offre'] ?></span>
                                        </li>
                                        <li>
                                            <strong>Mode:</strong> 
                                            <span class="badge badge-secondary"><?= $offre['mode'] ?></span>
                                        </li>
                                        <li>
                                            <strong>Horaire:</strong> 
                                            <span class="badge badge-secondary"><?= $offre['horaire'] ?></span>
                                        </li>
                                        <li>
                                            <strong>Lieu:</strong> 
                                            <span><?= htmlspecialchars($offre['lieu']) ?></span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-section card-shine">
                                    <h6><i class="fas fa-calendar-alt"></i>Dates</h6>
                                    <ul class="list-unstyled info-list">
                                        <li>
                                            <strong>Publication:</strong> 
                                            <span><?= date('d/m/Y H:i', strtotime($offre['date_publication'])) ?></span>
                                        </li>
                                        <li>
                                            <strong>Expiration:</strong> 
                                            <?php if ($offre['date_expiration']): ?>
                                                <span><?= date('d/m/Y', strtotime($offre['date_expiration'])) ?></span>
                                                <?php if (strtotime($offre['date_expiration']) < time()): ?>
                                                    <span class="badge badge-danger ml-2 expired-badge">
                                                        <i class="fas fa-exclamation-triangle mr-1"></i>Expiré
                                                    </span>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-muted">Non définie</span>
                                            <?php endif; ?>
                                        </li>
                                        <?php if ($offre['date_modification']): ?>
                                            <li>
                                                <strong>Dernière modification:</strong> 
                                                <span><?= date('d/m/Y H:i', strtotime($offre['date_modification'])) ?></span>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <?php if (!empty($offre['type_handicap']) && $offre['type_handicap'] !== 'tous'): ?>
                            <hr class="section-divider">
                            <div class="info-section card-shine">
                                <h6><i class="fas fa-universal-access"></i>Types de handicap ciblés</h6>
                                <div class="handicap-badges">
                                    <?php 
                                    $types_handicap = explode(',', $offre['type_handicap']);
                                    foreach ($types_handicap as $type): 
                                    ?>
                                        <span class="handicap-badge">
                                            <i class="fas fa-wheelchair mr-1"></i><?= trim($type) ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Informations du créateur -->
                <div class="detail-card creator-card card-shine">
                    <div class="card-header">
                        <h6><i class="fas fa-user-tie"></i>Créateur de l'Offre</h6>
                    </div>
                    <div class="card-body">
                        <div class="creator-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <h5><?= htmlspecialchars($createur['prenom'] . ' ' . $createur['nom']) ?></h5>
                        <p class="text-muted mb-2">
                            <i class="fas fa-envelope mr-2"></i><?= htmlspecialchars($createur['email']) ?>
                        </p>
                        <p class="small mb-3">
                            <strong>Rôle:</strong> 
                            <span class="badge badge-<?= $createur['role'] === 'admin' ? 'warning' : 'info' ?> ml-2">
                                <?= ucfirst($createur['role']) ?>
                            </span>
                        </p>
                        <p class="small text-muted mb-0">
                            <i class="fas fa-calendar-day mr-2"></i>
                            Inscrit le: <?= date('d/m/Y', strtotime($createur['date_inscription'])) ?>
                        </p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="detail-card card-shine">
                    <div class="card-header">
                        <h6><i class="fas fa-cogs"></i>Actions</h6>
                    </div>
                    <div class="card-body p-0 action-buttons">
                        <a href="index.php?action=admin-modifier-offre&id=<?= $offre['Id_offre'] ?>" 
                           class="btn btn-warning">
                            <i class="fas fa-edit"></i> Modifier l'Offre
                        </a>
                        <a href="index.php?action=admin-gestion-offres" 
                           class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour à la liste
                        </a>
                        <a href="index.php?action=admin-supprimer-offre&id=<?= $offre['Id_offre'] ?>" 
                           class="btn btn-danger"
                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette offre ? Cette action est irréversible.')">
                            <i class="fas fa-trash"></i> Supprimer l'Offre
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>