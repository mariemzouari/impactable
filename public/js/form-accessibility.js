// Gestion de l'affichage des options d'accessibilité
document.addEventListener('DOMContentLoaded', function() {
    const disabilityCheckbox = document.querySelector('input[name="disability_friendly"]');
    const accessibilityOptions = document.querySelector('.accessibility-options');
    
    if (disabilityCheckbox && accessibilityOptions) {
        function toggleAccessibilityOptions() {
            if (disabilityCheckbox.checked) {
                accessibilityOptions.style.display = 'block';
                // Ajouter une animation smooth
                accessibilityOptions.style.opacity = '0';
                accessibilityOptions.style.transition = 'opacity 0.3s ease';
                setTimeout(() => {
                    accessibilityOptions.style.opacity = '1';
                }, 10);
            } else {
                accessibilityOptions.style.opacity = '0';
                setTimeout(() => {
                    accessibilityOptions.style.display = 'none';
                    // Décocher toutes les cases de type de handicap
                    document.querySelectorAll('input[name="type_handicap[]"]').forEach(checkbox => {
                        checkbox.checked = false;
                    });
                }, 300);
            }
        }

        // Initialiser l'état au chargement
        toggleAccessibilityOptions();
        
        // Ajouter l'écouteur d'événement
        disabilityCheckbox.addEventListener('change', toggleAccessibilityOptions);
        
        // Gérer aussi le rechargement de page avec les données existantes
        window.addEventListener('load', toggleAccessibilityOptions);
    }
});

// Validation complète du formulaire
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.offre-form');
    
    if (form) {
        // Configuration des règles de validation
        const validationRules = {
            titre: {
                minLength: 5,
                errorMessage: 'Le titre doit contenir au moins 5 caractères',
                required: true
            },
            description: {
                minLength: 50,
                errorMessage: 'La description doit contenir au moins 50 caractères',
                required: true
            },
            impact_sociale: {
                minLength: 30,
                errorMessage: 'L\'impact social doit contenir au moins 30 caractères',
                required: true
            },
            date_expiration: {
                futureDate: true,
                errorMessage: 'La date d\'expiration doit être postérieure à aujourd\'hui',
                required: true
            },
            type_offre: {
                required: true,
                errorMessage: 'Le type d\'offre est obligatoire'
            },
            mode: {
                required: true,
                errorMessage: 'Le mode de travail est obligatoire'
            },
            horaire: {
                required: true,
                errorMessage: 'L\'horaire de travail est obligatoire'
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

            // Validation longueur minimale
            if (value && rules.minLength && value.length < rules.minLength) {
                isValid = false;
                errorMessage = rules.errorMessage;
            }

            // Validation date future
            if (value && rules.futureDate) {
                const selectedDate = new Date(value);
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                
                if (selectedDate <= today) {
                    isValid = false;
                    errorMessage = rules.errorMessage;
                }
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

        // Validation des boutons radio
        function validateRadioGroup(name, rules) {
            const radios = document.querySelectorAll(`input[name="${name}"]`);
            const checked = Array.from(radios).some(radio => radio.checked);
            let isValid = true;

            if (rules.required && !checked) {
                isValid = false;
                // Ajouter un message d'erreur pour le groupe
                const firstRadio = radios[0];
                const groupContainer = firstRadio.closest('.form-group');
                
                let groupError = groupContainer.querySelector('.group-error');
                if (!groupError) {
                    groupError = document.createElement('div');
                    groupError.className = 'group-error field-error';
                    groupError.style.color = '#dc2626';
                    groupError.style.fontSize = '0.875rem';
                    groupError.style.marginTop = '0.25rem';
                    groupError.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${rules.errorMessage}`;
                    groupContainer.appendChild(groupError);
                }
            } else {
                // Supprimer l'erreur du groupe si elle existe
                const firstRadio = radios[0];
                const groupContainer = firstRadio.closest('.form-group');
                const groupError = groupContainer.querySelector('.group-error');
                if (groupError) {
                    groupError.remove();
                }
            }

            return isValid;
        }

        // Validation en temps réel
        function setupRealTimeValidation() {
            // Validation des champs texte
            const textFields = ['titre', 'description', 'impact_sociale', 'date_expiration'];
            
            textFields.forEach(fieldName => {
                const field = document.getElementById(fieldName);
                if (field) {
                    field.addEventListener('blur', function() {
                        validateField(this, validationRules[fieldName]);
                    });

                    // Pour les textareas, compteur de caractères
                    if (fieldName === 'description' || fieldName === 'impact_sociale') {
                        field.addEventListener('input', function() {
                            const value = this.value.trim();
                            if (value.length > 0) {
                                validateField(this, validationRules[fieldName]);
                                updateCharacterCounter(this, value.length, validationRules[fieldName].minLength);
                            } else {
                                removeCharacterCounter(this);
                            }
                        });
                    }
                }
            });

            // Validation des boutons radio
            const radioGroups = ['mode', 'horaire'];
            radioGroups.forEach(groupName => {
                const radios = document.querySelectorAll(`input[name="${groupName}"]`);
                radios.forEach(radio => {
                    radio.addEventListener('change', function() {
                        validateRadioGroup(groupName, validationRules[groupName]);
                    });
                });
            });
        }

        // Mettre à jour le compteur de caractères
        function updateCharacterCounter(field, currentLength, minLength) {
            let counter = field.parentNode.querySelector('.character-counter');
            
            if (!counter) {
                counter = document.createElement('div');
                counter.className = 'character-counter';
                counter.style.fontSize = '0.75rem';
                counter.style.marginTop = '0.25rem';
                counter.style.fontWeight = '500';
                field.parentNode.appendChild(counter);
            }

            counter.textContent = `${currentLength} caractères (minimum ${minLength} requis)`;
            counter.style.color = currentLength < minLength ? '#dc2626' : '#16a34a';
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

            // Validation des champs texte
            for (const [fieldName, rules] of Object.entries(validationRules)) {
                const field = document.getElementById(fieldName);
                if (field && !validateField(field, rules)) {
                    isValid = false;
                    if (!firstInvalidField) {
                        firstInvalidField = field;
                    }
                }
            }

            // Validation des boutons radio
            const radioGroups = ['mode', 'horaire'];
            radioGroups.forEach(groupName => {
                if (!validateRadioGroup(groupName, validationRules[groupName])) {
                    isValid = false;
                    if (!firstInvalidField) {
                        const radios = document.querySelectorAll(`input[name="${groupName}"]`);
                        firstInvalidField = radios[0];
                    }
                }
            });

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
            
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Publication en cours...';
            submitBtn.disabled = true;
            
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 2000);
        }

        // Initialiser la validation en temps réel
        setupRealTimeValidation();

        // Nettoyage des erreurs au focus
        function setupErrorCleanup() {
            const fields = ['titre', 'description', 'impact_sociale', 'date_expiration'];
            
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