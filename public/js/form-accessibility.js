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

// Validation supplémentaire du formulaire
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.offre-form');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            const dateExpiration = document.getElementById('date_expiration');
            const today = new Date();
            const selectedDate = new Date(dateExpiration.value);
            
            // Validation de la date d'expiration
            if (selectedDate <= today) {
                e.preventDefault();
                alert('La date d\'expiration doit être postérieure à aujourd\'hui.');
                dateExpiration.focus();
                return false;
            }
            
            // Validation des champs requis
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = '#dc2626';
                } else {
                    field.style.borderColor = '';
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Veuillez remplir tous les champs obligatoires.');
                return false;
            }
            
            return true;
        });
    }
});