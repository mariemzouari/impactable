/**
 * Fichier de validation JavaScript pour le backoffice
 * Remplace les contrôles de saisie HTML5 par des validations JavaScript
 */

// ========================================
// CONSTANTES ET CONFIGURATION
// ========================================
const COLORS = {
    ERROR: '#C62828',
    SUCCESS: '#A9B97D',
    DEFAULT: '#555'
};

// ========================================
// FONCTIONS UTILITAIRES
// ========================================

/**
 * Affiche une erreur visuelle sur un champ
 */
function setFieldError(field, hasError) {
    if (!field) return;
    field.style.borderColor = hasError ? COLORS.ERROR : COLORS.SUCCESS;
}

/**
 * Affiche les erreurs dans une alerte
 */
function showErrors(errors) {
    if (errors.length > 0) {
        alert('Veuillez corriger les erreurs suivantes :\n\n' + errors.join('\n'));
        return false;
    }
    return true;
}

/**
 * Vérifie si une valeur est vide
 */
function isEmpty(value) {
    return !value || value.trim() === '';
}

/**
 * Vérifie si une valeur est un nombre entier positif
 */
function isPositiveInteger(value) {
    const num = parseInt(value);
    return !isNaN(num) && num > 0;
}

/**
 * Vérifie si une valeur respecte une longueur minimale
 */
function hasMinLength(value, minLength) {
    return value && value.trim().length >= minLength;
}

/**
 * Vérifie si une valeur respecte une longueur maximale
 */
function hasMaxLength(value, maxLength) {
    return value && value.trim().length <= maxLength;
}

/**
 * Vérifie si un email est valide
 */
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

/**
 * Vérifie si une date est au format valide (YYYY-MM-DD)
 */
function isValidDate(date) {
    const dateRegex = /^\d{4}-\d{2}-\d{2}$/;
    return dateRegex.test(date);
}

// ========================================
// VALIDATION FORMULAIRE AJOUT RÉCLAMATION
// ========================================

/**
 * Valide le formulaire d'ajout de réclamation
 * @param {Event} event - L'événement de soumission
 * @returns {boolean} - True si valide, False sinon
 */
function validateAddReclamationForm(event) {
    const errors = [];
    
    // Validation sujet
    const sujet = document.getElementById('sujet');
    if (isEmpty(sujet?.value)) {
        errors.push('Le sujet est requis');
        setFieldError(sujet, true);
    } else if (!hasMinLength(sujet.value, 3)) {
        errors.push('Le sujet doit contenir au moins 3 caractères');
        setFieldError(sujet, true);
    } else {
        setFieldError(sujet, false);
    }
    
    // Validation description
    const description = document.getElementById('description');
    if (isEmpty(description?.value)) {
        errors.push('La description est requise');
        setFieldError(description, true);
    } else if (!hasMinLength(description.value, 10)) {
        errors.push('La description doit contenir au moins 10 caractères');
        setFieldError(description, true);
    } else {
        setFieldError(description, false);
    }
    
    // Validation catégorie
    const categorie = document.getElementById('categorie');
    if (isEmpty(categorie?.value)) {
        errors.push('Veuillez sélectionner une catégorie');
        setFieldError(categorie, true);
    } else {
        setFieldError(categorie, false);
    }
    
    // Validation priorité
    const priorite = document.getElementById('priorite');
    if (isEmpty(priorite?.value)) {
        errors.push('Veuillez sélectionner une priorité');
        setFieldError(priorite, true);
    } else {
        setFieldError(priorite, false);
    }
    
    // Validation statut
    const statut = document.getElementById('statut');
    if (isEmpty(statut?.value)) {
        errors.push('Veuillez sélectionner un statut');
        setFieldError(statut, true);
    } else {
        setFieldError(statut, false);
    }
    
    // Validation ID utilisateur
    const utilisateurId = document.getElementById('utilisateurId');
    if (isEmpty(utilisateurId?.value)) {
        errors.push('L\'ID utilisateur est requis');
        setFieldError(utilisateurId, true);
    } else if (!isPositiveInteger(utilisateurId.value)) {
        errors.push('L\'ID utilisateur doit être un nombre supérieur à 0');
        setFieldError(utilisateurId, true);
    } else {
        setFieldError(utilisateurId, false);
    }
    
    // Si des erreurs, empêcher la soumission
    if (errors.length > 0) {
        event.preventDefault();
        showErrors(errors);
        return false;
    }
    
    return true;
}

