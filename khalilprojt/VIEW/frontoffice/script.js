// Gestion des onglets
function switchTab(tabName) {
    // Masquer toutes les sections
    document.querySelectorAll('.section').forEach(section => {
        section.classList.remove('active');
    });
    
    // Désactiver tous les boutons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Afficher la section correspondante
    const targetSection = document.getElementById(tabName + '-section');
    if (targetSection) {
        targetSection.classList.add('active');
    }
    
    // Activer le bouton correspondant
    if (event && event.target) {
        event.target.classList.add('active');
    }
}

// Compteur de caractères pour la description
const descriptionTextarea = document.getElementById('description');
const charCount = document.getElementById('charCount');

if (descriptionTextarea && charCount) {
    descriptionTextarea.addEventListener('input', function() {
        const length = this.value.length;
        charCount.textContent = length;
        
        if (length > 2000) {
            charCount.style.color = '#D32F2F';
        } else if (length > 1800) {
            charCount.style.color = '#FF9800';
        } else {
            charCount.style.color = '#5E6D38';
        }
    });
}

// Fonction de validation du formulaire
function validateReclamationForm() {
    const errors = [];
    
    // Validation nom
    const nom = document.getElementById('nom');
    if (!nom || !nom.value.trim() || nom.value.trim().length < 2) {
        errors.push('Le nom doit contenir au moins 2 caractères');
        if (nom) nom.style.borderColor = '#D32F2F';
    } else if (nom) {
        nom.style.borderColor = '#A9B97D';
    }
    
    // Validation prénom
    const prenom = document.getElementById('prenom');
    if (!prenom || !prenom.value.trim() || prenom.value.trim().length < 2) {
        errors.push('Le prénom doit contenir au moins 2 caractères');
        if (prenom) prenom.style.borderColor = '#D32F2F';
    } else if (prenom) {
        prenom.style.borderColor = '#A9B97D';
    }
    
    // Validation email
    const email = document.getElementById('email');
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!email || !email.value.trim() || !emailRegex.test(email.value.trim())) {
        errors.push('Veuillez entrer une adresse email valide');
        if (email) email.style.borderColor = '#D32F2F';
    } else if (email) {
        email.style.borderColor = '#A9B97D';
    }
    
    // Validation téléphone
    const telephone = document.getElementById('telephone');
    if (!telephone || !telephone.value.trim()) {
        errors.push('Le numéro de téléphone est requis');
        if (telephone) telephone.style.borderColor = '#D32F2F';
    } else if (telephone) {
        telephone.style.borderColor = '#A9B97D';
    }
    
    // Validation sujet
    const sujet = document.getElementById('sujet');
    if (!sujet || !sujet.value.trim() || sujet.value.trim().length < 10) {
        errors.push('Le sujet doit contenir au moins 10 caractères');
        if (sujet) sujet.style.borderColor = '#D32F2F';
    } else if (sujet) {
        sujet.style.borderColor = '#A9B97D';
    }
    
    // Validation catégorie
    const categorie = document.getElementById('categorie');
    if (!categorie || !categorie.value) {
        errors.push('Veuillez sélectionner une catégorie');
        if (categorie) categorie.style.borderColor = '#D32F2F';
    } else if (categorie) {
        categorie.style.borderColor = '#A9B97D';
    }
    
    // Validation description
    const description = document.getElementById('description');
    if (!description || !description.value.trim() || description.value.trim().length < 50) {
        errors.push('La description doit contenir au moins 50 caractères');
        if (description) description.style.borderColor = '#D32F2F';
    } else if (description) {
        description.style.borderColor = '#A9B97D';
    }
    
    // Validation priorité
    const priorite = document.querySelector('input[name="priorite"]:checked');
    if (!priorite) {
        errors.push('Veuillez sélectionner une priorité');
    }
    
    // Validation lieu
    const lieu = document.getElementById('lieu');
    if (!lieu || !lieu.value.trim()) {
        errors.push('Le lieu de l\'incident est requis');
        if (lieu) lieu.style.borderColor = '#D32F2F';
    } else if (lieu) {
        lieu.style.borderColor = '#A9B97D';
    }
    
    // Validation date incident
    const dateIncident = document.getElementById('dateIncident');
    const dateRegex = /^\d{4}-\d{2}-\d{2}$/;
    if (!dateIncident || !dateIncident.value.trim()) {
        errors.push('La date de l\'incident est requise (format: YYYY-MM-DD)');
        if (dateIncident) dateIncident.style.borderColor = '#D32F2F';
    } else if (!dateRegex.test(dateIncident.value.trim())) {
        errors.push('La date doit être au format YYYY-MM-DD (ex: 2025-11-30)');
        if (dateIncident) dateIncident.style.borderColor = '#D32F2F';
    } else if (dateIncident) {
        dateIncident.style.borderColor = '#A9B97D';
    }
    
    // Validation solution souhaitée
    const solutionSouhaitee = document.getElementById('solutionSouhaitee');
    if (!solutionSouhaitee || !solutionSouhaitee.value.trim()) {
        errors.push('La solution souhaitée est requise');
        if (solutionSouhaitee) solutionSouhaitee.style.borderColor = '#D32F2F';
    } else if (solutionSouhaitee) {
        solutionSouhaitee.style.borderColor = '#A9B97D';
    }
    
    return errors;
}

