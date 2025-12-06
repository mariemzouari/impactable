<?php
require_once(__DIR__ . '/../../controller/ReclamationController.php');
require_once(__DIR__ . '/../../controller/ReponseController.php');

$reclamationController = new ReclamationController();
$reponseController = new ReponseController();

// Récupérer toutes les réclamations
$reclamations = $reclamationController->listReclamations();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Déposer une Réclamation - ImpactAble</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- CSS EXTERNE -->
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <div class="container">
        <header>
            <div class="logo-brand">
                <div class="logo-icon-box"><i class="fas fa-compress-arrows-alt"></i></div>
                <div class="logo-text-box">
                    <span class="logo-name">ImpactAble</span>
                    <span class="logo-slogan">Where Ability Meets Impact</span>
                </div>
            </div>
            <h2>Système de Réclamations</h2>
            <p class="subtitle">Votre voix compte. Nous sommes là pour vous écouter.</p>
            <div style="display: flex; gap: 15px; flex-wrap: wrap; justify-content: center; margin-top: 20px;">
                <a href="suivi_reclamation.php" class="dashboard-btn-header" style="background: linear-gradient(135deg, #4B2E16, #5E6D3B);">
                    <i class="fas fa-search"></i> Suivre ma Réclamation
                </a>
                <a href="demo_ia.php" class="dashboard-btn-header" style="background: linear-gradient(135deg, #b47b47, #4B2E16);">
                    <i class="fas fa-brain"></i> Démo IA
                </a>
                <a href="../backoffice/admin_dashboard.php" class="dashboard-btn-header">
                    <i class="fas fa-tachometer-alt"></i> Dashboard Admin
                </a>
            </div>
        </header>

        <!-- Navigation -->
        <div class="nav-tabs">
            <button class="tab-btn active" onclick="switchTab('nouvelle')">
                <i class="fas fa-plus-circle"></i> Nouvelle Réclamation
            </button>
            <button class="tab-btn" onclick="switchTab('mes-reclamations')">
                <i class="fas fa-list"></i> Mes Réclamations
            </button>
        </div>

        <!-- FORMULAIRE -->
        <div class="section active" id="nouvelle-section">

            <div class="info-banner">
                <h3><i class="fas fa-info-circle"></i> Comment ça marche ?</h3>
                <ul>
                    <li>Remplissez tous les champs obligatoires du formulaire ci-dessous</li>
                    <li>Notre équipe traitera votre réclamation sous 48h</li>
                    <li>Vous recevrez un numéro de suivi après l'envoi</li>
                    <li>Vous pouvez suivre vos réclamations dans l'onglet dédié</li>
                    <li>Toutes vos données sont sécurisées</li>
                </ul>
            </div>

            <form class="reclamation-form" id="reclamationForm" action="submit_reclamation.php" method="POST" enctype="multipart/form-data">

                <!-- SECTION 1: Informations Personnelles -->
                <div class="form-section">
                    <h3><i class="fas fa-user"></i> 1. Informations Personnelles</h3>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="nom">Nom <span class="required">*</span></label>
                            <input type="text" id="nom" name="nom" placeholder="Votre nom">
                        </div>

                        <div class="form-group">
                            <label for="prenom">Prénom <span class="required">*</span></label>
                            <input type="text" id="prenom" name="prenom" placeholder="Votre prénom">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="email">Email <span class="required">*</span></label>
                            <input type="text" id="email" name="email" placeholder="votre.email@example.com">
                        </div>

                        <div class="form-group">
                            <label for="telephone">Téléphone <span class="required">*</span></label>
                            <input type="text" id="telephone" name="telephone" placeholder="+216 XX XXX XXX">
                        </div>
                    </div>
                </div>

                <!-- SECTION 2: Informations de la Réclamation -->
                <div class="form-section">
                    <h3><i class="fas fa-file-alt"></i> 2. Informations de la Réclamation</h3>

                    <div class="form-group">
                        <label for="sujet">Sujet de la réclamation <span class="required">*</span></label>
                        <input type="text" id="sujet" name="sujet" placeholder="Résumez votre réclamation en quelques mots">
                    </div>

                    <div class="form-group">
                        <label for="categorie">Catégorie <span class="required">*</span></label>
                        <select id="categorie" name="categorie">
                            <option value="">-- Choisir une catégorie --</option>
                            <option value="Technique">Technique</option>
                            <option value="Facturation">Facturation</option>
                            <option value="Service">Service</option>
                            <option value="Produit">Produit</option>
                            <option value="Accessibilité">Accessibilité</option>
                            <option value="Discrimination">Discrimination</option>
                            <option value="Transport">Transport</option>
                            <option value="Éducation">Éducation</option>
                            <option value="Emploi">Emploi</option>
                            <option value="Santé">Santé</option>
                            <option value="Administration">Administration</option>
                            <option value="Autre">Autre</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="description">Description détaillée <span class="required">*</span></label>
                        <textarea id="description" name="description" placeholder="Décrivez votre réclamation en détail..."></textarea>
                        <div class="char-counter"><span id="charCount">0</span> / 2000 caractères</div>
                    </div>

                    <div class="form-group">
                        <label>Priorité <span class="required">*</span></label>
                        <div class="priorite-options">
                            <div class="priorite-option">
                                <input type="radio" id="faible" name="priorite" value="Faible">
                                <label for="faible" class="priorite-label">Faible</label>
                            </div>
                            <div class="priorite-option">
                                <input type="radio" id="moyenne" name="priorite" value="Moyenne" checked>
                                <label for="moyenne" class="priorite-label">Moyenne</label>
                            </div>
                            <div class="priorite-option">
                                <input type="radio" id="urgente" name="priorite" value="Urgente">
                                <label for="urgente" class="priorite-label">Urgente</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECTION 3: Détails de l'Incident -->
                <div class="form-section">
                    <h3><i class="fas fa-map-marker-alt"></i> 3. Détails de l'Incident</h3>

                    <div class="form-group">
                        <label for="lieu">Lieu de l'incident <span class="required">*</span></label>
                        <input type="text" id="lieu" name="lieu" placeholder="Où s'est produit l'incident ?">
                    </div>

                    <div class="form-group">
                        <label for="dateIncident">Date de l'incident <span class="required">*</span></label>
                        <input type="date" id="dateIncident" name="dateIncident" max="<?= date('Y-m-d') ?>">
                    </div>

                    <div class="form-group">
                        <label for="typeHandicap">Type de handicap (si applicable)</label>
                        <select id="typeHandicap" name="typeHandicap">
                            <option value="">-- Choisir --</option>
                            <option value="physique">Handicap physique</option>
                            <option value="mental">Handicap mental</option>
                            <option value="sensoriel">Handicap sensoriel</option>
                            <option value="psychique">Handicap psychique</option>
                            <option value="invisible">Handicap invisible</option>
                            <option value="social">Handicap social</option>
                            <option value="aucun">Aucun</option>
                        </select>
                    </div>
                </div>

                <!-- SECTION 4: Personnes Impliquées -->
                <div class="form-section">
                    <h3><i class="fas fa-users"></i> 4. Personnes Impliquées</h3>

                    <div class="form-group">
                        <label for="personnesImpliquees">Personnes impliquées (optionnel)</label>
                        <textarea id="personnesImpliquees" name="personnesImpliquees" placeholder="Nommez les personnes impliquées dans l'incident..."></textarea>
                    </div>

                    <div class="form-group">
                        <label for="temoins">Témoins (optionnel)</label>
                        <textarea id="temoins" name="temoins" placeholder="Nommez les témoins de l'incident..."></textarea>
                    </div>
                </div>

                <!-- SECTION 5: Actions et Solutions -->
                <div class="form-section">
                    <h3><i class="fas fa-tasks"></i> 5. Actions et Solutions</h3>

                    <div class="form-group">
                        <label for="actionsPrecedentes">Actions déjà entreprises (optionnel)</label>
                        <textarea id="actionsPrecedentes" name="actionsPrecedentes" placeholder="Décrivez les actions que vous avez déjà entreprises..."></textarea>
                    </div>

                    <div class="form-group">
                        <label for="solutionSouhaitee">Solution souhaitée <span class="required">*</span></label>
                        <textarea id="solutionSouhaitee" name="solutionSouhaitee" placeholder="Quelle solution souhaitez-vous ?"></textarea>
                    </div>
                </div>

                <!-- SECTION 6: Pièce Jointe -->
                <div class="form-section">
                    <h3><i class="fas fa-image"></i> 6. Pièce Jointe</h3>

                    <div class="form-group">
                        <label for="image">Image (optionnel)</label>
                        <input type="file" id="image" name="image" accept="image/*" onchange="previewImage(this)">
                        <small style="color: #5E6D38; display: block; margin-top: 5px;">
                            Formats acceptés: JPG, PNG, GIF (max 5MB)
                        </small>
                        <div id="imagePreview" style="margin-top: 15px; display: none;">
                            <img id="previewImg" src="" alt="Aperçu" style="max-width: 300px; max-height: 200px; border-radius: 10px; border: 2px solid #A9B57D;">
                            <button type="button" onclick="removeImage()" style="margin-top: 10px; padding: 8px 15px; background: #D32F2F; color: white; border: none; border-radius: 8px; cursor: pointer;">
                                <i class="fas fa-times"></i> Supprimer l'image
                            </button>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="utilisateurId" value="1">

                <!-- ENVOI -->
                <div class="submit-section">
                    <button type="submit" class="submit-btn">
                        <i class="fas fa-paper-plane"></i> Envoyer ma réclamation
                    </button>
                    <div class="loader" id="loader">
                        <div class="spinner"></div>
                        <p>Envoi en cours...</p>
                    </div>
                </div>

                <!-- MESSAGE SUCCESS -->
                <div class="success-message" id="successMessage">
                    <i class="fas fa-check-circle"></i>
                    <h3>Réclamation envoyée avec succès !</h3>
                    <p>Nous avons bien reçu votre réclamation.</p>
                    <p><strong>Numéro de suivi : <span id="trackingNumber"></span></strong></p>
                    
                    <!-- Analyse IA -->
                    <div id="iaAnalysisResult" style="margin-top: 20px; background: rgba(255,255,255,0.2); padding: 15px; border-radius: 10px; display: none;">
                        <h4 style="margin-bottom: 10px;"><i class="fas fa-brain"></i> Analyse Intelligente</h4>
                        <p><strong>Priorité détectée :</strong> <span id="iaPriorite"></span></p>
                        <p><strong>Niveau de confiance :</strong> <span id="iaConfiance"></span>%</p>
                    </div>
                    
                    <a href="suivi_reclamation.php" id="suiviLink" class="submit-btn" style="margin-top: 20px; text-decoration: none; display: inline-block;">
                        <i class="fas fa-search"></i> Suivre ma réclamation
                    </a>
                </div>

            </form>
        </div>

        <!-- MES RÉCLAMATIONS -->
        <div class="section" id="mes-reclamations-section">
            <div class="mes-reclamations">

                <h3><i class="fas fa-list"></i> Mes Réclamations</h3>

                <div class="search-section">
                    <input type="text" class="search-input" id="searchReclamation" placeholder="🔍 Rechercher par numéro ou sujet...">
                </div>

                <div class="reclamations-list" id="reclamationsList">
                    <?php if (empty($reclamations)): ?>
                        <div class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <h3>Aucune réclamation trouvée</h3>
                            <p>Vous n'avez pas encore déposé de réclamation.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($reclamations as $rec): ?>
                            <?php 
                            // Récupérer les réponses pour cette réclamation
                            $reponses = $reponseController->getReponsesByReclamation($rec['id']);
                            $nbReponses = count($reponses);
                            ?>
                            <div class="reclamation-card" data-id="<?= htmlspecialchars($rec['id']) ?>" data-sujet="<?= htmlspecialchars(strtolower($rec['sujet'])) ?>">
                                <div class="reclamation-header">
                                    <div class="reclamation-id">
                                        <i class="fas fa-hashtag"></i>
                                        <strong>#<?= htmlspecialchars($rec['id']) ?></strong>
                                    </div>
                                    <div class="reclamation-date">
                                        <i class="fas fa-calendar"></i>
                                        <?= date('d/m/Y H:i', strtotime($rec['dateCreation'])) ?>
                                    </div>
                                </div>
                                
                                <div class="reclamation-body">
                                    <h4 class="reclamation-sujet"><?= htmlspecialchars($rec['sujet']) ?></h4>
                                    <p class="reclamation-description">
                                        <?= htmlspecialchars(substr($rec['description'], 0, 150)) ?>
                                        <?= strlen($rec['description']) > 150 ? '...' : '' ?>
                                    </p>
                                    
                                    <div class="reclamation-details">
                                        <div class="detail-item">
                                            <i class="fas fa-tag"></i>
                                            <span><?= htmlspecialchars($rec['categorie']) ?></span>
                                        </div>
                                        <div class="detail-item">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <span><?= htmlspecialchars($rec['lieu'] ?? 'Non spécifié') ?></span>
                                        </div>
                                        <?php if ($rec['agentAttribue']): ?>
                                        <div class="detail-item">
                                            <i class="fas fa-user-tie"></i>
                                            <span>Agent: <?= htmlspecialchars($rec['agentAttribue']) ?></span>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="reclamation-footer">
                                    <div class="reclamation-badges">
                                        <span class="badge badge-priority badge-<?= strtolower($rec['priorite']) ?>">
                                            <i class="fas fa-exclamation-circle"></i>
                                            <?= htmlspecialchars($rec['priorite']) ?>
                                        </span>
                                        <span class="badge badge-status badge-<?= strtolower(str_replace(' ', '-', $rec['statut'])) ?>">
                                            <i class="fas fa-info-circle"></i>
                                            <?= htmlspecialchars($rec['statut']) ?>
                                        </span>
                                        <?php if ($nbReponses > 0): ?>
                                        <span class="badge badge-reponses">
                                            <i class="fas fa-comments"></i>
                                            <?= $nbReponses ?> réponse<?= $nbReponses > 1 ? 's' : '' ?>
                                        </span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="reclamation-actions">
                                        <button class="btn-repondre" onclick="openReponseModal(<?= $rec['id'] ?>, '<?= htmlspecialchars(addslashes($rec['sujet'])) ?>')">
                                            <i class="fas fa-reply"></i>
                                            Répondre
                                        </button>
                                        <?php if ($nbReponses > 0): ?>
                                        <button class="btn-toggle-reponses" onclick="toggleReponses(<?= $rec['id'] ?>)">
                                            <i class="fas fa-chevron-down"></i>
                                            Voir les réponses
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <?php if ($nbReponses > 0): ?>
                                    <div class="reponses-preview">
                                        <div class="reponses-list" id="reponses-<?= $rec['id'] ?>" style="display:none;">
                                            <?php foreach ($reponses as $rep): ?>
                                            <div class="reponse-item">
                                                <div class="reponse-header">
                                                    <strong>
                                                        <?= htmlspecialchars($rep['nom'] ?? 'Admin') ?> 
                                                        <?= htmlspecialchars($rep['prenom'] ?? '') ?>
                                                    </strong>
                                                    <span class="reponse-date">
                                                        <?= date('d/m/Y H:i', strtotime($rep['date_reponse'])) ?>
                                                    </span>
                                                </div>
                                                <p class="reponse-message"><?= htmlspecialchars($rep['message']) ?></p>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

            </div>
        </div>

    </div>

    <!-- MODAL POUR AJOUTER UNE RÉPONSE -->
    <div class="modal-overlay" id="reponseModal" style="display:none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-reply"></i> Ajouter une réponse</h3>
                <button class="modal-close" onclick="closeReponseModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="reclamation-info-modal">
                    <p><strong>Réclamation #<span id="modalReclamationId"></span></strong></p>
                    <p id="modalReclamationSujet"></p>
                </div>
                <form id="reponseForm" onsubmit="submitReponse(event)">
                    <input type="hidden" id="reclamationIdInput" name="reclamationId">
                    <div class="form-group">
                        <label for="reponseMessage">Votre réponse <span class="required">*</span></label>
                        <textarea id="reponseMessage" name="message" placeholder="Écrivez votre réponse ici..." rows="6"></textarea>
                        <div class="char-counter"><span id="reponseCharCount">0</span> / 1000 caractères</div>
                    </div>
                    <div class="form-group">
                        <label for="reponseUserId">Votre ID utilisateur <span class="required">*</span></label>
                        <input type="text" id="reponseUserId" name="userId" placeholder="Entrez votre ID utilisateur" value="1">
                    </div>
                    <div class="modal-actions">
                        <button type="button" class="btn-cancel-modal" onclick="closeReponseModal()">
                            <i class="fas fa-times"></i> Annuler
                        </button>
                        <button type="submit" class="btn-submit-reponse">
                            <i class="fas fa-paper-plane"></i> Envoyer la réponse
                        </button>
                    </div>
                </form>
                <div class="loader" id="loaderReponse" style="display:none;">
                    <div class="spinner"></div>
                    <p>Envoi en cours...</p>
                </div>
                <div class="success-message" id="successMessageReponse" style="display:none;">
                    <i class="fas fa-check-circle"></i>
                    <h3>Réponse envoyée avec succès !</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- JS EXTERNE -->
    <script src="script.js"></script>

</body>
</html>
