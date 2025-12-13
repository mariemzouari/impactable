<?php require_once __DIR__ . '/../templates/header.php'; ?>

<style>
/* ==================== MODIFICATION CANDIDATURE STYLES ==================== */
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

/* Styles spécifiques à la page modification */
.modification-container {
    background: var(--sand);
    min-height: 100vh;
    padding: 2rem 0;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid var(--copper);
}

.page-header h1 {
    color: var(--brown);
    font-weight: 700;
    margin: 0;
}

.page-subtitle {
    color: var(--muted);
    font-size: 1.1rem;
    margin-top: 0.5rem;
}

/* Cartes améliorées */
.form-card {
    background: var(--white);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    padding: 2rem;
    margin-bottom: 2rem;
    border: 1px solid rgba(75,46,22,0.05);
    transition: all var(--ease-l);
}

.form-card:hover {
    box-shadow: var(--shadow-lg);
}

.form-card h3 {
    color: var(--brown);
    font-weight: 700;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 1.3rem;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid var(--light-sage);
}

.form-card h3 i {
    color: var(--copper);
}

/* Grille de formulaire */
.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group.full-width {
    grid-column: 1 / -1;
}

.form-label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: var(--brown);
    font-size: 0.95rem;
}

.form-label.required::after {
    content: ' *';
    color: var(--muted);
}

/* Champs de formulaire */
.form-input,
.form-select,
.form-textarea {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid rgba(75,46,22,0.1);
    border-radius: var(--radius-sm);
    background: var(--white);
    color: var(--brown);
    font-size: 0.95rem;
    transition: all var(--ease-s);
    font-family: inherit;
}

.form-textarea {
    resize: vertical;
    min-height: 120px;
}

.form-input:focus,
.form-select:focus,
.form-textarea:focus {
    outline: none;
    border-color: var(--copper);
    box-shadow: var(--focus);
}

.form-input:disabled,
.form-select:disabled {
    background: rgba(75,46,22,0.05);
    color: var(--muted);
    cursor: not-allowed;
}

/* Badges de statut */
.status-badge {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
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

/* Boutons */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.75rem 1.5rem;
    border-radius: var(--radius-sm);
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    transition: all var(--ease-s);
    border: none;
    background: transparent;
    color: var(--brown);
    position: relative;
    overflow: hidden;
    gap: 0.5rem;
    font-size: 0.9rem;
}

.btn.primary {
    background: var(--sage);
    color: var(--brown);
}

