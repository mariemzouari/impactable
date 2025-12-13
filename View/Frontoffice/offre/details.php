<?php 
$title = $offre['titre'] . ' - Détails de l\'offre | ' . Config::SITE_NAME;
require_once __DIR__ . '/../templates/header.php'; 
?>

<style>
.offre-details {
    font-family: var(--font-sans);
    background: var(--white);
    color: var(--brown);
}

.hero-section {
    background: linear-gradient(135deg, var(--sand) 0%, var(--light-sage) 100%);
    color: var(--brown);
    border-radius: var(--radius);
    margin-bottom: 2rem;
    border: 2px solid var(--copper);
    position: relative;
    overflow: hidden;
}

.hero-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, rgba(180, 123, 71, 0.05) 0%, transparent 50%);
    pointer-events: none;
}

.hero-title {
    color: var(--brown) !important;
    font-weight: 700;
    font-size: 2.2rem;
    margin-bottom: 1rem;
}

.hero-date {
    color: var(--brown) !important;
    font-weight: 600;
    font-size: 1rem;
}

.hero-date-warning {
    color: var(--brown) !important;
    font-weight: 700;
    font-size: 1rem;
}

.hero-badge {
    font-size: 0.9em;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    background: var(--copper);
    color: var(--white);
    font-weight: 500;
    border: 1px solid var(--brown);
}

.card {
    border: none;
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    transition: all var(--ease-l);
    margin-bottom: 1.5rem;
    background: var(--card-bg);
}

.card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
}

.card-header {
    background: linear-gradient(135deg, var(--sand) 0%, var(--light-sage) 100%);
    border-bottom: 2px solid var(--sage);
    border-radius: var(--radius) var(--radius) 0 0 !important;
    padding: 1.25rem 1.5rem;
    font-weight: 600;
    color: var(--brown);
    border: none;
}

.info-item {
    display: flex;
    align-items: center;
    margin-bottom: 0.75rem;
    padding: 0.5rem 0;
    color: var(--brown);
}

.info-item i {
    width: 20px;
    margin-right: 12px;
    color: var(--copper);
}

.badge-custom {
    font-size: 0.75em;
    padding: 0.4em 0.8em;
    border-radius: var(--radius-sm);
    margin: 0.2em;
    font-weight: 500;
}

.badge-primary {
    background: var(--copper);
    color: var(--white);
}

.badge-success {
    background: var(--moss);
    color: var(--white);
}

.badge-info {
    background: var(--sage);
    color: var(--brown);
}

.badge-secondary {
    background: var(--sand);
    color: var(--brown);
}

.badge-dark {
    background: var(--brown);
    color: var(--white);
}

.company-avatar {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, var(--copper) 0%, var(--brown) 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    color: var(--white);
    font-size: 2rem;
    border: 3px solid var(--sage);
}

.action-btn {
    border-radius: var(--radius-sm);
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    transition: all var(--ease-s);
    border: none;
    margin-bottom: 1rem;
    height: var(--input-height);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.action-btn-primary {
    background: linear-gradient(135deg, var(--copper) 0%, var(--brown) 100%);
    color: var(--white);
}

.action-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(180, 123, 71, 0.3);
    background: linear-gradient(135deg, var(--brown) 0%, var(--copper) 100%);
}

.btn-outline-primary {
    border: 2px solid var(--copper);
    color: var(--copper);
    background: transparent;
}

.btn-outline-primary:hover {
    background: var(--copper);
    color: var(--white);
    transform: translateY(-2px);
}

.btn-success {
    background: var(--moss);
    border-color: var(--moss);
    color: var(--white);
}

.btn-success:hover {
    background: var(--moss);
    opacity: 0.9;
    transform: translateY(-2px);
}

.share-btn {
    border-radius: var(--radius-sm);
    padding: 0.6rem 1rem;
    font-size: 0.9em;
    transition: all var(--ease-s);
    border: 2px solid var(--sand);
    background: var(--white);
    color: var(--brown);
}

