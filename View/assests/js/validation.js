/**
 * Validation.js - Script de validation pour ImpactAble SANS HTML5
 * Gère la validation côté client des formulaires sans attributs HTML5
 */

class FormValidator {
    constructor() {
        this.init();
    }

    init() {
        console.log('✅ FormValidator SANS HTML5 initialisé');
        this.setupEventListeners();
    }

    setupEventListeners() {
        // Validation en temps réel pour les formulaires
        document.addEventListener('input', (e) => {
            if (e.target.form && e.target.form.id) {
                this.validateField(e.target);
            }
        });

        // Validation à la soumission SANS HTML5
        document.addEventListener('submit', (e) => {
            const form = e.target;
            if (form.id === 'postForm' || form.id === 'editForm' || form.id === 'commentForm') {
                if (!this.validateForm(form)) {
                    e.preventDefault();
                }
            }
        });
    }

    validateField(field) {
        const value = field.value.trim();
        let isValid = true;
        let errorMessage = '';

        switch (field.name) {
            case 'titre':
                // Validation SANS HTML5
                if (value.length < 5) {
                    isValid = false;
                    errorMessage = 'Le titre doit contenir au moins 5 caractères';
                } else if (value.length > 255) {
                    isValid = false;
                    errorMessage = 'Le titre ne peut pas dépasser 255 caractères';
                }
                break;

            case 'contenu':
                // Validation SANS HTML5
                if (value.length < 10) {
                    isValid = false;
                    errorMessage = 'Le contenu doit contenir au moins 10 caractères';
                }
                break;

            case 'categorie':
                // Validation SANS HTML5
                if (!value) {
                    isValid = false;
                    errorMessage = 'Veuillez sélectionner une catégorie';
                }
                break;

            case 'piece_jointe':
                if (field.files.length > 0) {
                    const file = field.files[0];
                    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                    const maxSize = 5 * 1024 * 1024; // 5MB

                    if (!allowedTypes.includes(file.type)) {
                        isValid = false;
                        errorMessage = 'Type de fichier non autorisé. Formats acceptés: JPG, PNG, GIF, WebP';
                    } else if (file.size > maxSize) {
                        isValid = false;
                        errorMessage = 'Le fichier est trop volumineux (max 5MB)';
                    }
                }
                break;
        }

        this.updateFieldStatus(field, isValid, errorMessage);
        return isValid;
    }

    validateForm(form) {
        let isValid = true;
        const fields = form.querySelectorAll('input, textarea, select');

        fields.forEach(field => {
            if (!this.validateField(field)) {
                isValid = false;
            }
        });

        if (!isValid) {
            this.showFormErrors(form);
        }

        return isValid;
    }

    updateFieldStatus(field, isValid, errorMessage) {
        // Retirer les classes existantes
        field.classList.remove('valid', 'invalid');
        
        // Ajouter la classe appropriée
        if (field.value.trim() !== '') {
            field.classList.add(isValid ? 'valid' : 'invalid');
        }

        // Gérer les messages d'erreur
        this.updateErrorMessage(field, errorMessage);
    }

    updateErrorMessage(field, message) {
        // Retirer l'ancien message d'erreur
        const existingError = field.parentNode.querySelector('.field-error-message');
        if (existingError) {
            existingError.remove();
        }

        // Ajouter le nouveau message d'erreur si nécessaire
        if (message) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'field-error-message';
            errorDiv.style.cssText = `
                color: #e74c3c;
                font-size: 0.8rem;
                margin-top: 4px;
                font-weight: 500;
            `;
            errorDiv.textContent = message;
            field.parentNode.appendChild(errorDiv);
        }
    }

    showFormErrors(form) {
        // Créer ou mettre à jour le conteneur d'erreurs global
        let errorContainer = form.querySelector('.form-errors');
        if (!errorContainer) {
            errorContainer = document.createElement('div');
            errorContainer.className = 'form-errors';
            errorContainer.style.cssText = `
                background: #f8d7da;
                color: #721c24;
                padding: 12px 16px;
                border-radius: 8px;
                margin-bottom: 20px;
                border: 1px solid #f5c6cb;
            `;
            form.insertBefore(errorContainer, form.firstChild);
        }

        errorContainer.innerHTML = `
            <strong style="display: block; margin-bottom: 8px;">
                <i class="fas fa-exclamation-triangle"></i>
                Veuillez corriger les erreurs suivantes :
            </strong>
            <ul style="margin: 0; padding-left: 20px;">
                <li>Certains champs contiennent des erreurs. Veuillez les vérifier.</li>
            </ul>
        `;

        // Scroll vers les erreurs
        errorContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    // Méthode utilitaire pour valider les commentaires SANS HTML5
    validateComment(commentText) {
        const trimmed = commentText.trim();
        if (trimmed.length < 2) {
            return { isValid: false, message: 'Le commentaire doit contenir au moins 2 caractères' };
        }
        if (trimmed.length > 1000) {
            return { isValid: false, message: 'Le commentaire ne peut pas dépasser 1000 caractères' };
        }
        return { isValid: true, message: '' };
    }
}

// Initialisation quand le DOM est chargé
document.addEventListener('DOMContentLoaded', function() {
    window.formValidator = new FormValidator();
    
    // Ajout de styles CSS pour la validation
    const style = document.createElement('style');
    style.textContent = `
        .valid {
            border-color: #4CAF50 !important;
            background-color: #e8f5e8 !important;
        }
        
        .invalid {
            border-color: #e74c3c !important;
            background-color: #ffebee !important;
        }
        
        .field-error-message {
            color: #e74c3c;
            font-size: 0.8rem;
            margin-top: 4px;
            font-weight: 500;
        }
        
        .form-errors {
            background: #f8d7da;
            color: #721c24;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }
    `;
    document.head.appendChild(style);
});

console.log('🚀 validation.js SANS HTML5 chargé avec succès');