 // Ferme le menu au chargement de la page
    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('sidePanel').classList.remove('open');
        document.getElementById('panelOverlay').classList.remove('active');
        document.body.style.overflow = '';
    });