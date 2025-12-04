<?php
class EmailController {
    public function envoyerRecuDon($email, $nom, $montant, $don_id, $campagne_titre) {
        // Simulation d'envoi d'email avec template professionnel
        $sujet = "ImpactAble - Confirmation de votre don";
        $message = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; }
                .header { background: #4CAF50; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; }
                .footer { background: #f4f4f4; padding: 10px; text-align: center; }
            </style>
        </head>
        <body>
            <div class='header'>
                <h1>ImpactAble</h1>
                <h2>Merci pour votre don !</h2>
            </div>
            <div class='content'>
                <p>Cher(e) $nom,</p>
                <p>Votre don de <strong>$montant TND</strong> a été enregistré avec succès.</p>
                <p><strong>Campagne :</strong> $campagne_titre</p>
                <p><strong>Référence du don :</strong> DON-$don_id</p>
                <p>Votre contribution fait la différence !</p>
            </div>
            <div class='footer'>
                <p>ImpactAble - Plateforme d'impact social</p>
            </div>
        </body>
        </html>
        ";
        
        // En-têtes pour l'email HTML
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: no-reply@impactable.org" . "\r\n";
        
        // Envoyer l'email (décommentez pour activer)
        // return mail($email, $sujet, $message, $headers);
        
        return mail($email, $sujet, $message, $headers);
    }
}
?>