.btn.primary:hover,
.btn.primary:focus {
    background: var(--moss);
    color: var(--white);
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.btn.secondary {
    border: 2px solid var(--brown);
    background: transparent;
    color: var(--brown);
}

.btn.secondary:hover,
.btn.secondary:focus {
    background: var(--brown);
    color: var(--white);
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.btn.warning {
    background: var(--copper);
    color: var(--white);
}

.btn.warning:hover,
.btn.warning:focus {
    background: var(--brown);
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.btn.danger {
    background: var(--muted);
    color: var(--white);
}

.btn.danger:hover,
.btn.danger:focus {
    background: #5a3c35;
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

/* Actions */
.form-actions {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid rgba(75,46,22,0.1);
    flex-wrap: wrap;
}

/* Informations en lecture seule */
.readonly-info {
    background: rgba(169,185,125,0.05);
    padding: 1rem;
    border-radius: var(--radius-sm);
    border-left: 4px solid var(--sage);
    margin-top: 0.5rem;
}

.readonly-info p {
    margin: 0;
    color: var(--brown);
    font-weight: 500;
}

/* Liens de documents */
.doc-links {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    margin-top: 0.5rem;
}

.doc-link {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    background: rgba(169,185,125,0.1);
    border-radius: var(--radius-sm);
    color: var(--moss);
    text-decoration: none;
    font-weight: 500;
    transition: all var(--ease-s);
    border: 1px solid rgba(94,109,59,0.2);
}

.doc-link:hover {
    background: var(--moss);
    color: var(--white);
    transform: translateY(-2px);
    box-shadow: var(--shadow);
}

.doc-link.linkedin {
    background: rgba(10,102,194,0.1);
    color: #0a66c2;
    border-color: rgba(10,102,194,0.3);
}

.doc-link.linkedin:hover {
    background: #0a66c2;
    color: var(--white);
}

/* Messages d'alerte */
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

.alert-warning {
    background: rgba(180,123,71,0.1);
    color: var(--brown);
    border-left: 4px solid var(--copper);
}

/* Indicateur de chargement */
.loading {
    opacity: 0.7;
    pointer-events: none;
    position: relative;
}

.loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 20px;
    height: 20px;
    margin: -10px 0 0 -10px;
    border: 2px solid transparent;
    border-top: 2px solid var(--copper);
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Responsive */
@media (max-width: 768px) {
    .modification-container {
        padding: 1rem 0;
    }
    
    .page-header {
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start;
    }
    
    .form-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
        justify-content: center;
    }
    
    .doc-links {
        flex-direction: column;
    }
}

/* Amélioration de l'accessibilité */
.btn:focus,
.form-input:focus,
.form-select:focus,
.form-textarea:focus,
.doc-link:focus {
    outline: 2px solid var(--copper);
    outline-offset: 2px;
}

/* Style pour les champs obligatoires */
.form-input:required,
.form-select:required,
.form-textarea:required {
    border-left: 3px solid var(--copper);
}
</style>

<div class="modification-container">
    <div class="container-fluid">
        <div class="page-header">
            <div>
                <h1>Modifier la candidature</h1>
                <div class="page-subtitle">
                    Candidature de <strong><?= htmlspecialchars($candidat['prenom'] . ' ' . $candidat['nom']) ?></strong> 
                    pour l'offre <strong><?= htmlspecialchars($offre['titre']) ?></strong>
                </div>
            </div>
            <a href="index.php?action=admin-voir-candidature&id=<?= $candidature['Id_candidature'] ?>" class="btn secondary">
                <i class="fas fa-arrow-left"></i> Retour aux détails
            </a>
        </div>

        <!-- Messages d'alerte -->
        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i>
                <?= $success ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>


<form action="index.php?action=admin-modifier-candidature-traitement" method="POST" id="modificationForm">
    <input type="hidden" name="id_candidature" value="<?= $candidature['Id_candidature'] ?>">
            <!-- Informations de base -->
            <div class="form-card">
                <h3><i class="fas fa-info-circle"></i> Informations de base</h3>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Candidat</label>
                        <div class="readonly-info">
                            <p><?= htmlspecialchars($candidat['prenom'] . ' ' . $candidat['nom']) ?></p>
                            <small><?= htmlspecialchars($candidat['email']) ?></small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Offre</label>
                        <div class="readonly-info">
                            <p><?= htmlspecialchars($offre['titre']) ?></p>
                            <small><?= htmlspecialchars($offre['type_offre']) ?> • <?= htmlspecialchars($offre['lieu'] ?? 'Non spécifié') ?></small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Date de candidature</label>
                        <div class="readonly-info">
                            <p><?= date('d/m/Y à H:i', strtotime($candidature['date_candidature'])) ?></p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Statut actuel</label>
                        <div>
                            <span class="status-badge status-<?= $candidature['status'] ?>">
                                <?php
                                $statusLabels = [
                                    'en_attente' => 'En attente',
                                    'en_revue' => 'En revue',
                                    'entretien' => 'Entretien',
                                    'retenu' => 'Retenu',
                                    'refuse' => 'Refusé'
                                ];
                                echo htmlspecialchars($statusLabels[$candidature['status']] ?? $candidature['status']);
                                ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

<!-- Statut et évaluation -->
<div class="form-card">
    <h3><i class="fas fa-chart-line"></i> Statut et évaluation</h3>
    
    <div class="form-group">
        <label for="status" class="form-label required">Nouveau statut</label>
        <select name="status" id="status" class="form-select" required>
            <option value="">Sélectionnez un statut</option>
            <option value="en_attente" <?= $candidature['status'] === 'en_attente' ? 'selected' : '' ?>>En attente</option>
            <option value="en_revue" <?= $candidature['status'] === 'en_revue' ? 'selected' : '' ?>>En revue</option>
            <option value="entretien" <?= $candidature['status'] === 'entretien' ? 'selected' : '' ?>>Entretien</option>
            <option value="retenu" <?= $candidature['status'] === 'retenu' ? 'selected' : '' ?>>Retenu</option>
            <option value="refuse" <?= $candidature['status'] === 'refuse' ? 'selected' : '' ?>>Refusé</option>
        </select>
    </div>

    <div class="form-group">
        <label for="notes" class="form-label">Notes et commentaires</label>
        <textarea name="notes" id="notes" class="form-textarea" placeholder="Ajoutez vos observations sur cette candidature..."><?= htmlspecialchars($candidature['notes'] ?? '') ?></textarea>
        <small class="text-muted">Ces notes sont visibles uniquement par les administrateurs</small>
    </div>
</div>

            <!-- Documents joints -->
            <div class="form-card">
                <h3><i class="fas fa-paperclip"></i> Documents joints</h3>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">CV</label>
                        <div>
                            <?php if (!empty($candidature['cv'])): ?>
                                <div class="doc-links">
                                    <a href="<?= htmlspecialchars($candidature['cv']) ?>" target="_blank" class="doc-link">
                                        <i class="fas fa-external-link-alt"></i> Voir le CV
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="readonly-info">
                                    <p>Aucun CV fourni</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">LinkedIn</label>
                        <div>
                            <?php if (!empty($candidature['linkedin'])): ?>
                                <div class="doc-links">
                                    <a href="<?= htmlspecialchars($candidature['linkedin']) ?>" target="_blank" class="doc-link linkedin">
                                        <i class="fab fa-linkedin"></i> Voir le profil
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="readonly-info">
                                    <p>Aucun profil LinkedIn fourni</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="form-group full-width">
                    <label class="form-label">Lettre de motivation</label>
                    <div class="readonly-info">
                        <?php if (!empty($candidature['lettre_motivation'])): ?>
                            <p><?= nl2br(htmlspecialchars($candidature['lettre_motivation'])) ?></p>
                        <?php else: ?>
                            <p>Aucune lettre de motivation fournie</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="form-card">
                <h3><i class="fas fa-cog"></i> Actions</h3>
                
                <div class="form-actions">
                    <button type="submit" class="btn primary" id="submitBtn">
                        <i class="fas fa-save"></i> Enregistrer les modifications
                    </button>
                    
                    <a href="index.php?action=admin-voir-candidature&id=<?= $candidature['Id_candidature'] ?>" class="btn secondary">
                        <i class="fas fa-times"></i> Annuler
                    </a>
                    
                    <a href="index.php?action=admin-supprimer-candidature&id=<?= $candidature['Id_candidature'] ?>" 
                       class="btn danger"
                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette candidature ? Cette action est irréversible.')">
                        <i class="fas fa-trash"></i> Supprimer la candidature
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>



<?php require_once __DIR__ . '/../templates/footer.php'; ?>