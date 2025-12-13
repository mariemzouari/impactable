<?php
session_start();
// Inclusion des fichiers de configuration et modèles
require_once __DIR__ . '/../../../Config.php';
require_once __DIR__ . '/../../../Model/EventModel.php';
require_once __DIR__ . '/../../../Model/ParticipationModel.php';
require_once __DIR__ . '/../../../Model/FavoritesModel.php'; // Nouveau modèle pour les favoris

$config = new Config();
$db = $config->getPDO();

$eventModel = new EventModel($db);
$participationModel = new ParticipationModel($db);
$favoritesModel = new FavoritesModel($db); // Instanciation du modèle Favorites

// 1. Récupération de l'ID de l'événement
$eventId = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($eventId === 0) {
    echo "ID d'événement manquant.";
    exit;
}

// 2. Récupération des détails de l'événement
$event = $eventModel->getById($eventId);
if (!$event) {
    echo "Événement non trouvé.";
    exit;
}

$userId = $_SESSION['user_id'] ?? null;

// Vérification du statut favori
$isFavorited = false;
if ($userId) {
    $favoriteDetails = $favoritesModel->findOneBy([
        'id_utilisateur' => $userId,
        'id_evenement' => $eventId,
    ]);
    if ($favoriteDetails) {
        $isFavorited = true;
    }
}

// ==============================================================================
// CONFIGURATION DES CHEMINS (C'EST ICI QUE CA SE JOUE)
// ==============================================================================

// Mettez ici le nom de votre dossier projet dans htdocs (ex: '/test1' ou '/ImpactAble')
// Si votre URL est localhost/test1/..., laissez '/test1'
$nomDossierSite = '/test1'; 

// Chemin vers le dossier assets
$assetsUrl = $nomDossierSite . '/assets';

// --- A. Image Principale de l'événement ---
$imageFilename = $event['image'] ?? '';
// Par défaut
$mainImageUrl = $assetsUrl . '/images/placeholder.jpg';

// On utilise le chemin physique (__DIR__) pour vérifier si le fichier existe
if (!empty($imageFilename) && file_exists(__DIR__ . '/../../../assets/uploads/' . $imageFilename)) {
    $mainImageUrl = $assetsUrl . '/uploads/' . $imageFilename;
}

// --- B. Logos des Catégories ---
$category = htmlspecialchars($event['categorie']);
$logoPath = $assetsUrl . '/images/logo_default.jpeg'; 
$metierIcon = '<i class="fas fa-briefcase" style="color:#6b4b44;"></i>';

switch (strtolower(trim($category))) {
    case 'education':
    case 'atelier':
    case 'formation':
    case 'cours':
        $logoPath = $assetsUrl . '/images/logo_education.png';
        $metierIcon = '<i class="fas fa-graduation-cap" style="color:#b47b47;"></i>';
        break;

    case 'environment':
    case 'nature':
    case 'jardinage':
    case 'écologie':
        $logoPath = $assetsUrl . '/images/logo_environment.jpeg';
        $metierIcon = '<i class="fas fa-leaf" style="color:#5e6d3b;"></i>';
        break;

    case 'health':
    case 'santé':
    case 'garde 2':
    case 'sport':
    case 'médical':
        $logoPath = $assetsUrl . '/images/logo_health.jpg';
        $metierIcon = '<i class="fas fa-heartbeat" style="color:#d9534f;"></i>';
        break;
}

// ==============================================================================
// GESTION PARTICIPATION & FAVORIS
// ==============================================================================
$isParticipating = false;
$participationDetails = null;
if ($userId) {
    $participationDetails = $participationModel->findOneBy([
        'id_utilisateur' => $userId,
        'id_evenement' => $eventId,
        'statut' => ['inscrit', 'confirmé']
    ]);
    if ($participationDetails) {
        $isParticipating = true;
    }
}

