function participer(buttonElement, eventId, userId) {
    console.log('Debug: Appui sur participer. Event ID:', eventId, 'User ID from JS:', userId); // Ligne de débogage
    
    const originalButtonText = buttonElement.innerHTML;
    buttonElement.disabled = true;
    buttonElement.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Inscription...';

    // Référence aux conteneurs de messages spécifiques à l'événement
    // Cela suppose que le bouton se trouve dans un div parent qui contient aussi .success-msg et .error-msg
    const parentEventContainer = buttonElement.closest('.event-item') || buttonElement.closest('.card-body');
    const successMsg = parentEventContainer ? parentEventContainer.querySelector('.success-msg') : null;
    const errorMsg = parentEventContainer ? parentEventContainer.querySelector('.error-msg') : null;
    const participantsCountSpan = parentEventContainer ? parentEventContainer.querySelector('.participants-count') : null;

    // Fonction d'aide pour afficher les messages
    const displayMessage = (element, message, isError = false) => {
        if (element) {
            element.textContent = message;
            element.style.color = isError ? 'red' : '#27ae60';
            element.style.display = 'block';
            setTimeout(() => element.style.display = 'none', 5000);
        } else if (isError) {
            alert(message);
        }
    };

    if (!userId) {
        displayMessage(errorMsg, 'Veuillez vous connecter pour participer.', true);
        buttonElement.disabled = false;
        buttonElement.innerHTML = originalButtonText;
        return;
    }

    const participationUrl = '../../Controller/ParticipationController.php';

    fetch(participationUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ event_id: eventId, user_id: userId }) // Envoyer userId explicitement
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayMessage(successMsg, data.message || '✔ Vous êtes inscrit à cet événement !');
            if (participantsCountSpan) {
                // Pour les événements dynamiques, incrémenter le compteur. Pour les statiques, juste changer le texte.
                if (participantsCountSpan.textContent.includes('participants inscrits')) {
                    let currentCount = parseInt(participantsCountSpan.textContent.match(/\d+/)[0], 10);
                    participantsCountSpan.textContent = `${currentCount + 1} participants inscrits`;
                } else {
                    participantsCountSpan.textContent = 'Vous participez'; // Pour le cas "Vous participez"
                }
            }
            buttonElement.innerHTML = '<i class="fas fa-check"></i> Inscrit';
            buttonElement.classList.add('btn-success');
            buttonElement.disabled = true; // Garder désactivé pour éviter les réinscriptions
            if (errorMsg) errorMsg.style.display = 'none'; // Cacher toute erreur précédente
        } else {
            const errorMessage = data.error || data.message || 'Erreur inconnue.';
            displayMessage(errorMsg, 'Erreur lors de l\'inscription : ' + errorMessage, true);
            buttonElement.innerHTML = originalButtonText;
            buttonElement.disabled = false;
            if (successMsg) successMsg.style.display = 'none'; // Cacher le message de succès
        }
    })
    .catch(error => {
        console.error('Problème avec l\'opération fetch :', error);
        const errorMessage = 'Erreur réseau lors de l\'inscription : ' + error.message;
        displayMessage(errorMsg, errorMessage, true);
        buttonElement.innerHTML = originalButtonText;
        buttonElement.disabled = false;
        if (successMsg) successMsg.style.display = 'none';
    });
}

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

// Ce script gère l'ouverture de la modale de participation détaillée et la soumission du formulaire.

const globalStatusMessageElement = document.getElementById('globalStatusMessage');

function showGlobalStatusMessage(message, isSuccess = true) {
    if (globalStatusMessageElement) {
        globalStatusMessageElement.textContent = message;
        globalStatusMessageElement.className = 'global-status-message'; // Reset classes
        if (isSuccess) {
            globalStatusMessageElement.classList.add('success');
        } else {
            globalStatusMessageElement.classList.add('error'); // Assuming you have .error style
        }
        globalStatusMessageElement.style.display = 'block';
        setTimeout(() => {
            globalStatusMessageElement.style.display = 'none';
            globalStatusMessageElement.textContent = '';
        }, 5000);
    } else {
        alert(message); // Fallback if no global message element
    }
}

