// ==================== SYSTÈME DE VALIDATION DE FORMULAIRES ====================

class FormValidator {
    constructor(formId, options = {}) {
        this.form = document.getElementById(formId);
        this.options = {
            realTime: true,
            showErrors: true,
            highlightFields: true,
            requiredFields: [], // Champs obligatoires définis en JS
            validationRules: {}, // Règles de validation personnalisées
            ...options
        };
        
        this.init();
    }
    
    init() {
        if (!this.form) return;
        
        // Définir les champs obligatoires automatiquement
        this.autoDetectRequiredFields();
        
        // Événements de validation
        if (this.options.realTime) {
            this.form.addEventListener('input', this.validateField.bind(this));
            this.form.addEventListener('change', this.validateField.bind(this));
        }
        
        this.form.addEventListener('submit', this.validateForm.bind(this));
        
        // Initialisation des champs
        this.initFields();
    }
    
    autoDetectRequiredFields() {
        // Détecter les champs qui devraient être obligatoires basés sur le contexte
        const fields = this.form.querySelectorAll('input, textarea, select');
        
        fields.forEach(field => {
            const fieldName = field.name || field.id;
            
            // Règles pour déterminer si un champ est obligatoire
            if (this.shouldBeRequired(field)) {
                if (!this.options.requiredFields.includes(fieldName)) {
                    this.options.requiredFields.push(fieldName);
                }
            }
        });
    }
    
    shouldBeRequired(field) {
        const fieldName = field.name || field.id;
        const formId = this.form.id;
        
        // Règles basées sur le type de formulaire et le nom du champ
        const rules = {
            // Formulaire d'offre
            'offre-form': ['titre', 'description', 'type_offre', 'impact_sociale', 'date_expiration'],
            'modification-form': ['titre', 'description', 'type_offre', 'impact_sociale', 'date_expiration'],
            
            // Formulaire de candidature
            'candidature-form': ['lettre_motivation'],
            
            // Formulaire de connexion
            'login-form': ['email', 'mot_de_passe'],
            
            // Règles générales basées sur le nom du champ
            'always-required': ['titre', 'description', 'email', 'mot_de_passe', 'lettre_motivation']
        };
        
        return rules[formId]?.includes(fieldName) || 
               rules['always-required']?.includes(fieldName) ||
               field.placeholder?.includes('*') ||
               field.closest('.form-group')?.querySelector('.form-label')?.textContent?.includes('*');
    }
    
    initFields() {
        const fields = this.form.querySelectorAll('input, textarea, select');
        fields.forEach(field => {
            this.addValidationIndicators(field);
            
            if (this.options.realTime) {
                this.validateField({ target: field });
            }
        });
    }
    
    addValidationIndicators(field) {
        const wrapper = field.closest('.form-group') || field.parentNode;
        
        // Créer l'élément d'erreur
        let errorElement = wrapper.querySelector('.field-error');
        if (!errorElement) {
            errorElement = document.createElement('div');
            errorElement.className = 'field-error';
            wrapper.appendChild(errorElement);
        }
        
        field.classList.add('validatable-field');
        
        // Ajouter un compteur de caractères pour les textareas et inputs text
        if ((field.type === 'textarea' || field.type === 'text') && 
            (field.name === 'description' || field.name === 'lettre_motivation' || field.name === 'impact_sociale')) {
            this.addCharCounter(field);
        }
    }
    
    addCharCounter(field) {
        const wrapper = field.closest('.form-group') || field.parentNode;
        let counter = wrapper.querySelector('.char-counter');
        
        if (!counter) {
            counter = document.createElement('div');
            counter.className = 'char-counter';
            wrapper.appendChild(counter);
        }
        
        field.addEventListener('input', () => {
            this.updateCharCounter(field, counter);
        });
        
        this.updateCharCounter(field, counter);
    }
    
    updateCharCounter(field, counter) {
        const length = field.value.length;
        const maxLength = this.getFieldMaxLength(field);
        
        counter.textContent = `${length}${maxLength ? `/${maxLength}` : ''} caractères`;
        
        // Changer la couleur selon la longueur
        counter.classList.remove('warning', 'error');
        
        if (maxLength) {
            const ratio = length / maxLength;
            if (ratio > 0.9) {
                counter.classList.add('error');
            } else if (ratio > 0.7) {
                counter.classList.add('warning');
            }
        }
    }
    