.share-btn:hover {
    transform: translateY(-2px);
    border-color: var(--copper);
    background: var(--sand);
}

.description-text {
    line-height: 1.8;
    color: var(--brown);
    font-size: 1.05em;
}

.accessibility-badge {
    background: linear-gradient(135deg, var(--moss) 0%, var(--sage) 100%);
    color: var(--white);
    padding: 0.5rem 1rem;
    border-radius: 25px;
    font-size: 0.9em;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    border: 2px solid var(--light-sage);
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.stat-item {
    text-align: center;
    padding: 1.5rem 1rem;
    background: var(--sand);
    border-radius: var(--radius-sm);
    border-left: 4px solid var(--copper);
    transition: all var(--ease-s);
}

.stat-item:hover {
    transform: translateY(-3px);
    background: var(--light-sage);
}

.handicap-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-top: 1rem;
}

.breadcrumb-custom {
    background: transparent;
    padding: 0;
    margin-bottom: 2rem;
}

.breadcrumb-custom .breadcrumb-item a {
    color: var(--copper);
    text-decoration: none;
    font-weight: 500;
}

.breadcrumb-custom .breadcrumb-item a:hover {
    color: var(--brown);
    text-decoration: underline;
}

.breadcrumb-custom .breadcrumb-item.active {
    color: var(--muted);
}

.social-stats {
    display: flex;
    justify-content: space-around;
    text-align: center;
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid var(--sand);
}

.stat-number {
    font-size: 1.5em;
    font-weight: bold;
    color: var(--copper);
}

.stat-label {
    font-size: 0.8em;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Type badges */
.type-badge {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.85em;
}

.type-emploi {
    background: var(--copper);
    color: var(--white);
}

.type-stage {
    background: var(--moss);
    color: var(--white);
}

/* Status indicators */
.status-indicator {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    background: var(--light-sage);
    color: var(--moss);
    font-size: 0.9em;
    font-weight: 500;
}

/* Company card enhancements */
.company-highlights {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    margin-top: 1rem;
}

.highlight-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem;
    background: var(--sand);
    border-radius: var(--radius-sm);
    font-size: 0.9em;
}

