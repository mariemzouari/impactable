<?php
http_response_code(404);
require_once __DIR__ . '/../templates/header.php';
?>
<div style="text-align:center; padding: 6rem;">
    <h1>404 — Page non trouvée</h1>
    <p>La page demandée est introuvable. Vérifiez l'URL ou retournez à l'accueil.</p>
    <a href="/impactable_integration/View/Frontoffice/Index.php" class="btn primary">Retour à l'accueil</a>
</div>
<?php require_once __DIR__ . '/../templates/footer.php'; ?>