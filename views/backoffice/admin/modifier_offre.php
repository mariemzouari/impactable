<?php require_once __DIR__ . '/../templates/header.php'; ?>

<style>
:root {
    /* Color palette */
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
    --focus: 0 0 0 3px rgba(180,123,71,0.18);
}

/* Styles personnalisés */
.modification-page {
    background: var(--sand);
    min-height: 100vh;
    padding: 2rem 0;
}

.custom-card {
    background: var(--white);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    border: 1px solid rgba(75,46,22,0.05);
    margin-bottom: 2rem;
}

.custom-card-header {
    background: linear-gradient(135deg, var(--sage), var(--light-sage));
    border-bottom: 2px solid var(--moss);
    color: var(--brown);
    font-weight: 700;
    border-radius: var(--radius) var(--radius) 0 0 !important;
    padding: 1.5rem 2rem;
}

.custom-card-body {
    padding: 2rem;
}

.custom-form-control {
    border: 2px solid rgba(75,46,22,0.1);
    border-radius: var(--radius-sm);
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
    background: var(--white);
    font-size: 0.95rem;
}

.custom-form-control:focus {
    border-color: var(--copper);
    box-shadow: var(--focus);
    background: var(--white);
}

.form-label {
    color: var(--brown);
    font-weight: 600;
    margin-bottom: 0.5rem;
    font-size: 0.95rem;
}

.form-text-help {
    color: var(--muted);
    font-size: 0.85rem;
    margin-top: 0.25rem;
}

.custom-btn-primary {
    background: linear-gradient(135deg, var(--sage), var(--moss));
    border: none;
    color: var(--brown);
    font-weight: 600;
    padding: 0.75rem 2rem;
    border-radius: var(--radius-sm);
    transition: all 0.3s ease;
    font-size: 0.95rem;
}

.custom-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
    background: linear-gradient(135deg, var(--moss), var(--dark-green));
    color: var(--white);
}

.custom-btn-secondary {
    background: transparent;
    border: 2px solid var(--brown);
    color: var(--brown);
    font-weight: 600;
    padding: 0.75rem 2rem;
    border-radius: var(--radius-sm);
    transition: all 0.3s ease;
    font-size: 0.95rem;
}

.custom-btn-secondary:hover {
    background: var(--brown);
    color: var(--white);
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.alert-custom-success {
    background: rgba(94,109,59,0.1);
    border: 1px solid var(--moss);
    border-left: 4px solid var(--moss);
    color: var(--dark-green);
    border-radius: var(--radius-sm);
    padding: 1rem 1.5rem;
}

.alert-custom-danger {
    background: rgba(107,75,68,0.1);
    border: 1px solid var(--muted);
    border-left: 4px solid var(--muted);
    color: var(--muted);
    border-radius: var(--radius-sm);
    padding: 1rem 1.5rem;
}

.info-box {
    background: rgba(169,185,125,0.05);
    border: 1px solid rgba(94,109,59,0.2);
    border-radius: var(--radius-sm);
    padding: 1rem;
    margin-bottom: 1rem;
}

.badge-custom {
    background: var(--copper);
    color: var(--white);
    border-radius: 20px;
    padding: 0.5rem 1rem;
    font-weight: 600;
    font-size: 0.85rem;
}

.form-check-input:checked {
    background-color: var(--moss);
    border-color: var(--moss);
}

.section-title {
    color: var(--brown);
    border-bottom: 2px solid var(--light-sage);
    padding-bottom: 0.75rem;
    margin-bottom: 1.5rem;
    font-weight: 700;
    font-size: 1.1rem;
}

.creator-avatar {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, var(--sage), var(--light-sage));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    color: var(--brown);
    font-size: 2rem;
}

.form-section {
    margin-bottom: 2.5rem;
    padding-bottom: 1.5rem;
}

.form-section:not(:last-child) {
    border-bottom: 1px solid rgba(75,46,22,0.1);
}

.page-header {
    margin-bottom: 2rem;
}

