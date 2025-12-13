// Fonction Participer
    function participer() {
        const msg = document.getElementById('message');
        msg.innerHTML = '✔ Inscription confirmée ! Vous recevrez un email de confirmation.';
        msg.style.display = 'block';
        setTimeout(() => msg.style.display = 'none', 6000);
    }

    // Fonction Favoris
    function ajouterFavoris(btn) {
        btn.classList.add('added');
        btn.innerHTML = '<i class="fas fa-heart"></i> Ajouté aux favoris !';
        const msg = document.getElementById('message');
        msg.innerHTML = '❤ Événement ajouté à vos favoris !';
        msg.style.display = 'block';
        setTimeout(() => msg.style.display = 'none', 4000);
    }