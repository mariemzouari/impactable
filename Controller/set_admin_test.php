<?php
// Temporary test helper - set admin session flag for local testing only
// USAGE: open http://localhost/test/Controller/set_admin_test.php in your browser

if (session_status() === PHP_SESSION_NONE) session_start();

// Set admin flag
$_SESSION['is_admin'] = 1;

// Friendly message with link back to the backoffice participants page
$back = '/test/View/Backoffice/view_event_participants.php';
$eventId = intval($_GET['id'] ?? 1);
$backWithId = $back . '?id=' . $eventId;
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Test Admin Session</title>
  <style>
    body{font-family: Arial, Helvetica, sans-serif;background:#f7f7f7;color:#222;padding:40px}
    .card{background:#fff;border-radius:8px;padding:24px;max-width:720px;margin:0 auto;box-shadow:0 6px 20px rgba(0,0,0,0.06)}
    a.btn{display:inline-block;padding:10px 16px;border-radius:6px;background:#5e6d3b;color:#fff;text-decoration:none;margin-top:12px}
    small{color:#666;display:block;margin-top:12px}
  </style>
</head>
<body>
  <div class="card">
    <h1>Session administrateur activée (test)</h1>
    <p>Le flag <code>$_SESSION['is_admin']</code> a été défini à <strong>1</strong> pour cette session.</p>
    <p>Cliquez ci-dessous pour retourner à la page des participants (avec l'ID d'événement <strong><?= $eventId ?></strong>).</p>
    <p><a class="btn" href="<?= htmlspecialchars($backWithId) ?>">Aller à la page Participants (événement <?= $eventId ?>)</a></p>
    <small>Important: Ce fichier est un utilitaire de test local. Supprimez-le après vérification pour éviter tout risque de sécurité.</small>
  </div>
</body>
</html>