.page-title {
    color: var(--brown);
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.page-subtitle {
    color: var(--muted);
    font-size: 1rem;
}

/* Améliorations responsives */
@media (max-width: 768px) {
    .custom-card-body {
        padding: 1.5rem;
    }
    
    .custom-card-header {
        padding: 1.25rem 1.5rem;
    }
    
    .form-section {
        margin-bottom: 2rem;
        padding-bottom: 1rem;
    }
}
</style>

<div class="modification-page">
    <div class="container-fluid">
        <!-- En-tête de page -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center page-header">
                    <div>
                        <h1 class="page-title">
                            <i class="fas fa-edit me-2" style="color: var(--copper);"></i>
                            Modifier l'Offre
                        </h1>

                    </div>
                    <a href="index.php?action=admin-voir-offre&id=<?= $offre['Id_offre'] ?>" 
                       class="custom-btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Retour aux détails
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Colonne principale - Formulaire -->
            <div class="col-lg-8">
                <div class="custom-card">
                    <div class="custom-card-header">
                        <h6 class="m-0 font-weight-bold">
                            <i class="fas fa-edit me-2"></i>Formulaire de Modification
                        </h6>
                    </div>
                    <div class="custom-card-body">
                        <!-- Messages d'alerte -->
                        <?php if ($success): ?>
                            <div class="alert alert-custom-success alert-dismissible fade show mb-4" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                <?= $success ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-custom-danger alert-dismissible fade show mb-4" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?= $error ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <!-- Section 1: Informations principales -->
                            <div class="form-section">
                                <h5 class="section-title">
                                    <i class="fas fa-info-circle me-2"></i>Informations principales
                                </h5>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="titre" class="form-label">Titre de l'offre *</label>
                                        <input type="text" class="form-control custom-form-control" id="titre" name="titre" 
                                               value="<?= htmlspecialchars($offre['titre']) ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="type_offre" class="form-label">Type d'offre *</label>
                                        <select class="form-control custom-form-control" id="type_offre" name="type_offre" required>
                                            <option value="emploi" <?= $offre['type_offre'] === 'emploi' ? 'selected' : '' ?>>Emploi</option>
                                            <option value="stage" <?= $offre['type_offre'] === 'stage' ? 'selected' : '' ?>>Stage</option>
                                            <option value="alternance" <?= $offre['type_offre'] === 'alternance' ? 'selected' : '' ?>>Alternance</option>
                                            <option value="freelance" <?= $offre['type_offre'] === 'freelance' ? 'selected' : '' ?>>Freelance</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 2: Description -->
                            <div class="form-section">
                                <h5 class="section-title">
                                    <i class="fas fa-align-left me-2"></i>Description
                                </h5>
                                <div class="mb-4">
                                    <label for="description" class="form-label">Description de l'offre *</label>
                                    <textarea class="form-control custom-form-control" id="description" name="description" rows="5" required><?= htmlspecialchars($offre['description']) ?></textarea>
                                </div>

                                <div>
                                    <label for="impact_sociale" class="form-label">Impact social *</label>
                                    <textarea class="form-control custom-form-control" id="impact_sociale" name="impact_sociale" rows="3" required><?= htmlspecialchars($offre['impact_sociale']) ?></textarea>
                                    <div class="form-text-help">
                                        <i class="fas fa-lightbulb me-1"></i>Décrivez l'impact social positif de ce poste.
                                    </div>
                                </div>
                            </div>

                            <!-- Section 3: Détails pratiques -->
                            <div class="form-section">
                                <h5 class="section-title">
                                    <i class="fas fa-cogs me-2"></i>Détails pratiques
                                </h5>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="date_expiration" class="form-label">Date d'expiration *</label>
                                        <input type="date" class="form-control custom-form-control" id="date_expiration" name="date_expiration" 
                                               value="<?= $offre['date_expiration'] ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="lieu" class="form-label">Lieu *</label>
                                        <input type="text" class="form-control custom-form-control" id="lieu" name="lieu" 
                                               value="<?= htmlspecialchars($offre['lieu']) ?>" required>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="mode" class="form-label">Mode de travail</label>
                                        <select class="form-control custom-form-control" id="mode" name="mode">
                                            <option value="presentiel" <?= $offre['mode'] === 'presentiel' ? 'selected' : '' ?>>Présentiel</option>
                                            <option value="teletravail" <?= $offre['mode'] === 'teletravail' ? 'selected' : '' ?>>Télétravail</option>
                                            <option value="hybride" <?= $offre['mode'] === 'hybride' ? 'selected' : '' ?>>Hybride</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="horaire" class="form-label">Horaire</label>
                                        <select class="form-control custom-form-control" id="horaire" name="horaire">
                                            <option value="temps_plein" <?= $offre['horaire'] === 'temps_plein' ? 'selected' : '' ?>>Temps plein</option>
                                            <option value="temps_partiel" <?= $offre['horaire'] === 'temps_partiel' ? 'selected' : '' ?>>Temps partiel</option>
                                            <option value="flexible" <?= $offre['horaire'] === 'flexible' ? 'selected' : '' ?>>Flexible</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="pt-4">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="disability_friendly" 
                                                       name="disability_friendly" value="1" 
                                                       <?= $offre['disability_friendly'] ? 'checked' : '' ?>>
                                                <label class="form-check-label form-label" for="disability_friendly">
                                                    <i class="fas fa-universal-access me-1"></i>Accessible aux personnes handicapées
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 4: Accessibilité -->
                            <div class="form-section">
                                <h5 class="section-title">
                                    <i class="fas fa-universal-access me-2"></i>Accessibilité
                                </h5>
                                <div>
                                    <label class="form-label">Types de handicap ciblés</label>
                                    <div class="row">
                                        <?php
                                        $types_handicap_disponibles = ['moteur', 'visuel', 'auditif', 'mental', 'psychique'];
                                        $types_selectionnes = explode(',', $offre['type_handicap']);
                                        ?>
                                        <?php foreach ($types_handicap_disponibles as $type): ?>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input" name="type_handicap[]" 
                                                           value="<?= $type ?>" 
                                                           <?= in_array($type, $types_selectionnes) ? 'checked' : '' ?>>
                                                    <label class="form-check-label">
                                                        <?= ucfirst($type) ?>
                                                    </label>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <div class="form-text-help">
                                        <i class="fas fa-info-circle me-1"></i>Laissez vide ou cochez "tous" pour cibler tous les types de handicap.
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="form-section border-0 mb-0">
                                <div class="d-flex gap-3 flex-wrap">
                                    <button type="submit" class="custom-btn-primary">
                                        <i class="fas fa-save me-2"></i> Enregistrer les modifications
                                    </button>
                                    <a href="index.php?action=admin-voir-offre&id=<?= $offre['Id_offre'] ?>" 
                                       class="custom-btn-secondary">
                                        <i class="fas fa-times me-2"></i> Annuler
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Informations du créateur -->
                <div class="custom-card mb-4">
                    <div class="custom-card-header">
                        <h6 class="m-0 font-weight-bold">
                            <i class="fas fa-user me-2"></i>Créateur de l'Offre
                        </h6>
                    </div>
                    <div class="custom-card-body text-center">
                        <div class="creator-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <h5 style="color: var(--brown); margin-bottom: 0.5rem;">
                            <?= htmlspecialchars($createur['prenom'] . ' ' . $createur['nom']) ?>
                        </h5>
                        <p class="text-muted mb-2">
                            <?= htmlspecialchars($createur['email']) ?>
                        </p>
                        <p class="small mb-0">
                            <strong style="color: var(--brown);">Rôle:</strong> 
                            <span class="badge-custom">
                                <?= $createur['role'] ?>
                            </span>
                        </p>
                    </div>
                </div>

                <!-- Informations techniques -->
                <div class="custom-card">
                    <div class="custom-card-header">
                        <h6 class="m-0 font-weight-bold">
                            <i class="fas fa-cog me-2"></i>Informations Techniques
                        </h6>
                    </div>
                    <div class="custom-card-body">
                        <div class="info-box">
                            <strong style="color: var(--brown);">ID:</strong> 
                            <span class="float-end">#<?= $offre['Id_offre'] ?></span>
                        </div>
                        <div class="info-box">
                            <strong style="color: var(--brown);">Créée le:</strong> 
                            <span class="float-end"><?= date('d/m/Y H:i', strtotime($offre['date_publication'])) ?></span>
                        </div>
                        <?php if ($offre['date_modification'] && $offre['date_modification'] != $offre['date_publication']): ?>
                            <div class="info-box">
                                <strong style="color: var(--brown);">Modifiée le:</strong> 
                                <span class="float-end"><?= date('d/m/Y H:i', strtotime($offre['date_modification'])) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>