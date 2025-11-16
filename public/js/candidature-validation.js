// Validation du formulaire de candidature - Version sans contrôles HTML
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.candidature-form');
    
    if (form) {
        // Configuration des règles de validation
        const validationRules = {
            cv: {
                pattern: /^https?:\/\/.+\..+/,
                errorMessage: 'Le lien CV doit être une URL valide (http:// ou https://)',
                required: false
            },
            linkedin: {
                pattern: /^https?:\/\/(www\.)?linkedin\.com\/in\/.+/,
                errorMessage: 'Le lien LinkedIn doit être un profil valide (ex: https://linkedin.com/in/votre-profil)',
                required: false
            },
            lettre_motivation: {
                minLength: 50,
                errorMessage: 'La lettre de motivation doit contenir au moins 50 caractères',
                required: true
            },
            notes: {
                maxLength: 1000,
                errorMessage: 'Les notes ne doivent pas dépasser 1000 caractères',
                required: false
            }
        };

        // Fonction de validation générique
        function validateField(field, rules) {
            const value = field.value.trim();
            let isValid = true;
            let errorMessage = '';

            // Réinitialiser le style
            field.style.borderColor = '';
            removeExistingError(field);

            // Validation des champs requis
            if (rules.required && !value) {
                isValid = false;
                errorMessage = 'Ce champ est obligatoire';
            }

            // Validation pattern (URL, etc.)
            if (value && rules.pattern && !rules.pattern.test(value)) {
                isValid = false;
                errorMessage = rules.errorMessage;
            }

            // Validation longueur minimale
            if (value && rules.minLength && value.length < rules.minLength) {
                isValid = false;
                errorMessage = rules.errorMessage;
            }

            // Validation longueur maximale
            if (value && rules.maxLength && value.length > rules.maxLength) {
                isValid = false;
                errorMessage = rules.errorMessage;
            }

            // Appliquer le style et le message d'erreur
            if (!isValid) {
                field.style.borderColor = '#dc2626';
                field.style.borderWidth = '2px';
                showError(field, errorMessage);
            } else if (value) {
                field.style.borderColor = '#16a34a';
                field.style.borderWidth = '2px';
            } else {
                field.style.borderColor = '';
                field.style.borderWidth = '';
            }

            return isValid;
        }

        // Afficher un message d'erreur
        function showError(field, message) {
            removeExistingError(field);
            
            const errorDiv = document.createElement('div');
            errorDiv.className = 'field-error';
            errorDiv.style.color = '#dc2626';
            errorDiv.style.fontSize = '0.875rem';
            errorDiv.style.marginTop = '0.25rem';
            errorDiv.style.fontWeight = '500';
            errorDiv.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;
            
            field.parentNode.appendChild(errorDiv);
        }

        // Supprimer les messages d'erreur existants
        function removeExistingError(field) {
            const existingError = field.parentNode.querySelector('.field-error');
            if (existingError) {
                existingError.remove();
            }
        }

        // Validation en temps réel
        function setupRealTimeValidation() {
            const fields = ['cv', 'linkedin', 'lettre_motivation', 'notes'];
            
            fields.forEach(fieldName => {
                const field = document.getElementById(fieldName);
                if (field) {
                    // Retirer les attributs HTML de validation
                    field.removeAttribute('required');
                    field.removeAttribute('pattern');
                    field.removeAttribute('maxlength');
                    field.removeAttribute('minlength');

                    field.addEventListener('blur', function() {
                        validateField(this, validationRules[fieldName]);
                    });

                    // Pour la lettre de motivation, validation au fur et à mesure
                    if (fieldName === 'lettre_motivation') {
                        field.addEventListener('input', function() {
                            const value = this.value.trim();
                            if (value.length > 0) {
                                validateField(this, validationRules[fieldName]);
                                
                                // Afficher le compteur de caractères
                                updateCharacterCounter(this, value.length, validationRules[fieldName].minLength);
                            } else {
                                removeCharacterCounter(this);
                            }
                        });
                    }

                    // Pour les notes, afficher le compteur de caractères
                    if (fieldName === 'notes') {
                        field.addEventListener('input', function() {
                            const value = this.value.trim();
                            if (value.length > 0) {
                                updateCharacterCounter(this, value.length, validationRules[fieldName].maxLength, true);
                            } else {
                                removeCharacterCounter(this);
                            }
                        });
                    }

                    // Validation initiale si le champ a déjà une valeur
                    if (field.value.trim()) {
                        setTimeout(() => {
                            validateField(field, validationRules[fieldName]);
                        }, 100);
                    }
                }
            });
        }

        // Mettre à jour le compteur de caractères
        function updateCharacterCounter(field, currentLength, limit, isMaxLimit = false) {
            let counter = field.parentNode.querySelector('.character-counter');
            
            if (!counter) {
                counter = document.createElement('div');
                counter.className = 'character-counter';
                counter.style.fontSize = '0.75rem';
                counter.style.marginTop = '0.25rem';
                counter.style.fontWeight = '500';
                field.parentNode.appendChild(counter);
            }

            if (isMaxLimit) {
                counter.textContent = `${currentLength}/${limit} caractères`;
                counter.style.color = currentLength > limit ? '#dc2626' : '#6b7280';
            } else {
                counter.textContent = `${currentLength} caractères (minimum ${limit} requis)`;
                counter.style.color = currentLength < limit ? '#dc2626' : '#16a34a';
            }
        }

        // Supprimer le compteur de caractères
        function removeCharacterCounter(field) {
            const counter = field.parentNode.querySelector('.character-counter');
            if (counter) {
                counter.remove();
            }
        }

        // Validation complète du formulaire
        function validateForm() {
            let isValid = true;
            let firstInvalidField = null;

            for (const [fieldName, rules] of Object.entries(validationRules)) {
                const field = document.getElementById(fieldName);
                if (field && !validateField(field, rules)) {
                    isValid = false;
                    if (!firstInvalidField) {
                        firstInvalidField = field;
                    }
                }
            }

            return { isValid, firstInvalidField };
        }

        // Gestion de la soumission du formulaire
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const { isValid, firstInvalidField } = validateForm();
            
            if (!isValid) {
                // Afficher un message d'erreur général
                showGeneralError('Veuillez corriger les erreurs dans le formulaire avant de soumettre.');
                
                if (firstInvalidField) {
                    firstInvalidField.scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'center' 
                    });
                    firstInvalidField.focus();
                }
                return false;
            }

            // Si tout est valide, soumettre le formulaire
            showSuccessMessage();
            this.submit();
        });

        // Afficher une erreur générale
        function showGeneralError(message) {
            let generalError = document.querySelector('.general-form-error');
            
            if (!generalError) {
                generalError = document.createElement('div');
                generalError.className = 'general-form-error';
                generalError.style.cssText = `
                    background: #fef2f2;
                    border: 2px solid #dc2626;
                    color: #dc2626;
                    padding: 1rem;
                    border-radius: 0.5rem;
                    margin-bottom: 1rem;
                    font-weight: 500;
                `;
                generalError.innerHTML = `
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Erreur de validation</strong>
                    </div>
                    <div style="margin-top: 0.5rem; margin-left: 1.5rem;">${message}</div>
                `;
                form.insertBefore(generalError, form.firstChild);
            }

            // Supprimer l'erreur générale après 5 secondes
            setTimeout(() => {
                if (generalError && generalError.parentNode) {
                    generalError.remove();
                }
            }, 5000);
        }

        // Afficher un message de succès
        function showSuccessMessage() {
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Envoi en cours...';
            submitBtn.disabled = true;
            
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 2000);
        }

        // Fonction utilitaire pour formater les URLs
        function formatURL(url) {
            if (!url) return '';
            
            // Ajouter https:// si manquant
            if (!url.startsWith('http://') && !url.startsWith('https://')) {
                return 'https://' + url;
            }
            
            return url;
        }

        // Auto-format des URLs
        function setupAutoFormat() {
            const urlFields = ['cv', 'linkedin'];
            
            urlFields.forEach(fieldName => {
                const field = document.getElementById(fieldName);
                if (field) {
                    field.addEventListener('blur', function() {
                        const formattedURL = formatURL(this.value.trim());
                        if (formattedURL !== this.value) {
                            this.value = formattedURL;
                            // Re-valider après formatage
                            validateField(this, validationRules[fieldName]);
                        }
                    });
                }
            });
        }

        // Initialiser toutes les fonctionnalités
        setupRealTimeValidation();
        setupAutoFormat();

        // Nettoyage des erreurs au focus
        function setupErrorCleanup() {
            const fields = ['cv', 'linkedin', 'lettre_motivation', 'notes'];
            
            fields.forEach(fieldName => {
                const field = document.getElementById(fieldName);
                if (field) {
                    field.addEventListener('focus', function() {
                        this.style.borderColor = '';
                        this.style.borderWidth = '';
                        removeExistingError(this);
                        
                        // Supprimer l'erreur générale
                        const generalError = document.querySelector('.general-form-error');
                        if (generalError) {
                            generalError.remove();
                        }
                    });
                }
            });
        }

        setupErrorCleanup();
    }
});

// Style CSS additionnel pour améliorer l'apparence
const additionalStyles = `
    .field-error {
        animation: fadeIn 0.3s ease-in;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-5px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .form-control:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
`;

// Injecter les styles additionnels
const styleSheet = document.createElement('style');
styleSheet.textContent = additionalStyles;
document.head.appendChild(styleSheet);