// ========================================
// VALIDATION FORMULAIRE MISE À JOUR RÉCLAMATION
// ========================================

/**
 * Valide le formulaire de mise à jour de réclamation
 * @param {Event} event - L'événement de soumission
 * @returns {boolean} - True si valide, False sinon
 */
function validateUpdateReclamationForm(event) {
    const errors = [];
    
    // Validation sujet
    const sujet = document.getElementById('sujet');
    if (isEmpty(sujet?.value)) {
        errors.push('Le sujet est requis');
        setFieldError(sujet, true);
    } else if (!hasMinLength(sujet.value, 3)) {
        errors.push('Le sujet doit contenir au moins 3 caractères');
        setFieldError(sujet, true);
    } else {
        setFieldError(sujet, false);
    }
    
    // Validation description
    const description = document.getElementById('description');
    if (isEmpty(description?.value)) {
        errors.push('La description est requise');
        setFieldError(description, true);
    } else if (!hasMinLength(description.value, 10)) {
        errors.push('La description doit contenir au moins 10 caractères');
        setFieldError(description, true);
    } else {
        setFieldError(description, false);
    }
    
    // Validation catégorie
    const categorie = document.getElementById('categorie');
    if (isEmpty(categorie?.value)) {
        errors.push('Veuillez sélectionner une catégorie');
        setFieldError(categorie, true);
    } else {
        setFieldError(categorie, false);
    }
    
    // Validation priorité
    const priorite = document.getElementById('priorite');
    if (isEmpty(priorite?.value)) {
        errors.push('Veuillez sélectionner une priorité');
        setFieldError(priorite, true);
    } else {
        setFieldError(priorite, false);
    }
    
    // Validation statut
    const statut = document.getElementById('statut');
    if (isEmpty(statut?.value)) {
        errors.push('Veuillez sélectionner un statut');
        setFieldError(statut, true);
    } else {
        setFieldError(statut, false);
    }
    
    // Validation ID utilisateur
    const utilisateurId = document.getElementById('utilisateurId');
    if (isEmpty(utilisateurId?.value)) {
        errors.push('L\'ID utilisateur est requis');
        setFieldError(utilisateurId, true);
    } else if (!isPositiveInteger(utilisateurId.value)) {
        errors.push('L\'ID utilisateur doit être un nombre supérieur à 0');
        setFieldError(utilisateurId, true);
    } else {
        setFieldError(utilisateurId, false);
    }
    
    // Si des erreurs, empêcher la soumission
    if (errors.length > 0) {
        event.preventDefault();
        showErrors(errors);
        return false;
    }
    
    return true;
}

// ========================================
// VALIDATION FORMULAIRE AJOUT RÉPONSE
// ========================================

/**
 * Valide le formulaire d'ajout de réponse
 * @param {Event} event - L'événement de soumission
 * @returns {boolean} - True si valide, False sinon
 */
function validateAddReponseForm(event) {
    const errors = [];
    
    // Validation contenu
    const contenu = document.getElementById('contenu');
    if (isEmpty(contenu?.value)) {
        errors.push('Le contenu de la réponse est requis');
        setFieldError(contenu, true);
    } else if (!hasMinLength(contenu.value, 5)) {
        errors.push('Le contenu doit contenir au moins 5 caractères');
        setFieldError(contenu, true);
    } else if (!hasMaxLength(contenu.value, 2000)) {
        errors.push('Le contenu ne peut pas dépasser 2000 caractères');
        setFieldError(contenu, true);
    } else {
        setFieldError(contenu, false);
    }
    
    // Validation reclamation_id (champ caché)
    const reclamationId = document.getElementById('reclamation_id');
    if (isEmpty(reclamationId?.value)) {
        errors.push('L\'ID de la réclamation est manquant');
    } else if (!isPositiveInteger(reclamationId.value)) {
        errors.push('L\'ID de la réclamation est invalide');
    }
    
    // Si des erreurs, empêcher la soumission
    if (errors.length > 0) {
        event.preventDefault();
        showErrors(errors);
        return false;
    }
    
    return true;
}