// Gestion de la soumission du formulaire
const reclamationForm = document.getElementById('reclamationForm');
const loader = document.getElementById('loader');
const successMessage = document.getElementById('successMessage');
const trackingNumber = document.getElementById('trackingNumber');

if (reclamationForm) {
    reclamationForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Valider le formulaire
        const errors = validateReclamationForm();
        if (errors.length > 0) {
            alert('Veuillez corriger les erreurs suivantes :\n\n' + errors.join('\n'));
            return false;
        }
        
        // Afficher le loader
        if (loader) {
            loader.style.display = 'flex';
            loader.classList.add('active');
        }
        if (successMessage) {
            successMessage.style.display = 'none';
            successMessage.classList.remove('show');
        }
        
        // Préparer les données du formulaire
        const formData = new FormData(this);
        
        // Envoyer la requête AJAX
        fetch('submit_reclamation.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            // Vérifier si la réponse est OK
            if (!response.ok) {
                throw new Error('Erreur HTTP: ' + response.status);
            }
            // Vérifier le type de contenu
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                return response.text().then(text => {
                    console.error('Réponse non-JSON:', text);
                    throw new Error('Le serveur a retourné une réponse invalide. Vérifiez la console pour plus de détails.');
                });
            }
            return response.json();
        })
        .then(data => {
            if (loader) {
                loader.style.display = 'none';
                loader.classList.remove('active');
            }
            
            if (data.success) {
                // Afficher le message de succès
                if (trackingNumber) trackingNumber.textContent = '#' + data.id;
                if (successMessage) {
                    successMessage.style.display = 'block';
                    successMessage.classList.add('show');
                }
                
                // Réinitialiser le formulaire
                reclamationForm.reset();
                if (charCount) charCount.textContent = '0';
                // Réinitialiser l'aperçu d'image
                removeImage();
                
                // Faire défiler vers le message de succès
                if (successMessage) {
                    successMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                
                // Masquer le message après 10 secondes (optionnel)
                setTimeout(() => {
                    if (successMessage) {
                        successMessage.style.display = 'none';
                        successMessage.classList.remove('show');
                    }
                }, 10000);
            } else {
                // Afficher un message d'erreur détaillé
                const errorMsg = data.message || 'Une erreur inconnue est survenue.';
                console.error('Erreur serveur:', data);
                alert('Erreur: ' + errorMsg);
            }
        })
        .catch(error => {
            if (loader) {
                loader.style.display = 'none';
                loader.classList.remove('active');
            }
            console.error('Erreur détaillée:', error);
            alert('Une erreur est survenue lors de l\'envoi de la réclamation: ' + error.message + '\n\nVérifiez la console du navigateur pour plus de détails.');
        });
    });
}

// Fonction pour prévisualiser l'image
function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    const fileInput = document.getElementById('image');
    
    if (input.files && input.files[0]) {
        const file = input.files[0];
        
        // Vérifier la taille (max 5MB)
        if (file.size > 5 * 1024 * 1024) {
            alert('L\'image est trop volumineuse. Taille maximale: 5MB');
            fileInput.value = '';
            return;
        }
        
        // Vérifier le type
        const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!validTypes.includes(file.type)) {
            alert('Format d\'image non supporté. Utilisez JPG, PNG ou GIF.');
            fileInput.value = '';
            return;
        }
        
        const reader = new FileReader();
        
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.style.display = 'block';
        };
        
        reader.readAsDataURL(file);
    } else {
        preview.style.display = 'none';
    }
}

// Fonction pour supprimer l'image
function removeImage() {
    const fileInput = document.getElementById('image');
    const preview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    
    fileInput.value = '';
    previewImg.src = '';
    preview.style.display = 'none';
}

// Recherche dans les réclamations (pour l'onglet "Mes Réclamations")
const searchInput = document.getElementById('searchReclamation');
if (searchInput) {
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const cards = document.querySelectorAll('.reclamation-card');
        
        cards.forEach(card => {
            const id = card.getAttribute('data-id');
            const sujet = card.getAttribute('data-sujet') || '';
            const description = card.querySelector('.reclamation-description')?.textContent.toLowerCase() || '';
            
            if (id.includes(searchTerm) || sujet.includes(searchTerm) || description.includes(searchTerm)) {
                card.classList.remove('hidden');
            } else {
                card.classList.add('hidden');
            }
        });
    });
}

// Fonction pour afficher/masquer les réponses
function toggleReponses(reclamationId) {
    const reponsesDiv = document.getElementById('reponses-' + reclamationId);
    const btn = event.target.closest('.btn-toggle-reponses');
    
    if (reponsesDiv) {
        if (reponsesDiv.style.display === 'none') {
            reponsesDiv.style.display = 'block';
            if (btn) btn.classList.add('active');
        } else {
            reponsesDiv.style.display = 'none';
            if (btn) btn.classList.remove('active');
        }
    }
}

