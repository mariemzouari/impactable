
document.addEventListener('DOMContentLoaded', function(){
  // Set current year in footer
  var yearElement = document.getElementById('year');
  if(yearElement) {
    yearElement.textContent = new Date().getFullYear();
  }

  // Force all modals to be visible with inline styles
  document.querySelectorAll('.modal-backdrop').forEach(modal => {
    modal.style.cssText = `
      display: flex !important;
      position: relative !important;
      background: transparent !important;
      margin: 2rem 0 !important;
      width: 100% !important;
      height: auto !important;
      z-index: 1 !important;
    `;
  });

  // Side Panel Navigation
  var navToggle = document.getElementById('navToggle');
  var sidePanel = document.getElementById('sidePanel');
  var panelClose = document.getElementById('panelClose');
  var panelOverlay = document.getElementById('panelOverlay');
  
  function openSidePanel() {
    if(sidePanel) sidePanel.classList.add('active');
    if(panelOverlay) panelOverlay.classList.add('active');
    document.body.style.overflow = 'hidden';
  }
  
  function closeSidePanel() {
    if(sidePanel) sidePanel.classList.remove('active');
    if(panelOverlay) panelOverlay.classList.remove('active');
    document.body.style.overflow = '';
  }
  
  if(navToggle && sidePanel) {
    navToggle.addEventListener('click', openSidePanel);
  }
  
  if(panelClose) {
    panelClose.addEventListener('click', closeSidePanel);
  }
  
  if(panelOverlay) {
    panelOverlay.addEventListener('click', closeSidePanel);
  }
  
  // Close panel when clicking on a link
  if(sidePanel) {
    var panelLinks = sidePanel.querySelectorAll('.nav-link');
    panelLinks.forEach(function(link) {
      link.addEventListener('click', function() {
        closeSidePanel();
      });
    });
  }

  // Add smooth scrolling for anchor links
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    if(anchor.getAttribute('href') !== '#') {
      anchor.addEventListener('click', function (e) {
        e.preventDefault();
        
        const targetId = this.getAttribute('href');
        const targetElement = document.querySelector(targetId);
        
        if(targetElement) {
          // Close mobile menu if open
          if(sidePanel && sidePanel.classList.contains('active')) {
            closeSidePanel();
          }
          
          window.scrollTo({
            top: targetElement.offsetTop - 100,
            behavior: 'smooth'
          });
        }
      });
    }
  });
  
  // Add visual feedback for buttons on click
  document.querySelectorAll('.btn').forEach(button => {
    button.addEventListener('click', function() {
      this.style.transform = 'scale(0.98)';
      setTimeout(() => {
        this.style.transform = '';
      }, 150);
    });
  });
});



    function validerFormulaireDon(event) {
        // Réinitialiser les erreurs
        reinitialiserErreurs();
        
        let estValide = true;
        
        // Validation du montant
        const montantSelectionne = document.querySelector('input[name="montant"]:checked');
        const montantCustom = document.getElementById('custom-amount').value;
        
        let montantFinal = 0;
        
        if (montantSelectionne && montantSelectionne.value === 'custom') {
            if (!montantCustom || parseFloat(montantCustom) <= 0) {
                afficherErreur('erreur-custom-montant', 'Veuillez entrer un montant personnalisé valide.');
                document.getElementById('custom-amount').classList.add('erreur');
                estValide = false;
            } else {
                montantFinal = parseFloat(montantCustom);
            }
        } else if (montantSelectionne) {
            montantFinal = parseFloat(montantSelectionne.value);
        } else {
            afficherErreur('erreur-montant', 'Veuillez sélectionner un montant.');
            estValide = false;
        }
        
        // Validation du montant minimum
        if (montantFinal > 0 && montantFinal < 1) {
            afficherErreur('erreur-montant', 'Le montant minimum est de 1 TND.');
            estValide = false;
        }
        
        // Validation de l'email si fourni
        const email = document.getElementById('donor-email').value;
        if (email && !validerEmail(email)) {
            afficherErreur('erreur-email', 'Veuillez entrer une adresse email valide.');
            document.getElementById('donor-email').classList.add('erreur');
            estValide = false;
        }
        
        // Validation de la méthode de paiement
        const methodePaiement = document.querySelector('input[name="methode_paiment"]:checked');
        if (!methodePaiement) {
            afficherErreur('erreur-paiement', 'Veuillez sélectionner une méthode de paiement.');
            estValide = false;
        }
        
        if (!estValide) {
            event.preventDefault();
            return false;
        }
        
        // Créer un champ hidden pour le montant final
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'montant';
        hiddenInput.value = montantFinal;
        document.getElementById('donationForm').appendChild(hiddenInput);
        
        return true;
    }

    function validerEmail(email) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    }

    function afficherErreur(idElement, message) {
        const element = document.getElementById(idElement);
        element.textContent = message;
        element.style.display = 'block';
    }

    function reinitialiserErreurs() {
        const erreurs = document.querySelectorAll('.message-erreur');
        erreurs.forEach(erreur => {
            erreur.style.display = 'none';
            erreur.textContent = '';
        });
        
        const inputs = document.querySelectorAll('.input');
        inputs.forEach(input => input.classList.remove('erreur'));
    }

    // Gestion des options de montant
    document.addEventListener('DOMContentLoaded', function() {
        const montantOptions = document.querySelectorAll('.montant-option');
        const customAmountInput = document.getElementById('custom-amount');
        
        montantOptions.forEach(option => {
            option.addEventListener('click', function() {
                const input = this.querySelector('input');
                const allInputs = document.querySelectorAll('input[name="montant"]');
                
                // Retirer la sélection précédente
                montantOptions.forEach(opt => {
                    opt.style.borderColor = 'var(--light-sage)';
                    opt.style.backgroundColor = 'white';
                });
                
                // Appliquer le style à l'option sélectionnée
                this.style.borderColor = 'var(--moss)';
                this.style.backgroundColor = 'var(--light-sage)';
                
                // Cocher l'input
                allInputs.forEach(inp => inp.checked = false);
                input.checked = true;
                
                // Gérer le champ personnalisé
                if (input.value === 'custom') {
                    customAmountInput.style.display = 'block';
                    customAmountInput.focus();
                } else {
                    customAmountInput.style.display = 'block';
                    customAmountInput.value = input.value;
                }
            });
        });
        
        // Gestion du style des options de paiement
        document.querySelectorAll('.payment-option').forEach(option => {
            option.addEventListener('click', function() {
                const input = this.querySelector('input');
                const groupName = input.name;
                
                // Retirer la sélection de toutes les options du même groupe
                document.querySelectorAll(`input[name="${groupName}"]`).forEach(opt => {
                    const parent = opt.closest('.payment-option');
                    if (parent) {
                        parent.style.borderColor = 'var(--light-sage)';
                        parent.style.backgroundColor = 'white';
                    }
                });
                
                // Appliquer le style à l'option sélectionnée
                this.style.borderColor = 'var(--moss)';
                this.style.backgroundColor = 'var(--light-sage)';
                input.checked = true;
            });
        });
    });