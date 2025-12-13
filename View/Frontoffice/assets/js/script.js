
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