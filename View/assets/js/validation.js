/**
 * Système de validation complet pour ImpactAble
 * Valide les posts et commentaires avec affichage en temps réel (ROUGE/VERT)
 */

class FormValidator {
    constructor() {
        // Liste des mots interdits
        this.bannedWords = [
            'spam', 'arnaque', 'hack', 'pirate', 'connerie', 'merde', 'putain',
            'fuck', 'shit', 'bullshit', 'idiot', 'stupide', 'nul', 'inutile',
            'con', 'connard', 'salope', 'pute', 'bâtard', 'enculé'
        ];
        
        this.init();
    }

    init() {
        console.log('✅ FormValidator initialisé');
        this.setupEventListeners();
        this.initializeFormValidation();
    }

    setupEventListeners() {
        // Validation en temps réel pour tous les champs
        document.addEventListener('input', (e) => {
            if (e.target.form) {
                this.validateFieldRealTime(e.target);
            }
        });
        
        document.addEventListener('change', (e) => {
            if (e.target.form && e.target.name === 'categorie') {
                this.validateFieldRealTime(e.target);
            }
        });

        // Validation à la soumission
        document.addEventListener('submit', (e) => {
            const form = e.target;
            if (form.id === 'postForm' || form.id === 'editForm' || form.id === 'commentForm') {
                if (!this.validateForm(form)) {
                    e.preventDefault();
                }
            }
        });
    }

    initializeFormValidation() {
        // Validation du formulaire de post
        const postForm = document.getElementById('postForm') || document.getElementById('editForm');
        if (postForm) {
            postForm.addEventListener('submit', (e) => {
                if (!this.validatePostForm(postForm)) {
                    e.preventDefault();
                }
            });
        }

        // Validation du formulaire de commentaire
        const commentForm = document.getElementById('commentForm');
        if (commentForm) {
            commentForm.addEventListener('submit', (e) => {
                if (!this.validateCommentForm(commentForm)) {
                    e.preventDefault();
                }
            });
        }
    }

    validatePostForm(form) {
        const titre = form.querySelector('#titre').value.trim();
        const contenu = form.querySelector('#contenu').value.trim();
        const categorie = form.querySelector('#categorie').value;
        
        let errors = [];
        let titreValid = false;
        let contenuValid = false;
        let categorieValid = false;

        // Validation du titre (min 5, max 255)
        if (!titre) {
            errors.push('Le titre est obligatoire (minimum 5 caractères)');
            this.markFieldInvalid(form.querySelector('#titre'), 'Le titre est obligatoire');
        } else if (titre.length < 5) {
            errors.push('Le titre doit contenir au moins 5 caractères');
            this.markFieldInvalid(form.querySelector('#titre'), 'Min 5 caractères');
        } else if (titre.length > 255) {
            errors.push('Le titre ne peut pas dépasser 255 caractères');
            this.markFieldInvalid(form.querySelector('#titre'), 'Max 255 caractères');
        } else {
            titreValid = true;
            this.markFieldValid(form.querySelector('#titre'));
        }

        // Validation du contenu (min 10)
        if (!contenu) {
            errors.push('Le contenu est obligatoire (minimum 10 caractères)');
            this.markFieldInvalid(form.querySelector('#contenu'), 'Le contenu est obligatoire');
        } else if (contenu.length < 10) {
            errors.push('Le contenu doit contenir au moins 10 caractères');
            this.markFieldInvalid(form.querySelector('#contenu'), 'Min 10 caractères');
        } else {
            const contentValidation = this.validateContentQuality(contenu);
            if (!contentValidation.isValid) {
                errors = errors.concat(contentValidation.errors);
                this.markFieldInvalid(form.querySelector('#contenu'), contentValidation.errors[0]);
            } else {
                contenuValid = true;
                this.markFieldValid(form.querySelector('#contenu'));
            }
        }

        // Validation de la catégorie (obligatoire)
        if (!categorie) {
            errors.push('Veuillez sélectionner une catégorie');
            this.markFieldInvalid(form.querySelector('#categorie'), 'Catégorie obligatoire');
        } else {
            categorieValid = true;
            this.markFieldValid(form.querySelector('#categorie'));
        }

        if (errors.length > 0) {
            this.showFormErrors(form, errors);
            return false;
        }

        return true;
    }