// Fonction pour ouvrir le modal de réponse
function openReponseModal(reclamationId, sujet) {
    const modal = document.getElementById('reponseModal');
    const modalReclamationId = document.getElementById('modalReclamationId');
    const modalReclamationSujet = document.getElementById('modalReclamationSujet');
    const reclamationIdInput = document.getElementById('reclamationIdInput');
    
    if (modal && modalReclamationId && modalReclamationSujet && reclamationIdInput) {
        modalReclamationId.textContent = reclamationId;
        modalReclamationSujet.textContent = sujet;
        reclamationIdInput.value = reclamationId;
        
        // Réinitialiser le formulaire
        document.getElementById('reponseForm').reset();
        document.getElementById('reponseCharCount').textContent = '0';
        document.getElementById('successMessageReponse').style.display = 'none';
        document.getElementById('loaderReponse').style.display = 'none';
        
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
}

// Fonction pour fermer le modal de réponse
function closeReponseModal() {
    const modal = document.getElementById('reponseModal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
}

// Fermer le modal en cliquant sur l'overlay
document.addEventListener('click', function(e) {
    const modal = document.getElementById('reponseModal');
    if (modal && e.target === modal) {
        closeReponseModal();
    }
});

// Compteur de caractères pour la réponse
const reponseMessage = document.getElementById('reponseMessage');
const reponseCharCount = document.getElementById('reponseCharCount');

if (reponseMessage && reponseCharCount) {
    reponseMessage.addEventListener('input', function() {
        const length = this.value.length;
        reponseCharCount.textContent = length;
        
        if (length > 1000) {
            reponseCharCount.style.color = '#D32F2F';
        } else if (length > 800) {
            reponseCharCount.style.color = '#FF9800';
        } else {
            reponseCharCount.style.color = '#5E6D38';
        }
    });
}

// Fonction de validation du formulaire de réponse
function validateReponseForm() {
    const errors = [];
    
    // Validation message
    const message = document.getElementById('reponseMessage');
    if (!message || !message.value.trim() || message.value.trim().length < 10) {
        errors.push('Le message doit contenir au moins 10 caractères');
        if (message) message.style.borderColor = '#D32F2F';
    } else if (message.value.trim().length > 1000) {
        errors.push('Le message ne peut pas dépasser 1000 caractères');
        if (message) message.style.borderColor = '#D32F2F';
    } else if (message) {
        message.style.borderColor = '#A9B97D';
    }
    
    // Validation ID utilisateur
    const userId = document.getElementById('reponseUserId');
    const userIdNum = userId ? parseInt(userId.value) : 0;
    if (!userId || !userId.value.trim() || isNaN(userIdNum) || userIdNum < 1) {
        errors.push('L\'ID utilisateur doit être un nombre supérieur à 0');
        if (userId) userId.style.borderColor = '#D32F2F';
    } else if (userId) {
        userId.style.borderColor = '#A9B97D';
    }
    
    return errors;
}

// Fonction pour soumettre la réponse
function submitReponse(event) {
    event.preventDefault();
    
    const form = document.getElementById('reponseForm');
    const loader = document.getElementById('loaderReponse');
    const successMessage = document.getElementById('successMessageReponse');
    const submitBtn = form.querySelector('.btn-submit-reponse');
    
    // Valider le formulaire
    const errors = validateReponseForm();
    if (errors.length > 0) {
        alert('Veuillez corriger les erreurs suivantes :\n\n' + errors.join('\n'));
        return false;
    }
    
    // Afficher le loader
    if (loader) {
        loader.style.display = 'flex';
    }
    if (successMessage) {
        successMessage.style.display = 'none';
    }
    if (submitBtn) {
        submitBtn.disabled = true;
    }
    
    // Préparer les données
    const formData = new FormData(form);
    
    // Envoyer la requête AJAX
    fetch('submit_reponse.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Erreur HTTP: ' + response.status);
        }
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            return response.text().then(text => {
                console.error('Réponse non-JSON:', text);
                throw new Error('Le serveur a retourné une réponse invalide.');
            });
        }
        return response.json();
    })
    .then(data => {
        if (loader) {
            loader.style.display = 'none';
        }
        
        if (data.success) {
            // Afficher le message de succès
            if (successMessage) {
                successMessage.style.display = 'block';
            }
            
            // Réinitialiser le formulaire
            form.reset();
            if (reponseCharCount) reponseCharCount.textContent = '0';
            
            // Fermer le modal après 2 secondes
            setTimeout(() => {
                closeReponseModal();
                // Recharger la page pour afficher la nouvelle réponse
                location.reload();
            }, 2000);
        } else {
            // Afficher un message d'erreur
            const errorMsg = data.message || 'Une erreur est survenue.';
            alert('Erreur: ' + errorMsg);
            if (submitBtn) {
                submitBtn.disabled = false;
            }
        }
    })
    .catch(error => {
        if (loader) {
            loader.style.display = 'none';
        }
        console.error('Erreur détaillée:', error);
        alert('Une erreur est survenue lors de l\'envoi de la réponse: ' + error.message);
        if (submitBtn) {
            submitBtn.disabled = false;
        }
    });
}

