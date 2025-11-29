document.addEventListener('DOMContentLoaded', function() {
  // Vérifier si nous sommes sur la page index (dashboard)
  const isDashboardPage = document.getElementById('dashboard-content') !== null;
  // Vérifier si nous sommes sur la page utilisateurs
  const isUsersPage = document.getElementById('users-content') !== null;

  // Navigation sidebar - UNIQUEMENT pour les ancres internes
  const sidebarLinks = document.querySelectorAll('.sidebar-link');
  const tabContents = document.querySelectorAll('.tab-content');
  
  sidebarLinks.forEach(link => {
    link.addEventListener('click', function(e) {
      const href = this.getAttribute('href');
      
      // Si c'est un lien externe ou une autre page, laisser faire le navigateur
      if (!href.startsWith('#') || href === '#') {
        return true; // Navigation normale
      }
      
      // Si c'est une ancre interne, gérer l'affichage des onglets
      e.preventDefault();
      
      // Mettre à jour le lien actif
      sidebarLinks.forEach(l => l.classList.remove('active'));
      this.classList.add('active');
      
      // Afficher le contenu correspondant
      const target = href.substring(1);
      tabContents.forEach(content => {
        content.classList.remove('active');
        if (content.id === `${target}-content`) {
          content.classList.add('active');
        }
      });
    });
  });

  // Initialisation : Afficher le contenu par défaut selon la page
  function initializePage() {
    if (isDashboardPage) {
      // Sur la page dashboard, afficher le dashboard par défaut
      const dashboardContent = document.getElementById('dashboard-content');
      if (dashboardContent) {
        tabContents.forEach(content => content.classList.remove('active'));
        dashboardContent.classList.add('active');
      }
      
      // Activer le lien dashboard dans la sidebar
      const dashboardLink = document.querySelector('a[href="index.html"]');
      if (dashboardLink) {
        sidebarLinks.forEach(link => link.classList.remove('active'));
        dashboardLink.classList.add('active');
      }
    }
    
    if (isUsersPage) {
      // Sur la page utilisateurs, afficher le contenu utilisateurs par défaut
      const usersContent = document.getElementById('users-content');
      if (usersContent) {
        tabContents.forEach(content => content.classList.remove('active'));
        usersContent.classList.add('active');
      }
      
      // Activer le lien utilisateurs dans la sidebar
      const usersLink = document.querySelector('a[href="Ges_utilisateurs.html"]');
      if (usersLink) {
        sidebarLinks.forEach(link => link.classList.remove('active'));
        usersLink.classList.add('active');
      }
    }
  }

  // Initialiser la page
  initializePage();

  // tab navigation
  const tabs = document.querySelectorAll('.tab');
  tabs.forEach(tab => {
    tab.addEventListener('click', function() {
      tabs.forEach(t => t.classList.remove('active'));
      this.classList.add('active');
    });
  });
  


  // Button click animations
  document.querySelectorAll('.btn').forEach(button => {
    button.addEventListener('click', function() {
      this.style.transform = 'scale(0.98)';
      setTimeout(() => {
        this.style.transform = '';
      }, 150);
    });
  });
});