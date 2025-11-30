<?php
require_once __DIR__ . '/../../../config/Config.php';
require_once __DIR__ . '/../../../Model/EventModel.php';

$config = new Config();
$db = $config->getPDO();

$eventModel = new EventModel($db);

$eventId = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($eventId === 0) {
    echo "ID d'événement manquant.";
    exit;
}

$event = $eventModel->getById($eventId);
if (!$event) {
    echo "Événement non trouvé.";
    exit;
}

$userId = 1; // Placeholder for logged-in user ID
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($event['title']) ?> - ImpactAble</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/style rayen.css">
    <style>
        /* Local event-detail tweaks to match site theme */
        .container { max-width: 1100px; margin: 0 auto; padding: 24px; }
        .site-header { display:flex; justify-content:space-between; align-items:center; padding:12px 0; }
        .brand .logo-image { height:40px; }
        .hero-img { width:100%; height:360px; object-fit:cover; border-radius:12px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        .feature-list { display:flex; gap:12px; justify-content:center; margin-bottom:1rem; }
        .badge { background:var(--light-sage, #e1e8c9); color:var(--moss, #5e6d3b); padding:8px 12px; border-radius:999px; font-weight:700; display:inline-flex; gap:8px; align-items:center; }
        .text-muted { color: #6b6b6b; }
        .btn-participer, .btn-primary { background: var(--moss, #5e6d3b); color: white; border: none; padding: 12px 18px; border-radius: 10px; cursor:pointer; font-weight:700; }
        .btn-favoris { background: transparent; color: var(--moss, #5e6d3b); border: 2px solid var(--moss, #5e6d3b); padding: 10px 14px; border-radius:10px; font-weight:700; }
        .btn-favoris i { margin-right:8px; }
        .success-msg { margin-top:12px; color: var(--brown, #4b2e16); font-weight:700; }
        /* Modal */
        #participationModal { align-items: center; justify-content: center; }
        .form-input { width:100%; padding:10px; border-radius:8px; border:1px solid rgba(0,0,0,0.08); }
        @media (max-width:768px){ .hero-img{height:220px} .feature-list{flex-wrap:wrap} .container{padding:12px} }
    </style>
</head>
<body>
<div class="container">
    <!-- Header COMPLET -->
    <header class="site-header" role="banner">
        <div class="brand">
            <button class="nav-toggle" id="navToggle" aria-label="Ouvrir le menu"><i class="fas fa-bars"></i></button>
            <div class="logo">
                <img src="/impactable/assets/images/logo.png" alt="ImpactAble" class="logo-image">
            </div>
        </div>
        <div class="header-actions">
            <button class="btn ghost">Se connecter</button>
            <button class="btn primary">S'inscrire</button>
        </div>
    </header>

    <!-- Menu latéral COMPLET -->
    <div class="side-panel" id="sidePanel">
        <div class="panel-header">
            <div class="logo">
                <img src="/impactable/assets/images/logo.png" alt="ImpactAble" class="logo-image">
            </div>
            <button class="panel-close" id="panelClose"><i class="fas fa-times"></i></button>
        </div>
        <nav class="panel-nav">
            <div class="nav-section">
                <div class="nav-title">Navigation</div>
                <a href="/impactable/View/Frontoffice/index.php" class="nav-link"><i class="fas fa-home"></i> <span>Accueil</span></a>
                <a href="#" class="nav-link"><i class="fas fa-briefcase"></i> <span>Opportunités</span></a>
                <a href="#" class="nav-link active"><i class="fas fa-calendar-alt"></i> <span>Événements</span></a>
                <a href="#" class="nav-link"><i class="fas fa-hand-holding-heart"></i> <span>Campagnes</span></a>
                <a href="#" class="nav-link"><i class="fas fa-book"></i> <span>Ressources</span></a>
                <a href="#" class="nav-link"><i class="fas fa-comments"></i> <span>Forum</span></a>
                <a href="#" class="nav-link"><i class="fas fa-comment-alt"></i> <span>Réclamations</span></a>
            </div>
        </nav>
        <div class="panel-footer">
            <div class="user-profile">
                <div class="user-avatar">VS</div>
                <div class="user-info">
                    <h4>Visiteur</h4>
                    <p>Connectez-vous pour plus de fonctionnalités</p>
                </div>
            </div>
        </div>
    </div>
    <div class="panel-overlay" id="panelOverlay"></div>

    <!-- Contenu de la page détail -->
    <section class="section">
        <div class="hero container" style="align-items:start;">
            <div class="hero-content">
                <h1><?php echo htmlspecialchars($event['titre']); ?></h1>
                <p class="lead"><i class="fas fa-calendar-alt"></i> <?php echo htmlspecialchars($event['date_event']); ?> — <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($event['categorie']); ?></p>

                <div style="background:var(--card-bg); padding:20px; border-radius:var(--radius); margin-bottom:20px; text-align:left;">
                    <h2 style="margin-top:0;">Description</h2>
                    <p style="white-space:pre-line; line-height:1.7; color:var(--muted);"><?php echo htmlspecialchars($event['description']); ?></p>
                </div>

                <div class="hero-cta">
                    <?php if ($isParticipating): ?>
                        <p class="participation-status">Vous participez à cet événement.</p>
                        <form action="../../../Controller/ParticipationController.php" method="POST">
                            <input type="hidden" name="action" value="cancel_participation">
                            <input type="hidden" name="id_evenement" value="<?php echo $eventId; ?>">
                            <input type="hidden" name="id_utilisateur" value="<?php echo $userId; ?>">
                            <button type="submit" class="btn primary">Annuler participation</button>
                        </form>
                    <?php else: ?>
                        <form action="../../../Controller/ParticipationController.php" method="POST">
                            <input type="hidden" name="action" value="participate">
                            <input type="hidden" name="id_evenement" value="<?php echo $eventId; ?>">
                            <input type="hidden" name="id_utilisateur" value="<?php echo $userId; ?>">
                            <button type="submit" class="btn primary">Participer</button>
                        </form>
                    <?php endif; ?>

                    <!-- Placeholder for admin/creator only edit button -->
                    <form action="../../Backoffice/edit_event.php" method="GET" style="display:inline-block; margin-left: 10px;">
                        <input type="hidden" name="id" value="<?php echo $eventId; ?>">
                        <button type="submit" class="btn ghost">Modifier l'événement</button>
                    </form>
                </div>
            </div>
            <div>
                <img src="<?= $event['img'] ?>" alt="<?= htmlspecialchars($event['title']) ?>" class="hero-img">
            </div>
        </div>
            <div id="message" class="success-msg"></div>
            
            <!-- Participation modal -->
            <div id="participationModal" class="modal-backdrop" aria-hidden="true" style="display:none;">
                <div class="modal">
                    <div class="modal-header">
                        <h3>Inscription à l'événement</h3>
                        <button class="modal-close" type="button" onclick="closeParticipation()"><i class="fas fa-times"></i></button>
                    </div>
                    <div class="modal-body">
                        <form id="participationForm">
                            <input type="hidden" name="id_evenement" value="<?= intval($id) ?>">
                            <div class="form-group">
                                <label>Nom (optionnel)</label>
                                <input type="text" name="nom" class="input" />
                            </div>
                            <div class="form-group">
                                <label>Email (optionnel)</label>
                                <input type="email" name="email" class="input" />
                            </div>
                            <div class="form-group">
                                <label>Nombre d'accompagnants</label>
                                <input type="number" name="nombre_accompagnants" class="input" value="0" min="0" />
                            </div>
                            <div class="form-footer">
                                <button type="button" class="btn secondary" onclick="closeParticipation()">Annuler</button>
                                <button type="submit" class="btn primary">Valider</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer COMPLET -->
    <footer class="site-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <h3>ImpactAble</h3>
                    <p class="text-muted">Plateforme dédiée à l'inclusion et à l'impact social.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                <div class="footer-column">
                    <h3>Navigation</h3>
                    <div class="footer-links">
                        <a href="/impactable/View/Frontoffice/index.php">Accueil</a>
                        <a href="#">Opportunités</a>
                        <a href="#">Événements</a>
                        <a href="#">Campagnes</a>
                        <a href="#">Ressources</a>
                        <a href="#">Forum</a>
                    </div>
                </div>
                <div class="footer-column">
                    <h3>Légal</h3>
                    <div class="footer-links">
                        <a href="#">Mentions légales</a>
                        <a href="#">Politique de confidentialité</a>
                        <a href="#">Conditions d'utilisation</a>
                        <a href="#">Accessibilité</a>
                    </div>
                </div>
                <div class="footer-column">
                    <h3>Contact</h3>
                    <div class="footer-links">
                        <a href="mailto:contact@impactable.org">contact@impactable.org</a>
                        <a href="tel:+21612345678">+216 12 345 678</a>
                        <a href="#">Tunis, Tunisia</a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>© <?= $currentYear ?> ImpactAble — Tous droits réservés.</p>
            </div>
        </div>
    </footer>
</div>

<script src="/impactable/assets/js/script.js"></script>
<script src="/impactable/assets/js/script details.js"></script>
<script>
function participer(){
    const m = document.getElementById('participationModal');
    m.classList.add('active');
    m.style.display = 'flex';
    m.setAttribute('aria-hidden','false');
}
function closeParticipation(){
    const m = document.getElementById('participationModal');
    m.classList.remove('active');
    m.style.display = 'none';
    m.setAttribute('aria-hidden','true');
}

// Robust fetch helper to show HTTP/server response bodies for debugging
async function fetchJson(url, options = {}){
    const res = await fetch(url, options);
    const text = await res.text();
    if(!res.ok){
        throw new Error(`HTTP ${res.status} ${res.statusText}: ${text.substring(0,500)}`);
    }
    try {
        return JSON.parse(text);
    } catch(e){
        throw new Error('Server returned non-JSON response: ' + text.substring(0,500));
    }
}

document.getElementById('participationForm').addEventListener('submit', async function(e){
    e.preventDefault();
    const form = e.target;
    const data = new FormData(form);
    // if you have logged-in user, set id_utilisateur accordingly. default to 1
    data.append('id_utilisateur', 1);

    // Use absolute path to avoid relative resolution issues
    const url = '/test/Controller/participation_submit.php';
    try{
        const json = await fetchJson(url, { method: 'POST', body: data });
        if(json.success){
            document.getElementById('message').innerText = json.message || 'Inscription enregistrée';
            closeParticipation();
        } else {
            document.getElementById('message').innerText = json.error || 'Erreur';
        }
    } catch(err){
        console.error('Participation submit error:', err);
        document.getElementById('message').innerText = 'Erreur réseau — ' + (err.message || err);
    }
});
// Close modal when clicking on backdrop
document.getElementById('participationModal').addEventListener('click', function(e){
    if(e.target === this) closeParticipation();
});

// Close modal on Escape
document.addEventListener('keydown', function(e){ if(e.key === 'Escape') closeParticipation(); });
</script>
</body>
</html>