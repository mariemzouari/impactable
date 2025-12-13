<?php 
$title = "Opportunités | " . Config::SITE_NAME;
if (!defined('EMBEDDED')) {
    require_once __DIR__ . '/../templates/header.php'; 
}
?>

<?php if (!defined('EMBEDDED')): ?>
<!-- Hero Section -->
<section class="section">
    <div class="hero">
        <div class="hero-content">
            <h1>Trouvez l'opportunité qui vous correspond</h1>
            <p class="lead">Découvrez des emplois, stages, missions et formations adaptés à vos compétences et accessibles à tous.</p>

            <div class="hero-cta">
                <button class="btn primary" onclick="document.getElementById('searchForm').scrollIntoView({behavior: 'smooth'})">
                    <i class="fas fa-search"></i>
                    Rechercher
                </button>
                <button class="btn secondary" onclick="document.getElementById('filtresSection').scrollIntoView({behavior: 'smooth'})">
                    <i class="fas fa-filter"></i>
                    Filtrer
                </button>
                <a href="index.php?action=poster-offre" class="btn offer">
                    <i class="fas fa-plus-circle"></i>
                    Poster une offre
                </a>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Filtres -->
<section class="section" id="filtresSection">
    <form method="GET" action="" id="searchForm">
        <input type="hidden" name="action" value="offres">
        
        <div class="section-header">
            <h2>Filtres Rapides</h2>
        </div>

        <div class="feature-list" style="justify-content: center; gap: 1rem; margin-bottom: 2rem;">
            <button type="button" class="badge filter-btn" data-mode="en_ligne">
                <i class="fas fa-laptop-house"></i>
                Remote
            </button>
            <button type="button" class="badge filter-btn" data-mode="presentiel">
                <i class="fas fa-building"></i>
                Présentiel
            </button>
            <button type="button" class="badge filter-btn" data-mode="hybride">
                <i class="fas fa-balance-scale"></i>
                Hybride
            </button>
            <button type="button" class="badge filter-btn" data-horaire="temps_plein">
                <i class="fas fa-clock"></i>
                Temps plein
            </button>
            <button type="button" class="badge filter-btn" data-horaire="temps_partiel">
                <i class="fas fa-chart-pie"></i>
                Temps partiel
            </button>
        </div>

        <div class="cards-grid">
            <div class="card">
                <div class="card-body">
                    <h3>Accessibilité</h3>
                    <div class="checkbox-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="disability_friendly" value="1" <?php echo (isset($_GET['disability_friendly'])) ? 'checked' : ''; ?>>
                            Accessibilité adaptée
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="type_handicap" value="moteur" <?php echo (isset($_GET['type_handicap']) && $_GET['type_handicap'] == 'moteur') ? 'checked' : ''; ?>>
                            Handicap moteur
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="type_handicap" value="visuel" <?php echo (isset($_GET['type_handicap']) && $_GET['type_handicap'] == 'visuel') ? 'checked' : ''; ?>>
                            Handicap visuel
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="type_handicap" value="auditif" <?php echo (isset($_GET['type_handicap']) && $_GET['type_handicap'] == 'auditif') ? 'checked' : ''; ?>>
                            Handicap auditif
                        </label>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h3>Type d'opportunité</h3>
                    <div class="checkbox-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="type_offre" value="emploi" <?php echo (isset($_GET['type_offre']) && $_GET['type_offre'] == 'emploi') ? 'checked' : ''; ?>>
                            Emploi
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="type_offre" value="stage" <?php echo (isset($_GET['type_offre']) && $_GET['type_offre'] == 'stage') ? 'checked' : ''; ?>>
                            Stage
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="type_offre" value="volontariat" <?php echo (isset($_GET['type_offre']) && $_GET['type_offre'] == 'volontariat') ? 'checked' : ''; ?>>
                            Volontariat
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="type_offre" value="formation" <?php echo (isset($_GET['type_offre']) && $_GET['type_offre'] == 'formation') ? 'checked' : ''; ?>>
                            Formation
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-actions" style="justify-content: center; margin-top: 2rem;">
            <button type="submit" class="btn primary">
                <i class="fas fa-search"></i>
                Appliquer les filtres
            </button>
            <a href="index.php?action=offres" class="btn secondary">
                <i class="fas fa-times"></i>
                Réinitialiser
            </a>
        </div>
    </form>
</section>

