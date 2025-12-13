<?php require_once __DIR__ . '/../templates/header.php'; ?>

<style>
/* ==================== DÉTAILS CANDIDATURE STYLES ==================== */
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

/* Styles spécifiques à la page détail candidature */
.candidature-container {
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

/* Cartes améliorées */
.info-card {
    background: var(--white);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    padding: 2rem;
    margin-bottom: 2rem;
    border: 1px solid rgba(75,46,22,0.05);
    transition: all var(--ease-l);
    position: relative;
    overflow: hidden;
}

.info-card:hover {
    box-shadow: var(--shadow-lg);
    transform: translateY(-2px);
}

.info-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: var(--copper);
    opacity: 0;
    transition: opacity var(--ease-s);
}

.info-card:hover::before {
    opacity: 1;
}

.info-card h3 {
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

.info-card h3 i {
    color: var(--copper);
}

/* Grille d'information */
.info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    margin-bottom: 1.5rem;
}

.info-item {
    margin-bottom: 1.25rem;
}

.info-label {
    font-weight: 600;
    color: var(--muted);
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.info-value {
    font-size: 1rem;
    color: var(--brown);
    font-weight: 500;
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

.btn.small {
    padding: 0.5rem 1rem;
    font-size: 0.8rem;
}

/* Actions */
.actions {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
    flex-wrap: wrap;
}

/* Contenu texte */
.text-content {
    background: rgba(169,185,125,0.05);
    padding: 1.5rem;
    border-radius: var(--radius-sm);
    border-left: 4px solid var(--sage);
    line-height: 1.6;
    color: var(--brown);
    margin-top: 0.5rem;
}

.text-content.notes {
    background: rgba(180,123,71,0.05);
    border-left-color: var(--copper);
}

/* Liens de documents */
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

/* Formulaire de statut */
.status-form {
    background: rgba(169,185,125,0.05);
    padding: 1.5rem;
    border-radius: var(--radius-sm);
    margin-top: 1rem;
    border: 1px solid rgba(94,109,59,0.1);
}

.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: var(--brown);
}

.form-select {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid rgba(75,46,22,0.1);
    border-radius: var(--radius-sm);
    background: var(--white);
    color: var(--brown);
    font-size: 0.9rem;
    transition: all var(--ease-s);
}

.form-select:focus {
    outline: none;
    border-color: var(--copper);
    box-shadow: var(--focus);
}

.textarea {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid rgba(75,46,22,0.1);
    border-radius: var(--radius-sm);
    background: var(--white);
    color: var(--brown);
    font-size: 0.9rem;
    resize: vertical;
    min-height: 100px;
    transition: all var(--ease-s);
}

.textarea:focus {
    outline: none;
    border-color: var(--copper);
    box-shadow: var(--focus);
}

.form-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    margin-top: 1.5rem;
}

/* Modal de confirmation */
.modal-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(75,46,22,0.5);
    z-index: 1000;
    align-items: center;
    justify-content: center;
}

.modal {
    background: var(--white);
    border-radius: var(--radius);
    padding: 2rem;
    box-shadow: var(--shadow-lg);
    max-width: 500px;
    width: 90%;
    text-align: center;
}

.modal h3 {
    color: var(--brown);
    margin-bottom: 1rem;
}

.modal p {
    color: var(--muted);
    margin-bottom: 2rem;
}

.modal-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
}