.highlight-item i {
    color: var(--copper);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .hero-section {
        text-align: center;
        padding: 2rem 1rem !important;
    }
    
    .hero-title {
        font-size: 1.8rem;
    }
    
    .info-item {
        justify-content: center;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .handicap-tags {
        justify-content: center;
    }
    
    .social-stats {
        flex-direction: column;
        gap: 1rem;
    }
    
    .card-header {
        padding: 1rem 1.25rem;
    }
}

/* Focus states for accessibility */
.action-btn:focus,
.share-btn:focus {
    outline: none;
    box-shadow: var(--focus);
}

/* Loading animation for cards */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.card {
    animation: fadeInUp 0.6s ease-out;
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: var(--sand);
}

::-webkit-scrollbar-thumb {
    background: var(--copper);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: var(--brown);
}
</style>

<div class="offre-details">
    <!-- Breadcrumb amélioré -->
    <nav aria-label="breadcrumb" class="breadcrumb-custom">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php?action=offres"><i class="fas fa-arrow-left me-2"></i>Retour aux offres</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($offre['titre']) ?></li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-8">
            <!-- Hero Section -->
            <div class="card hero-section">
                <div class="card-body position-relative">
                    <div class="d-flex justify-content-between align-items-start flex-wrap">
                        <div class="flex-grow-1">
                            <h1 class="hero-title"><?= htmlspecialchars($offre['titre']) ?></h1>
                            <div class="d-flex flex-wrap gap-3 align-items-center mb-3">
                                <span class="hero-badge">
                                    <i class="fas fa-building me-2"></i><?= htmlspecialchars($offre['entreprise_nom'] ?? 'Entreprise') ?>
                                </span>
                                <span class="hero-badge">
                                    <i class="fas fa-map-marker-alt me-2"></i><?= htmlspecialchars($offre['lieu']) ?>
                                </span>
                                <span class="type-badge type-<?= $offre['type_offre'] ?>">
                                    <?= $offre['type_offre'] === 'emploi' ? '📊 Emploi' : '🎓 Stage' ?>
                                </span>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="hero-date mb-2">
                                <small><i class="fas fa-calendar me-1"></i>Publié le <?= date('d/m/Y', strtotime($offre['date_publication'] ?? 'now')) ?></small>
                            </div>
                            <div class="hero-date-warning">
                                <i class="fas fa-clock me-1"></i>
                                <small>Expire le <?= date('d/m/Y', strtotime($offre['date_expiration'])) ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informations clés -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informations du poste</h5>
                </div>
                <div class="card-body">
                    <div class="stats-grid">
                        <div class="stat-item">
                            <div class="stat-number">
                                <i class="fas fa-laptop-house"></i>
                            </div>
                            <div class="stat-label">Mode de travail</div>
                            <div class="fw-bold mt-1">
                                <?= $offre['mode'] === 'presentiel' ? '🏢 Présentiel' : ($offre['mode'] === 'teletravail' ? '🏠 Télétravail' : '🔀 Hybride') ?>
                            </div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="stat-label">Horaire</div>
                            <div class="fw-bold mt-1">
                                <?= $offre['horaire'] === 'temps_plein' ? '⏱️ Temps plein' : '🕒 Temps partiel' ?>
                            </div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <div class="stat-label">Contrat</div>
                            <div class="fw-bold mt-1">
                                <?= $offre['type_offre'] === 'emploi' ? '📝 CDI/CDD' : '🎓 Stage' ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>Description du poste</h5>
                </div>
                <div class="card-body">
                    <div class="description-text">
                        <?= nl2br(htmlspecialchars($offre['description'])) ?>
                    </div>
                </div>
            </div>

            <!-- Impact Social -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-heart me-2"></i>Impact Social</h5>
                </div>
                <div class="card-body">
                    <div class="description-text">
                        <?= nl2br(htmlspecialchars($offre['impact_sociale'])) ?>
                    </div>
                </div>
            </div>

            <!-- Accessibilité -->
            <?php if ($offre['disability_friendly']): ?>
            <div class="card" style="border: 2px solid var(--moss);">
                <div class="card-header" style="background: linear-gradient(135deg, var(--moss) 0%, var(--sage) 100%); color: var(--white);">
                    <h5 class="mb-0"><i class="fas fa-universal-access me-2"></i>Accessibilité</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <span class="accessibility-badge">
                            <i class="fas fa-check-circle"></i>
                            Adapté aux personnes en situation de handicap
                        </span>
                    </div>
                    <?php if (!empty($offre['type_handicap']) && $offre['type_handicap'] !== 'tous'): ?>
                        <div>
                            <strong class="mb-2 d-block" style="color: var(--moss);">Types de handicap concernés :</strong>
                            <div class="handicap-tags">
                                <?php
                                $handicaps = explode(',', $offre['type_handicap']);
                                $handicapLabels = [
                                    'moteur' => '♿ Moteur',
                                    'visuel' => '👁️ Visuel', 
                                    'auditif' => '👂 Auditif',
                                    'mental' => '🧠 Mental',
                                    'psychique' => '💭 Psychique'
                                ];
                                $handicapColors = [
                                    'moteur' => 'primary',
                                    'visuel' => 'info',
                                    'auditif' => 'warning', 
                                    'mental' => 'success',
                                    'psychique' => 'secondary'
                                ];
                                foreach ($handicaps as $handicap) {
                                    if (isset($handicapLabels[$handicap])) {
                                        echo '<span class="badge badge-custom badge-' . ($handicapColors[$handicap] ?? 'dark') . '">' . $handicapLabels[$handicap] . '</span>';
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="col-lg-4">
            <!-- Actions -->
            <div class="card sticky-top" style="top: 2rem;">
                <div class="card-body">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if ($hasApplied): ?>
                            <button class="btn btn-success w-100 action-btn" disabled>
                                <i class="fas fa-check-circle me-2"></i>Candidature envoyée
                            </button>
                            <div class="status-indicator w-100 justify-content-center">
                                <i class="fas fa-clock"></i>
                                <span>En attente de réponse</span>
                            </div>
                        <?php else: ?>
                            <a href="index.php?action=postuler&id=<?php echo $offre['Id_offre'];?>" 
                               class="btn action-btn action-btn-primary w-100">
                                <i class="fas fa-paper-plane me-2"></i>Postuler maintenant
                            </a>
                        <?php endif; ?>
                        
                        <div class="d-grid gap-2 mt-3">
                            <button class="btn share-btn" onclick="shareOnLinkedIn()">
                                <i class="fab fa-linkedin me-2"></i>Partager sur LinkedIn
                            </button>
                            <button class="btn share-btn" onclick="shareOnTwitter()">
                                <i class="fab fa-twitter me-2"></i>Partager sur Twitter
                            </button>
                            <button class="btn share-btn" onclick="shareOnFacebook()">
                                <i class="fab fa-facebook me-2"></i>Partager sur Facebook
                            </button>
                        </div>
                        

                    <?php else: ?>
                        <div class="text-center">
                            <div class="company-avatar">
                                <i class="fas fa-building"></i>
                            </div>
                            <h6 class="fw-bold"><?= htmlspecialchars($offre['entreprise_nom'] ?? 'Entreprise') ?></h6>
                            <p class="text-muted small mb-4">
                                <i class="fas fa-map-marker-alt me-1"></i><?= htmlspecialchars($offre['lieu']) ?>
                            </p>
                            
                            <p class="mb-3" style="color: var(--muted);">Pour postuler à cette offre, connectez-vous à votre compte.</p>
                            <a href="index.php?action=connexion" class="btn action-btn action-btn-primary w-100 mb-2">
                                <i class="fas fa-sign-in-alt me-2"></i>Se connecter
                            </a>
                            <a href="index.php?action=inscription" class="btn btn-outline-primary w-100">
                                <i class="fas fa-user-plus me-2"></i>Créer un compte
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Entreprise -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-building me-2"></i>À propos de l'entreprise</h5>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <div class="company-avatar">
                            <i class="fas fa-building"></i>
                        </div>
                        <h6 class="fw-bold"><?= htmlspecialchars($offre['entreprise_nom'] ?? 'Entreprise') ?></h6>
                        <p class="text-muted small mb-3">
                            <i class="fas fa-map-marker-alt me-1"></i><?= htmlspecialchars($offre['lieu']) ?>
                        </p>
                        <div class="company-highlights">
                            <div class="highlight-item">
                                <i class="fas fa-heart"></i>
                                <span>Engagé socialement</span>
                            </div>
                            <?php if ($offre['disability_friendly']): ?>
                                <div class="highlight-item">
                                    <i class="fas fa-universal-access"></i>
                                    <span>Entreprise inclusive</span>
                                </div>
                            <?php endif; ?>
                            <div class="highlight-item">
                                <i class="fas fa-leaf"></i>
                                <span>Démarche durable</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function shareOnLinkedIn() {
    const url = encodeURIComponent(window.location.href);
    const title = encodeURIComponent("<?= htmlspecialchars($offre['titre']) ?>");
    window.open(`https://www.linkedin.com/sharing/share-offsite/?url=${url}`, '_blank');
}

function shareOnTwitter() {
    const text = encodeURIComponent("Découvrez cette offre : <?= htmlspecialchars($offre['titre']) ?>");
    const url = encodeURIComponent(window.location.href);
    window.open(`https://twitter.com/intent/tweet?text=${text}&url=${url}`, '_blank');
}

function shareOnFacebook() {
    const url = encodeURIComponent(window.location.href);
    window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank');
}

// Animation au scroll
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.card');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, { threshold: 0.1 });

    cards.forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(card);
    });
});
</script>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>