    validateCommentForm(form) {
        const contenu = form.querySelector('#commentTextarea').value.trim();
        let errors = [];

        // Validation de la longueur (min 2)
        if (!contenu) {
            errors.push('Le commentaire ne peut pas être vide');
            this.markFieldInvalid(form.querySelector('#commentTextarea'), 'Vide');
            this.showFormErrors(form, errors);
            return false;
        }

        if (contenu.length < 2) {
            errors.push('Le commentaire doit contenir au moins 2 caractères');
            this.markFieldInvalid(form.querySelector('#commentTextarea'), 'Min 2 caractères');
            this.showFormErrors(form, errors);
            return false;
        }

        if (contenu.length > 1000) {
            errors.push('Le commentaire ne peut pas dépasser 1000 caractères');
            this.markFieldInvalid(form.querySelector('#commentTextarea'), 'Max 1000 caractères');
            this.showFormErrors(form, errors);
            return false;
        }

        // Validation de la qualité
        const qualityCheck = this.validateCommentQuality(contenu);
        if (!qualityCheck.isValid) {
            errors = errors.concat(qualityCheck.errors);
            this.markFieldInvalid(form.querySelector('#commentTextarea'), qualityCheck.errors[0]);
            this.showFormErrors(form, errors);
            return false;
        }

        this.markFieldValid(form.querySelector('#commentTextarea'));
        return true;
    }

    validateContentQuality(text) {
        const trimmed = text.trim();
        let errors = [];

        // Validation de la longueur minimale
        if (trimmed.length < 10) {
            errors.push('Le contenu doit contenir au moins 10 caractères');
        }

        // Détection des mots interdits
        const hasBannedWords = this.bannedWords.some(word => {
            const regex = new RegExp('\\b' + word + '\\b', 'i');
            return regex.test(trimmed);
        });
        
        if (hasBannedWords) {
            errors.push('Votre contenu contient des termes inappropriés. Merci d\'utiliser un langage respectueux.');
        }

        // Détection de répétition excessive de caractères
        if (this.hasCharacterRepetition(trimmed)) {
            errors.push('Évitez la répétition excessive de caractères (ex: aaaaa, !!!!!)');
        }

        // Détection du texte tout en majuscules
        if (this.isAllCaps(trimmed)) {
            errors.push('Évitez d\'écrire uniquement en majuscules. Cela est considéré comme crier.');
        }

        // Détection des URLs suspectes (spam)
        if (this.hasSuspiciousUrls(trimmed)) {
            errors.push('Votre contenu contient des liens suspects. Évitez le spam.');
        }

        return {
            isValid: errors.length === 0,
            errors: errors
        };
    }

    validateCommentQuality(text) {
        const trimmed = text.trim();
        let errors = [];

        // Détection des mots interdits
        const hasBannedWords = this.bannedWords.some(word => {
            const regex = new RegExp('\\b' + word + '\\b', 'i');
            return regex.test(trimmed);
        });
        
        if (hasBannedWords) {
            errors.push('Votre commentaire contient des termes inappropriés. Merci d\'utiliser un langage respectueux.');
        }

        // Détection de répétition excessive
        if (this.hasCharacterRepetition(trimmed)) {
            errors.push('Évitez la répétition excessive de caractères');
        }

        // Tout en majuscules (seulement pour les commentaires longs)
        if (this.isAllCaps(trimmed) && trimmed.length > 10) {
            errors.push('Évitez d\'écrire uniquement en majuscules');
        }

        return {
            isValid: errors.length === 0,
            errors: errors
        };
    }

    hasCharacterRepetition(text) {
        // Détecte 5 caractères identiques consécutifs ou plus
        return /(.)\1{4,}/.test(text);
    }

    isAllCaps(text) {
        // Vérifie si le texte est entièrement en majuscules (au moins 10 caractères)
        return text.length > 10 && text === text.toUpperCase() && /[A-Z]/.test(text);
    }

    hasSuspiciousUrls(text) {
        // Détecte plusieurs URLs dans le texte (potentiel spam)
        const urlRegex = /https?:\/\/[^\s]+/gi;
        const matches = text.match(urlRegex);
        return matches && matches.length > 3;
    }

