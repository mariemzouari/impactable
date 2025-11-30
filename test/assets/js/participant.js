 function participer(btn, currentCount) {
        btn.innerText = "Inscrit ✓";
        btn.classList.add('inscrit');
        btn.disabled = true;

        const span = btn.parentElement.querySelector('.participants-count');
        span.innerText = (currentCount + 1) + " participants inscrits";

        const msg = btn.parentElement.nextElementSibling;
        msg.style.display = "block";
        setTimeout(() => msg.style.display = "none", 5000);
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('sidePanel').classList.remove('open');
        document.getElementById('panelOverlay').classList.remove('active');
        document.body.style.overflow = '';
    });
       // Ferme le menu au chargement
    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('sidePanel').classList.remove('open');
        document.getElementById('panelOverlay').classList.remove('active');
        document.body.style.overflow = '';
    });

    // Fonction "Se désinscrire" 100 % fonctionnelle
    function desinscrire(id, btn) {
        const badge = document.getElementById('badge' + id);
        badge.innerText = "Désinscrit";
        badge.classList.remove('badge-inscrit');
        badge.classList.add('badge-desinscrit');

        btn.innerText = "S'inscrire à nouveau";
        btn.onclick = function() { reinscrire(id, btn); };

        const msg = document.getElementById('msg' + id);
        msg.innerHTML = "✔ Vous vous êtes désinscrit de cet événement.";
        msg.style.display = "block";
        setTimeout(() => msg.style.display = "none", 5000);
    }

    // Fonction "S'inscrire à nouveau"
    function reinscrire(id, btn) {
        const badge = document.getElementById('badge' + id);
        badge.innerText = "Inscrit";
        badge.classList.remove('badge-desinscrit');
        badge.classList.add('badge-inscrit');

        btn.innerText = "Se désinscrire";
        btn.onclick = function() { desinscrire(id, btn); };

        const msg = document.getElementById('msg' + id);
        msg.innerHTML = "✔ Vous êtes à nouveau inscrit !";
        msg.style.display = "block";
        setTimeout(() => msg.style.display = "none", 5000);
    }