// ========================================
// VALIDATION FORMULAIRE MODIFICATION RÉPONSE
// ========================================

/**
 * Valide le formulaire de modification de réponse
 * @param {Event} event - L'événement de soumission
 * @returns {boolean} - True si valide, False sinon
 */
function validateUpdateReponseForm(event) {
    const errors = [];
    
    // Validation contenu
    const contenu = document.getElementById('contenu');
    if (isEmpty(contenu?.value)) {
        errors.push('Le contenu de la réponse est requis');
        setFieldError(contenu, true);
    } else if (!hasMinLength(contenu.value, 5)) {
        errors.push('Le contenu doit contenir au moins 5 caractères');
        setFieldError(contenu, true);
    } else if (!hasMaxLength(contenu.value, 2000)) {
        errors.push('Le contenu ne peut pas dépasser 2000 caractères');
        setFieldError(contenu, true);
    } else {
        setFieldError(contenu, false);
    }
    
    // Si des erreurs, empêcher la soumission
    if (errors.length > 0) {
        event.preventDefault();
        showErrors(errors);
        return false;
    }
    
    return true;
}

// ========================================
// VALIDATION EN TEMPS RÉEL (optionnel)
// ========================================

/**
 * Initialise la validation en temps réel sur les champs
 */
function initRealTimeValidation() {
    // Validation en temps réel pour les champs de texte
    document.querySelectorAll('input[type="text"], textarea').forEach(field => {
        field.addEventListener('blur', function() {
            if (isEmpty(this.value)) {
                setFieldError(this, true);
            } else {
                setFieldError(this, false);
            }
        });
        
        field.addEventListener('input', function() {
            if (!isEmpty(this.value)) {
                setFieldError(this, false);
            }
        });
    });
    
    // Validation en temps réel pour les selects
    document.querySelectorAll('select').forEach(field => {
        field.addEventListener('change', function() {
            if (isEmpty(this.value)) {
                setFieldError(this, true);
            } else {
                setFieldError(this, false);
            }
        });
    });
}

// ========================================
// INITIALISATION AU CHARGEMENT DE LA PAGE
// ========================================

document.addEventListener('DOMContentLoaded', function() {
    // Initialiser la validation en temps réel
    initRealTimeValidation();
    
    // Attacher les validateurs aux formulaires existants
    const addReclamationForm = document.querySelector('form[action=""]');
    if (addReclamationForm && document.getElementById('sujet')) {
        // Vérifier si c'est le formulaire d'ajout ou de mise à jour
        const isUpdateForm = document.querySelector('input[type="hidden"][name="id"]');
        if (isUpdateForm) {
            addReclamationForm.addEventListener('submit', validateUpdateReclamationForm);
        } else {
            addReclamationForm.addEventListener('submit', validateAddReclamationForm);
        }
    }
    
    // Formulaire d'ajout de réponse
    const addReponseForm = document.querySelector('form[action="ajouter_reponse.php"]');
    if (addReponseForm) {
        addReponseForm.addEventListener('submit', validateAddReponseForm);
    }
    
    // Formulaire de modification de réponse
    const updateReponseForm = document.querySelector('form[method="POST"]:not([action])');
    if (updateReponseForm && document.getElementById('contenu')) {
        updateReponseForm.addEventListener('submit', validateUpdateReponseForm);
    }
});

// ========================================
// EXPORT DES FONCTIONS (pour utilisation inline si nécessaire)
// ========================================
window.validateAddReclamationForm = validateAddReclamationForm;
window.validateUpdateReclamationForm = validateUpdateReclamationForm;
window.validateAddReponseForm = validateAddReponseForm;
window.validateUpdateReponseForm = validateUpdateReponseForm;