    validateFieldRealTime(field) {
        const value = field.value.trim();
        
        if (!value) {
            field.classList.remove('valid', 'invalid');
            this.removeFieldError(field);
            return;
        }

        let isValid = true;
        let errorMsg = '';

        switch (field.name) {
            case 'titre':
                if (value.length < 5) {
                    isValid = false;
                    errorMsg = 'Min 5 caractères';
                } else if (value.length > 255) {
                    isValid = false;
                    errorMsg = 'Max 255 caractères';
                }
                break;

            case 'contenu':
                if (value.length < 10) {
                    isValid = false;
                    errorMsg = 'Min 10 caractères';
                } else {
                    const contentCheck = this.validateContentQuality(value);
                    if (!contentCheck.isValid) {
                        isValid = false;
                        errorMsg = contentCheck.errors[0];
                    }
                }
                break;

            case 'categorie':
                isValid = value !== '';
                if (!isValid) {
                    errorMsg = 'Sélectionner une catégorie';
                }
                break;

            case 'commentTextarea':
            case 'contenu':
                if (value.length < 2) {
                    isValid = false;
                    errorMsg = 'Min 2 caractères';
                } else if (value.length > 1000) {
                    isValid = false;
                    errorMsg = 'Max 1000 caractères';
                } else {
                    const qualityCheck = this.validateCommentQuality(value);
                    if (!qualityCheck.isValid) {
                        isValid = false;
                        errorMsg = qualityCheck.errors[0];
                    }
                }
                break;
        }

        if (isValid) {
            this.markFieldValid(field);
        } else {
            this.markFieldInvalid(field, errorMsg);
        }
    }

    markFieldValid(field) {
        field.classList.remove('invalid');
        field.classList.add('valid');
        this.removeFieldError(field);
    }

    markFieldInvalid(field, errorMsg = '') {
        field.classList.remove('valid');
        field.classList.add('invalid');
        
        if (errorMsg) {
            this.showFieldError(field, errorMsg);
        }
    }

    showFieldError(field, errorMsg) {
        this.removeFieldError(field);
        
        const errorDiv = document.createElement('div');
        errorDiv.className = 'field-error';
        errorDiv.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${errorMsg}`;
        
        field.parentElement.appendChild(errorDiv);
    }

    removeFieldError(field) {
        const existingError = field.parentElement.querySelector('.field-error');
        if (existingError) {
            existingError.remove();
        }
    }

    validateField(field) {
        const value = field.value.trim();
        
        if (!value) {
            field.classList.remove('valid', 'invalid');
            return;
        }

        let isValid = true;

        switch (field.name) {
            case 'titre':
                isValid = value.length >= 5 && value.length <= 255;
                break;

            case 'contenu':
                const contentCheck = this.validateContentQuality(value);
                isValid = contentCheck.isValid;
                break;

            case 'categorie':
                isValid = value !== '';
                break;
        }

        field.classList.remove('valid', 'invalid');
        if (value !== '') {
            field.classList.add(isValid ? 'valid' : 'invalid');
        }
    }

    validateForm(form) {
        let isValid = true;
        const fields = form.querySelectorAll('input[name="titre"], textarea[name="contenu"], select[name="categorie"]');

        fields.forEach(field => {
            if (!this.validateFieldRealTime(field)) {
                isValid = false;
            }
        });

        return isValid;
    }

    showFormErrors(form, errors) {
        // Supprimer les anciennes alertes
        const oldAlerts = form.querySelectorAll('.alert.alert-error');
        oldAlerts.forEach(alert => alert.remove());

        // Créer une nouvelle alerte
        let alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-error';
        alertDiv.innerHTML = `
            <i class="fas fa-exclamation-triangle"></i>
            <div>
                <strong>Erreurs à corriger :</strong>
                <ul>
                    ${errors.map(error => `<li>${error}</li>`).join('')}
                </ul>
            </div>
        `;

        // Insérer l'alerte au début du formulaire
        form.insertBefore(alertDiv, form.firstChild);

        // Scroll vers l'alerte
        alertDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });

        // Masquer l'alerte après 10 secondes
        setTimeout(() => {
            alertDiv.style.opacity = '0';
            alertDiv.style.transition = 'opacity 0.5s ease';
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 500);
        }, 10000);
    }
}

// Initialiser le validateur au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    window.formValidator = new FormValidator();
    console.log('🚀 Système de validation chargé avec affichage ROUGE/VERT');
});