async function openDetailedParticipationModal(eventId, userId) {
    const modal = document.getElementById('detailedParticipationModal');
    const form = document.getElementById('detailedParticipationForm');

    if (!modal || !form) {
        console.error('Modal or form not found.');
        return;
    }

    // Réinitialiser le formulaire
    form.reset();

    // Remplir les champs cachés
    document.getElementById('modalEventId').value = eventId;
    document.getElementById('modalUserId').value = userId;

    // Si l'utilisateur est connecté, tenter de pré-remplir son nom, prénom, email, numéro de téléphone, et numéro d'identité
    if (userId && userId !== 'null') { // 'null' peut être passé comme string si userId est null en PHP
        try {
            const response = await fetch(`../../Controller/UserController.php?action=get_user_details&id=${userId}`);
            const userData = await response.json();
            if (userData.success && userData.data) {
                document.getElementById('modalPrenom').value = userData.data.prenom || '';
                document.getElementById('modalNom').value = userData.data.nom || '';
                document.getElementById('modalEmail').value = userData.data.email || '';
                document.getElementById('modalNumTel').value = userData.data.num_tel || ''; // New field
                document.getElementById('modalNumIdentite').value = userData.data.num_identite || ''; // New field
            } else {
                document.getElementById('modalPrenom').value = '';
                document.getElementById('modalNom').value = '';
                document.getElementById('modalEmail').value = '';
                document.getElementById('modalNumTel').value = ''; // New field
                document.getElementById('modalNumIdentite').value = ''; // New field
            }
            // Mettre les champs en lecture seule si l'utilisateur est connecté
            document.getElementById('modalPrenom').readOnly = true;
            document.getElementById('modalNom').readOnly = true;
            document.getElementById('modalEmail').readOnly = true;
            document.getElementById('modalNumTel').readOnly = true; // New field
            document.getElementById('modalNumIdentite').readOnly = true; // New field

        } catch (error) {
            console.error('Erreur lors du pré-remplissage des données utilisateur:', error);
            // Rendre éditable en cas d'erreur de chargement
            document.getElementById('modalPrenom').readOnly = false;
            document.getElementById('modalNom').readOnly = false;
            document.getElementById('modalEmail').readOnly = false;
            document.getElementById('modalNumTel').readOnly = false; // New field
            document.getElementById('modalNumIdentite').readOnly = false; // New field
        }
    } else {
        // Rendre les champs éditables pour les utilisateurs non connectés
        document.getElementById('modalPrenom').readOnly = false;
        document.getElementById('modalNom').readOnly = false;
        document.getElementById('modalEmail').readOnly = false;
        document.getElementById('modalNumTel').readOnly = false; // New field
        document.getElementById('modalNumIdentite').readOnly = false; // New field
    }

    modal.classList.add('active');
}

function closeDetailedParticipationModal() {
    const modal = document.getElementById('detailedParticipationModal');
    if (modal) {
        modal.classList.remove('active');
        document.getElementById('detailedParticipationForm').reset();
    }
}

// Fonction de soumission du formulaire de participation détaillée
document.getElementById('detailedParticipationForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);

    const payload = {
        action: 'participate_with_details', // Nouvelle action pour le contrôleur
        event_id: parseInt(formData.get('id_evenement')),
        user_id: formData.get('id_utilisateur') === 'null' ? null : parseInt(formData.get('id_utilisateur')),
        prenom: formData.get('prenom'),
        nom: formData.get('nom'),
        email: formData.get('email'),
        num_tel: formData.get('num_tel'), // New field
        num_identite: formData.get('num_identite'), // New field
        nombre_accompagnants: parseInt(formData.get('nombre_accompagnants')),
        besoins_accessibilite: formData.get('besoins_accessibilite'),
        message: formData.get('message')
    };
    
    // Validation minimale côté client pour les champs requis si non connecté
    if (!payload.user_id) {
        if (!payload.prenom || !payload.nom || !payload.email || !payload.num_tel || !payload.num_identite) {
            showGlobalStatusMessage('Veuillez renseigner tous les champs obligatoires (prénom, nom, email, numéro de téléphone, numéro d\'identité).', false);
            return;
        }
    }

    const submitButton = form.querySelector('.btn-modal-primary');
    const originalButtonText = submitButton.innerHTML;
    submitButton.disabled = true;
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Envoi...';

    const url = '../../Controller/PublicParticipationController.php'; // Corrected URL

    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(payload)
        });

        const data = await response.json();

        if (data.success) {
            showGlobalStatusMessage(data.message || 'Inscription enregistrée avec succès !', true);
            closeDetailedParticipationModal();
            setTimeout(() => location.reload(), 1500); // Recharger pour refléter le changement
        } else {
            showGlobalStatusMessage('Erreur: ' + (data.error || 'Erreur inconnue lors de l\'inscription.'), false);
        }
    } catch (error) {
        console.error('Erreur AJAX lors de la soumission du formulaire:', error);
        showGlobalStatusMessage('Erreur réseau : ' + (error.message || 'Impossible de se connecter au serveur.'), false);
    } finally {
        submitButton.disabled = false;
        submitButton.innerHTML = originalButtonText;
    }
});

// Fermer la modale en cliquant sur le fond noir
document.getElementById('detailedParticipationModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDetailedParticipationModal();
    }
});

// Fermer la modale avec la touche Échap
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDetailedParticipationModal();
    }
});

// L'ancienne fonction 'participer' est maintenant gérée par openDetailedParticipationModal
// Si elle est encore appelée ailleurs, renommez-la ou redirigez les appels.
/*
async function participer(buttonElement, eventId, userId) {
    // ... ancienne logique de participation simple ...
}
*/