    getFieldMaxLength(field) {
        const rules = {
            'titre': 100,
            'description': 2000,
            'impact_sociale': 1000,
            'lettre_motivation': 5000
        };
        
        return rules[field.name] || field.maxLength || null;
    }
    
    validateField(e) {
        const field = e.target;
        const errors = this.getFieldErrors(field);
        
        this.updateFieldState(field, errors);
        this.updateFormState();
    }
    
    validateForm(e) {
        const fields = this.form.querySelectorAll('input, textarea, select');
        let isValid = true;
        let firstErrorField = null;
        
        fields.forEach(field => {
            const errors = this.getFieldErrors(field);
            this.updateFieldState(field, errors);
            
            if (errors.length > 0 && !firstErrorField) {
                firstErrorField = field;
                isValid = false;
            }
        });
        
        if (!isValid && firstErrorField) {
            e.preventDefault();
            firstErrorField.focus();
            this.showFormError('Veuillez corriger les erreurs dans le formulaire.');
            
            // Animation pour attirer l'attention sur le champ erroné
            firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
            this.highlightField(firstErrorField);
        }
        
        return isValid;
    }
    
    getFieldErrors(field) {
        const errors = [];
        const value = field.value.trim();
        const fieldName = field.name || field.id;
        const isRequired = this.options.requiredFields.includes(fieldName);
        
        // Validation required (uniquement via JavaScript)
        if (isRequired && !value) {
            const fieldLabel = this.getFieldLabel(field);
            errors.push(`${fieldLabel} est obligatoire.`);
        }
        
        // Validation selon le type et la valeur
        if (value) {
            switch (field.type) {
                case 'email':
                    if (!this.isValidEmail(value)) {
                        errors.push('Veuillez entrer une adresse email valide.');
                    }
                    break;
                    
                case 'url':
                    if (!this.isValidUrl(value)) {
                        errors.push('Veuillez entrer une URL valide.');
                    }
                    break;
            }
            
            // Validation de longueur minimale
            const minLength = this.getFieldMinLength(field);
            if (minLength && value.length < minLength) {
                errors.push(`Doit contenir au moins ${minLength} caractères.`);
            }
            
            // Validation de longueur maximale
            const maxLength = this.getFieldMaxLength(field);
            if (maxLength && value.length > maxLength) {
                errors.push(`Ne doit pas dépasser ${maxLength} caractères.`);
            }
            
            // Validation spécifique par nom de champ
            const fieldErrors = this.getSpecificFieldErrors(field, value);
            errors.push(...fieldErrors);
        }
        
        return errors;
    }
    
    getFieldMinLength(field) {
        const rules = {
            'titre': 5,
            'description': 50,
            'impact_sociale': 30,
            'lettre_motivation': 100
        };
        
        return rules[field.name] || field.minLength || null;
    }
    
    getSpecificFieldErrors(field, value) {
        const errors = [];
        const fieldName = field.name || field.id;
        
        switch (fieldName) {
            case 'titre':
                if (value.length < 5) errors.push('Le titre est trop court (min. 5 caractères).');
                if (value.length > 100) errors.push('Le titre est trop long (max. 100 caractères).');
                break;
                
            case 'description':
                if (value.length < 50) errors.push('La description est trop courte (min. 50 caractères).');
                if (value.length > 2000) errors.push('La description est trop longue (max. 2000 caractères).');
                break;
                
            case 'impact_sociale':
                if (value.length < 30) errors.push("L'impact social est trop court (min. 30 caractères).");
                if (value.length > 1000) errors.push("L'impact social est trop long (max. 1000 caractères).");
                break;
                
            case 'lettre_motivation':
                if (value.length < 100) errors.push('La lettre de motivation est trop courte (min. 100 caractères).');
                if (value.length > 5000) errors.push('La lettre de motivation est trop longue (max. 5000 caractères).');
                break;
                
            case 'date_expiration':
                const selectedDate = new Date(value);
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                
                if (selectedDate < today) {
                    errors.push("La date d'expiration ne peut pas être dans le passé.");
                }
                break;
                
            case 'email':
                if (!this.isValidEmail(value)) {
                    errors.push('Format d\'email invalide.');
                }
                break;
        }
        
        return errors;
    }
    