$isFavorited = false;
if ($userId) {
    $favoriteDetails = $participationModel->findOneBy([
        'id_utilisateur' => $userId,
        'id_evenement' => $eventId,
        'statut' => 'favori'
    ]);
    if ($favoriteDetails) {
        $isFavorited = true;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($event['titre']) ?> - ImpactAble</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="<?= $assetsUrl ?>/css/style.css">
    <link rel="stylesheet" href="<?= $assetsUrl ?>/css/event-detail.css">
</head>
<body>
<div class="container">
    <header class="site-header" role="banner">
        <div class="brand">
            <button class="nav-toggle" id="navToggle" aria-label="Ouvrir le menu"><i class="fas fa-bars"></i></button>
            <div class="logo">
                <img src="<?= $assetsUrl ?>/images/logo.png" alt="ImpactAble" class="logo-image">
            </div>
        </div>
        <div class="header-actions">
            <?php if(!$userId): ?>
                <button class="btn ghost">Se connecter</button>
                <button class="btn primary">S'inscrire</button>
            <?php else: ?>
                <button class="btn ghost">Mon Profil</button>
            <?php endif; ?>
        </div>
    </header>

    <div class="side-panel" id="sidePanel">
        <div class="panel-header">
            <div class="logo">
                <img src="<?= $assetsUrl ?>/images/logo.png" alt="ImpactAble" class="logo-image">
            </div>
            <button class="panel-close" id="panelClose"><i class="fas fa-times"></i></button>
        </div>
        <nav class="panel-nav">
            <div class="nav-section">
                <div class="nav-title">Navigation</div>
                <a href="<?= $nomDossierSite ?>/View/Frontoffice/index.php" class="nav-link"><i class="fas fa-home"></i> <span>Accueil</span></a>
                <a href="<?= $nomDossierSite ?>/View/Frontoffice/events-list.php" class="nav-link active"><i class="fas fa-calendar-alt"></i> <span>Événements</span></a>
            </div>
        </nav>
        <div class="panel-footer">
            <div class="user-profile">
                <div class="user-avatar">VS</div>
                <div class="user-info">
                    <h4><?= $userId ? 'Membre' : 'Visiteur' ?></h4>
                </div>
            </div>
        </div>
    </div>
    <div class="panel-overlay" id="panelOverlay"></div>

    <section class="section">
        <div class="hero container" style="align-items:start;">
            
            <div class="hero-content">
                <h1><?php echo htmlspecialchars($event['titre']); ?></h1>
                
                <div class="lead" style="display: flex; flex-direction: column; gap: 10px; font-size: 0.95rem; color: #6b4b44; margin-bottom: 20px;">
                    
                    <div>
                        <i class="fas fa-calendar-alt" style="color:#b47b47; width:20px;"></i> 
                        Du <?php echo htmlspecialchars(date('d M Y à H:i', strtotime($event['date_debut']))); ?> au <?php echo htmlspecialchars(date('d M Y à H:i', strtotime($event['date_fin']))); ?>
                    </div>

                    <div style="display:flex; align-items:center;">
                        <span style="width:20px; text-align:center; margin-right:5px;"><?= $metierIcon ?></span>
                        
                        <img src="<?= $logoPath ?>" alt="<?= $category ?>" 
                             style="height:20px; width:20px; margin-right:8px; object-fit:contain;"
                             onerror="this.src='<?= $assetsUrl ?>/images/logo_default.jpeg'">
                             
                        <span style="background:rgba(180,123,71,0.1); padding:2px 8px; border-radius:4px; font-weight:600;">
                            <?php echo $category; ?>
                        </span>
                    </div>

                    <div>
                        <i class="fas fa-map-marker-alt" style="color:#e74c3c; width:20px;"></i> 
                        <?php echo htmlspecialchars($event['location'] ?? 'Lieu non précisé'); ?>
                    </div>
                </div>

                <div style="background:var(--card-bg); padding:20px; border-radius:var(--radius); margin-bottom:20px; text-align:left;">
                    <h2 style="margin-top:0;">Description</h2>
                    <p style="white-space:pre-line; line-height:1.7; color:var(--muted);"><?php echo htmlspecialchars($event['description']); ?></p>
                </div>

                <?php if(!empty($event['location'])): ?>
                <div style="background:var(--card-bg); padding:20px; border-radius:var(--radius); margin-bottom:20px; text-align:left;">
                    <h2 style="margin-top:0;">Lieu de l'événement</h2>
                    <p><?= htmlspecialchars($event['location']) ?></p>
                    <div id="map-container" style="width: 100%; height: 300px; background-color: #e0e0e0; display: flex; align-items: center; justify-content: center; color: #555; border-radius: 8px; overflow: hidden;">
                        <iframe
  src="https://www.google.com/maps/embed?q=R6QP%2BVMQ%2C%20Tunis"
  width="100%"
  height="300"
  style="border:0;"
  allowfullscreen=""
  loading="lazy"
  referrerpolicy="no-referrer-when-downgrade">
</iframe>
                    </div>
                    <p style="text-align: center; margin-top: 10px;">
                        <a href="https://www.google.com/maps/search/?api=1&query=<?= urlencode($event['location']) ?>" target="_blank" style="color: #5e6d3b; text-decoration: none;">
                            <i class="fas fa-external-link-alt"></i> Ouvrir dans Google Maps
                        </a>
                    </p>
                </div>
                <?php endif; ?>

                <div class="hero-cta">
                    <?php if ($isParticipating): ?>
                        <p class="participation-status"><i class="fas fa-check-circle"></i> Vous participez à cet événement !</p>
                        <button type="button" onclick="cancelParticipation(<?= $participationDetails['id'] ?>)" class="btn primary" style="background: #e74c3c;"><i class="fas fa-times"></i> Annuler participation</button>
                    <?php else: ?>
                        <button type="button" onclick="openParticipationModal()" class="btn primary"><i class="fas fa-heart"></i> Participer</button>
                    <?php endif; ?>

                    <?php if ($userId): ?>
                        <button id="favoriteButton" type="button" class="btn ghost <?= $isFavorited ? 'favorited' : '' ?>" data-event-id="<?= htmlspecialchars($eventId) ?>">
                            <i class="far fa-star"></i> <span class="button-text"><?= $isFavorited ? 'Retirer des favoris' : 'Ajouter aux favoris' ?></span>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
            
            <div style="width: 100%; display:flex; justify-content:center;">
                <img 
                    src="<?= htmlspecialchars($mainImageUrl) ?>" 
                    alt="<?= htmlspecialchars($event['titre']) ?>" 
                    class="hero-img" 
                    style="border-radius:12px; box-shadow:0 5px 15px rgba(0,0,0,0.1); width: 100%; height: auto; object-fit: cover; max-height: 400px; background-color: #f0f0f0; min-height: 200px;"
                    onerror="this.onerror=null; this.src='<?= $assetsUrl ?>/images/logo_default.jpeg';"
                >
            </div>
        </div>
        
        <div id="statusMessage" class="success-msg"></div>
        
        <div id="participationModal" class="modal-backdrop hidden" aria-hidden="true">
            <div class="modal">
                <div class="modal-header">
                    <h3>Inscription à l'événement</h3>
                    <button class="modal-close" type="button" onclick="closeParticipationModal()"><i class="fas fa-times"></i></button>
                </div>
                <div class="modal-body">
                    <form id="participationForm">
                        <input type="hidden" name="event_id" value="<?= intval($eventId) ?>">
                        <input type="hidden" name="id_utilisateur" value="<?= $userId ? intval($userId) : '' ?>">
                        
                        <div class="form-group">
                            <label for="prenom">Prénom</label>
                            <input type="text" id="prenom" name="prenom" class="form-input" placeholder="Votre prénom" />
                        </div>
                        <div class="form-group">
                            <label for="nom">Nom</label>
                            <input type="text" id="nom" name="nom" class="form-input" placeholder="Votre nom" />
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" class="form-input" placeholder="Votre email" />
                        </div>

                        <div class="form-group">
                            <label for="num_tel">Numéro de téléphone</label>
                            <input type="text" id="num_tel" name="num_tel" class="form-input" placeholder="Votre numéro de téléphone" />
                        </div>
                        <div class="form-group">
                            <label for="num_identite">Numéro d'identité</label>
                            <input type="text" id="num_identite" name="num_identite" class="form-input" placeholder="Votre numéro d'identité" />
                        </div>

                        <div class="form-group">
                            <label for="nombre_accompagnants">Nombre d'accompagnants</label>
                            <input type="number" id="nombre_accompagnants" name="nombre_accompagnants" class="form-input" value="0" min="0" />
                        </div>
                        <div class="form-group">
                            <label for="besoins_accessibilite">Besoins d'accessibilité</label>
                            <select id="besoins_accessibilite" name="besoins_accessibilite" class="form-input">
                                <option value="aucun">Aucun besoin</option>
                                <option value="lsf">LSF (Langue des signes)</option>
                                <option value="pmr">Accès PMR (Fauteuil)</option>
                            </select>
                        </div>
                        
                        <div class="form-footer">
                            <button type="button" class="btn-modal secondary" onclick="closeParticipationModal()">Annuler</button>
                            <button type="submit" class="btn-modal primary">Valider</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <footer class="site-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <h3>ImpactAble</h3>
                    <p class="text-muted">Plateforme dédiée à l'inclusion.</p>
                </div>
                <div class="footer-column">
                    <h3>Contact</h3>
                    <div class="footer-links">
                        <a href="mailto:contact@impactable.org">contact@impactable.org</a>
                        <a href="#">Tunis, Tunisia</a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>© <?= date('Y'); ?> ImpactAble — Tous droits réservés.</p>
            </div>
        </div>
    </footer>
</div>

<script src="<?= $assetsUrl ?>/js/script.js"></script>
<script>
    const statusMessageElement = document.getElementById('statusMessage');
    const userId = <?= json_encode($userId); ?>;
    const controllerUrl = '<?= $nomDossierSite ?>/Controller/PublicParticipationController.php';

    function showStatusMessage(message, isSuccess = true) {
        statusMessageElement.textContent = message;
        statusMessageElement.style.color = isSuccess ? '#27ae60' : '#e74c3c';
        statusMessageElement.style.display = 'block';
        setTimeout(() => {
            statusMessageElement.style.display = 'none';
        }, 5000);
    }

    function openParticipationModal() {
        const prenomField = document.getElementById('prenom');
        const nomField = document.getElementById('nom');
        const emailField = document.getElementById('email');

        if (!userId) {
            prenomField.value = '';
            nomField.value = '';
            emailField.value = '';
            prenomField.readOnly = false;
            nomField.readOnly = false;
            emailField.readOnly = false;
        } else {
            prenomField.readOnly = false;
            nomField.readOnly = false;
            emailField.readOnly = false;
        }

        const m = document.getElementById('participationModal');
        m.classList.remove('hidden');
        m.setAttribute('aria-hidden', 'false');
    }

    function closeParticipationModal() {
        const m = document.getElementById('participationModal');
        m.classList.add('hidden');
        m.setAttribute('aria-hidden', 'true');
    }

    async function cancelParticipation(participationId) {
        if (!confirm('Êtes-vous sûr ?')) return;
        
        console.log('Attempting to cancel participation for ID:', participationId); // Ligne de débogage

        try {
            const response = await fetch(controllerUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'cancel_participation', id: participationId })
            });
            const text = await response.text();
            console.log('Server response (raw text):', text); // Ligne de débogage

            try {
                const result = JSON.parse(text);
                console.log('Server response (parsed JSON):', result); // Ligne de débogage

                if (result.success) {
                    location.reload();
                } else {
                    showStatusMessage('Erreur : ' + (result.error || 'Inconnue'), false);
                }
            } catch(e) {
                console.error("Réponse serveur invalide:", text);
                showStatusMessage('Erreur serveur (JSON invalide)', false);
            }
        } catch (e) {
            console.error('Network or fetch error:', e); // Ligne de débogage
            showStatusMessage('Erreur réseau', false);
        }
    }

    document.getElementById('participationForm').addEventListener('submit', async function(e){
        e.preventDefault();
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData.entries());
        data.action = 'participate_with_details';

        try {
            const response = await fetch(controllerUrl, { 
                method: 'POST', 
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            const text = await response.text();
            try {
                const result = JSON.parse(text);
                if (result.success) {
                    showStatusMessage('Inscription réussie !', true);
                    closeParticipationModal();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showStatusMessage(result.error || 'Erreur', false);
                }
            } catch(e) {
                console.error("Réponse serveur invalide:", text);
                showStatusMessage('Erreur serveur', false);
            }
        } catch(err) {
            showStatusMessage('Erreur réseau', false);
        }
    });
    
    document.addEventListener('keydown', (e) => { if(e.key === 'Escape') closeParticipationModal(); });

    // Gestion des favoris
    document.addEventListener('DOMContentLoaded', () => {
        const favoriteButton = document.getElementById('favoriteButton');
        if (favoriteButton) {
            favoriteButton.addEventListener('click', async () => {
                const eventId = favoriteButton.dataset.eventId;
                const isFavorited = favoriteButton.classList.contains('favorited');
                const action = isFavorited ? 'remove_favorite' : 'add_favorite';

                try {
                    const response = await fetch(`../../Controller/FavoritesController.php`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `action=${action}&event_id=${eventId}`
                    });
                    const result = await response.json();

                    if (result.success) {
                        if (action === 'add_favorite') {
                            favoriteButton.classList.add('favorited');
                            favoriteButton.querySelector('.button-text').textContent = 'Retirer des favoris';
                            favoriteButton.querySelector('i').classList.remove('far');
                            favoriteButton.querySelector('i').classList.add('fas'); // Étoile pleine
                            alert('Événement ajouté aux favoris!');
                        } else {
                            favoriteButton.classList.remove('favorited');
                            favoriteButton.querySelector('.button-text').textContent = 'Ajouter aux favoris';
                            favoriteButton.querySelector('i').classList.remove('fas');
                            favoriteButton.querySelector('i').classList.add('far'); // Étoile contour
                            alert('Événement retiré des favoris!');
                        }
                    } else {
                        alert('Erreur: ' + (result.message || 'Action échouée.'));
                        console.error('Favorite action failed:', result.message);
                    }
                } catch (error) {
                    console.error('Erreur AJAX pour les favoris:', error);
                    alert('Une erreur réseau est survenue.');
                }
            });
        }
    });
</script>
</body>
</html>