/* Responsive */
@media (max-width: 768px) {
    .candidature-container {
        padding: 1rem 0;
    }
    
    .page-header {
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start;
    }
    
    .info-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .actions {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
        justify-content: center;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .modal-actions {
        flex-direction: column;
    }
}

/* Amélioration de l'accessibilité */
.btn:focus,
.doc-link:focus {
    outline: 2px solid var(--copper);
    outline-offset: 2px;
}
</style>

<div class="candidature-container">
    <div class="container-fluid">
        <div class="page-header">
            <h1>Détails de la candidature</h1>
            <a href="index.php?action=admin-gestion-candidatures" class="btn secondary">
                <i class="fas fa-arrow-left"></i> Retour aux candidatures
            </a>
        </div>

        <div class="info-grid">
            <!-- Informations candidat -->
            <div class="info-card">
                <h3><i class="fas fa-user"></i> Informations du candidat</h3>
                <div class="info-item">
                    <div class="info-label">Nom complet</div>
                    <div class="info-value"><?= htmlspecialchars($candidat['prenom'] . ' ' . $candidat['nom']) ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Email</div>
                    <div class="info-value"><?= htmlspecialchars($candidat['email']) ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Téléphone</div>
                    <div class="info-value"><?= htmlspecialchars($candidat['numero_tel'] ?? 'Non renseigné') ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Type de handicap</div>
                    <div class="info-value">
                        <?php
                        $handicapLabels = [
                            'aucun' => 'Aucun',
                            'moteur' => 'Moteur', 
                            'visuel' => 'Visuel',
                            'auditif' => 'Auditif',
                            'mental' => 'Mental',
                            'autre' => 'Autre',
                            'tous' => 'Tous'
                        ];
                        $types = explode(',', $candidat['type_handicap']);
                        $labels = array_map(function($type) use ($handicapLabels) {
                            return $handicapLabels[$type] ?? $type;
                        }, $types);
                        echo htmlspecialchars(implode(', ', $labels));
                        ?>
                    </div>
                </div>
            </div>

            <!-- Informations offre -->
            <div class="info-card">
                <h3><i class="fas fa-briefcase"></i> Informations de l'offre</h3>
                <div class="info-item">
                    <div class="info-label">Titre de l'offre</div>
                    <div class="info-value"><?= htmlspecialchars($offre['titre']) ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Type d'offre</div>
                    <div class="info-value">
                        <?php
                        $typeOffreLabels = [
                            'emploi' => 'Emploi',
                            'stage' => 'Stage',
                            'volontariat' => 'Volontariat', 
                            'formation' => 'Formation',
                            'autre' => 'Autre'
                        ];
                        echo htmlspecialchars($typeOffreLabels[$offre['type_offre']] ?? $offre['type_offre']);
                        ?>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Mode de travail</div>
                    <div class="info-value">
                        <?php
                        $modeLabels = [
                            'presentiel' => 'Présentiel',
                            'en_ligne' => 'En ligne',
                            'hybride' => 'Hybride'
                        ];
                        echo htmlspecialchars($modeLabels[$offre['mode']] ?? $offre['mode']);
                        ?>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Lieu</div>
                    <div class="info-value"><?= htmlspecialchars($offre['lieu'] ?? 'Non spécifié') ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Recruteur</div>
                    <div class="info-value">
                        <?php if ($recruteur): ?>
                            <?= htmlspecialchars($recruteur['prenom'] . ' ' . $recruteur['nom']) ?>
                        <?php else: ?>
                            <span style="color: var(--muted);">Non spécifié</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Détails candidature -->
        <div class="info-card">
            <h3><i class="fas fa-file-alt"></i> Détails de la candidature</h3>
            
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Statut</div>
                    <div class="info-value">
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
                
                <div class="info-item">
                    <div class="info-label">Date de candidature</div>
                    <div class="info-value"><?= date('d/m/Y à H:i', strtotime($candidature['date_candidature'])) ?></div>
                </div>
            </div>

            <!-- CV et LinkedIn -->
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">CV</div>
                    <div class="info-value">
                        <?php if (!empty($candidature['cv'])): ?>
                            <a href="<?= htmlspecialchars($candidature['cv']) ?>" target="_blank" class="doc-link">
                                <i class="fas fa-external-link-alt"></i> Voir le CV
                            </a>
                        <?php else: ?>
                            <span style="color: var(--muted);">Non fourni</span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">LinkedIn</div>
                    <div class="info-value">
                        <?php if (!empty($candidature['linkedin'])): ?>
                            <a href="<?= htmlspecialchars($candidature['linkedin']) ?>" target="_blank" class="doc-link linkedin">
                                <i class="fab fa-linkedin"></i> Voir le profil
                            </a>
                        <?php else: ?>
                            <span style="color: var(--muted);">Non fourni</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Lettre de motivation -->
            <div class="info-item">
                <div class="info-label">Lettre de motivation</div>
                <div class="info-value">
                    <?php if (!empty($candidature['lettre_motivation'])): ?>
                        <div class="text-content">
                            <?= nl2br(htmlspecialchars($candidature['lettre_motivation'])) ?>
                        </div>
                    <?php else: ?>
                        <span style="color: var(--muted);">Aucune lettre de motivation fournie</span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Notes -->
            <?php if (!empty($candidature['notes'])): ?>
            <div class="info-item">
                <div class="info-label">Notes</div>
                <div class="info-value">
                    <div class="text-content notes">
                        <?= nl2br(htmlspecialchars($candidature['notes'])) ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

     

        <!-- Actions -->
        <div class="info-card">
            <h3><i class="fas fa-cog"></i> Actions</h3>
            <div class="actions">
                <a href="index.php?action=admin-gestion-candidatures" class="btn secondary">
                    <i class="fas fa-arrow-left"></i> Retour à la liste
                </a>
                <a href="index.php?action=admin-voir-offre&id=<?= $offre['Id_offre'] ?>" class="btn primary">
                    <i class="fas fa-eye"></i> Voir l'offre
                </a>
                <button type="button" class="btn danger" onclick="openDeleteModal()">
                    <i class="fas fa-trash"></i> Supprimer la candidature
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal-overlay" id="deleteModal">
    <div class="modal">
        <h3>Confirmer la suppression</h3>
        <p>Êtes-vous sûr de vouloir supprimer cette candidature ? Cette action est irréversible.</p>
        <div class="modal-actions">
            <button type="button" class="btn secondary" onclick="closeDeleteModal()">
                <i class="fas fa-times"></i> Annuler
            </button>
            <a href="index.php?action=admin-supprimer-candidature&id=<?= $candidature['Id_candidature'] ?>" class="btn danger">
                <i class="fas fa-trash"></i> Supprimer
            </a>
        </div>
    </div>
</div>

<script>
function openDeleteModal() {
    document.getElementById('deleteModal').style.display = 'flex';
}

function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
}

// Fermer la modal en cliquant à l'extérieur
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});

// Fermer la modal avec la touche Échap
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDeleteModal();
    }
});
</script>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>