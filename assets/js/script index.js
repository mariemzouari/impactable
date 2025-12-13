// Fonction pour les boutons "Participer" dans la timeline
    function participer(btn, currentCount) {
        btn.innerText = "Inscrit ✓";
        btn.classList.add('inscrit');
        btn.disabled = true;

        const span = btn.nextElementSibling.nextElementSibling;
        span.innerText = (currentCount + 1) + " participants inscrits";

        const msg = btn.parentElement.nextElementSibling;
        msg.style.display = 'block';
        setTimeout(() => msg.style.display = 'none', 5000);
    }

    // Ferme le menu au chargement de la page
    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('sidePanel').classList.remove('open');
        document.getElementById('panelOverlay').classList.remove('active');
        document.body.style.overflow = '';
    });