<!-- Offres -->
<section class="section">
    <div class="section-header">
        <h2>Les offres disponibles (<?php echo count($offres); ?>)</h2>
        <a href="toutes_offres.php" class="section-link">
            Voir toutes
            <i class="fas fa-arrow-right"></i>
        </a>
    </div>

    <?php if (empty($offres)): ?>
        <div class="card" style="text-align: center;">
            <div class="card-body">
                <h3>Aucune offre trouvée</h3>
                <p class="text-muted">Aucune offre ne correspond à vos critères de recherche.</p>
                <a href="index.php?action=offres" class="btn primary">
                    Voir toutes les offres
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="cards-grid">
            <?php foreach ($offres as $offre): ?>
                <article class="card">
                    <div class="card-body">
                        <div class="card-header">
                            <h3 class="card-title"><?php echo Utils::escape($offre['titre']); ?></h3>
                            <?php if (Utils::isNew($offre['date_publication'])): ?>
                                <span class="badge new">Nouveau</span>
                            <?php endif; ?>
                        </div>

                        <div class="offer-meta">
                            <span class="offer-type">
                                <i class="fas fa-briefcase"></i>
                                <?php echo Utils::formatTypeOffre($offre['type_offre']); ?>
                            </span>
                            <span class="offer-mode">
                                <i class="fas fa-laptop-house"></i>
                                <?php echo Utils::formatMode($offre['mode']); ?>
                            </span>
                            <span class="offer-schedule">
                                <i class="fas fa-clock"></i>
                                <?php echo str_replace('_', ' ', $offre['horaire']); ?>
                            </span>
                            <?php if (!empty($offre['lieu'])): ?>
                                <span class="offer-location">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?php echo Utils::escape($offre['lieu']); ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <p class="card-excerpt"><?php echo Utils::escape(Utils::getExcerpt($offre['description'])); ?></p>
                        
                        <div class="offer-features">
                            <?php if ($offre['disability_friendly']): ?>
                                <div class="feature-badge accessible">
                                    <i class="fas fa-wheelchair"></i>
                                    Accessible
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($offre['type_handicap'])): ?>
                                <?php if (strpos($offre['type_handicap'], 'moteur') !== false): ?>
                                    <div class="feature-badge handicap">
                                        <i class="fas fa-wheelchair-move"></i>
                                        Moteur
                                    </div>
                                <?php endif; ?>
                                <?php if (strpos($offre['type_handicap'], 'visuel') !== false): ?>
                                    <div class="feature-badge handicap">
                                        <i class="fas fa-low-vision"></i>
                                        Visuel
                                    </div>
                                <?php endif; ?>
                                <?php if (strpos($offre['type_handicap'], 'auditif') !== false): ?>
                                    <div class="feature-badge handicap">
                                        <i class="fas fa-deaf"></i>
                                        Auditif
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>

                        <div class="card-actions">
                            <?php 
                            // Utiliser la variable $candidatureManager du contrôleur
                            $candidatureManager = new Candidature();
                            if ($candidatureManager->hasAlreadyApplied($_SESSION['user_id'], $offre['Id_offre'])): 
                            ?>
                                <button class="btn success" disabled>
                                    <i class="fas fa-check"></i>
                                    Déjà postulé
                                </button>
                            <?php else: ?>
                                <a class="btn primary" href="index.php?action=postuler&id=<?php echo $offre['Id_offre']; ?>">
                                    <i class="fas fa-paper-plane"></i>
                                    Postuler
                                </a>
                            <?php endif; ?>
                            
                            <a class="btn ghost" href="index.php?action=details-offre&id=<?php echo $offre['Id_offre']; ?>">
                                <i class="fas fa-info-circle"></i>
                                Détails
                            </a>
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <span class="publication-date">
                            <i class="far fa-clock"></i>
                            <?php echo Utils::formatDate($offre['date_publication']); ?>
                        </span>
                        <span class="applicants-count">
                            <i class="fas fa-users"></i>
                            <?php echo $candidatureManager->getCountByOffre($offre['Id_offre']); ?> candidats
                        </span>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<!-- Statistiques -->
<section class="section">
    <div class="section-header">
        <h2>Impact de Notre Plateforme</h2>
    </div>

    <div class="stats">
        <div class="stat-card">
            <div class="stat-number"><?php echo $candidaturesPlacees; ?>+</div>
            <div class="stat-label">Personnes placées</div>
        </div>
    </div>
</section>

<?php if (!defined('EMBEDDED')) {
    require_once __DIR__ . '/../templates/footer.php'; 
} ?>
<script>
document.querySelectorAll('.filter-btn').forEach(button => {
    button.addEventListener('click', function() {
        const form = document.getElementById('searchForm');
        const mode = this.getAttribute('data-mode');
        const horaire = this.getAttribute('data-horaire');
        
        if (mode) {
            let input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'mode';
            input.value = mode;
            form.appendChild(input);
        }
        
        if (horaire) {
            let input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'horaire';
            input.value = horaire;
            form.appendChild(input);
        }
        
        form.submit();
    });
});
</script>