    getFieldLabel(field) {
        const label = field.closest('.form-group')?.querySelector('.form-label');
        return label ? label.textContent.replace('*', '').trim() : 'Ce champ';
    }
    
    updateFieldState(field, errors) {
        const wrapper = field.closest('.form-group') || field.parentNode;
        const errorElement = wrapper.querySelector('.field-error');
        
        // Réinitialiser les classes
        field.classList.remove('field-valid', 'field-invalid', 'field-warning');
        wrapper.classList.remove('has-error', 'has-success', 'has-warning');
        
        if (errors.length > 0) {
            // État invalide
            field.classList.add('field-invalid');
            wrapper.classList.add('has-error');
            
            if (this.options.showErrors && errorElement) {
                errorElement.textContent = errors[0];
                errorElement.style.display = 'block';
            }
        } else if (field.value.trim()) {
            // État valide
            field.classList.add('field-valid');
            wrapper.classList.add('has-success');
            
            if (errorElement) {
                errorElement.style.display = 'none';
            }
        } else {
            // État neutre
            if (errorElement) {
                errorElement.style.display = 'none';
            }
        }
    }
    
    highlightField(field) {
        field.classList.add('field-highlight');
        setTimeout(() => {
            field.classList.remove('field-highlight');
        }, 2000);
    }
    
    updateFormState() {
        const submitButton = this.form.querySelector('button[type="submit"]');
        if (!submitButton) return;
        
        const fields = this.form.querySelectorAll('input, textarea, select');
        let isValid = true;
        
        fields.forEach(field => {
            const fieldName = field.name || field.id;
            if (this.options.requiredFields.includes(fieldName) && !field.value.trim()) {
                isValid = false;
            }
        });
        
        submitButton.disabled = !isValid;
    }
    
    showFormError(message) {
        let globalError = this.form.querySelector('.global-form-error');
        if (!globalError) {
            globalError = document.createElement('div');
            globalError.className = 'global-form-error alert alert-error';
            this.form.prepend(globalError);
        }
        
        globalError.innerHTML = `<i class="fas fa-exclamation-triangle"></i> ${message}`;
        globalError.style.display = 'block';
        
        setTimeout(() => {
            globalError.style.display = 'none';
        }, 5000);
    }
    
    // Méthodes de validation de base
    isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    
    isValidUrl(url) {
        try {
            new URL(url);
            return true;
        } catch {
            return false;
        }
    }
    
    // Méthode pour réinitialiser
    resetForm() {
        this.form.reset();
        const fields = this.form.querySelectorAll('.validatable-field');
        fields.forEach(field => {
            this.updateFieldState(field, []);
        });
        this.updateFormState();
    }
}

// ==================== GESTIONNAIRE GLOBAL ====================

class FormManager {
    constructor() {
        this.validators = new Map();
        this.init();
    }
    
    init() {
        this.autoDetectForms();
        this.bindGlobalEvents();
    }
    
    autoDetectForms() {
        const forms = document.querySelectorAll('form');
        forms.forEach((form, index) => {
            if (!form.id) {
                form.id = `form-${index}`;
            }
            this.registerForm(form.id);
        });
    }
    
    registerForm(formId, options = {}) {
        const defaultOptions = this.getFormDefaultOptions(formId);
        const validator = new FormValidator(formId, { ...defaultOptions, ...options });
        this.validators.set(formId, validator);
        return validator;
    }
    
    getFormDefaultOptions(formId) {
        const formOptions = {
            'offre-form': {
                requiredFields: ['titre', 'description', 'type_offre', 'impact_sociale', 'date_expiration']
            },
            'modification-form': {
                requiredFields: ['titre', 'description', 'type_offre', 'impact_sociale', 'date_expiration']
            },
            'candidature-form': {
                requiredFields: ['lettre_motivation']
            },
            'login-form': {
                requiredFields: ['email', 'mot_de_passe']
            }
        };
        
        return formOptions[formId] || { requiredFields: [] };
    }
}

// ==================== INITIALISATION ====================

const formManager = new FormManager();

// Initialisation automatique au chargement
document.addEventListener('DOMContentLoaded', function() {
    console.log('🎯 Form Validator initialisé');
});

// Export pour utilisation globale
window.FormValidator = FormValidator;
window.FormManager = formManager;