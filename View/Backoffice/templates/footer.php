      </div> <!-- Fin de admin-content -->
    </main>
  </div>

  <script src="assets/js/script.js"></script>
  
  <script>
    // Script pour la sidebar active
    document.addEventListener('DOMContentLoaded', function() {
      const currentUrl = window.location.href;
      const sidebarLinks = document.querySelectorAll('.sidebar-link');
      
      sidebarLinks.forEach(link => {
        if (currentUrl.includes(link.getAttribute('href'))) {
          link.classList.add('active');
        }
      });

      // Animation des cartes de statistiques
      const statCards = document.querySelectorAll('.stat-card');
      statCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
          card.style.opacity = '1';
          card.style.transform = 'translateY(0)';
        }, index * 100);
      });

      // Fonction de recherche
      const searchInput = document.querySelector('.search-bar input');
      if (searchInput) {
        searchInput.addEventListener('input', function(e) {
          const query = e.target.value.toLowerCase();
          const tableRows = document.querySelectorAll('.admin-table tbody tr');
          
          tableRows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(query)) {
              row.style.display = '';
            } else {
              row.style.display = 'none';
            }
          });
        });
      }
    });
  </script>
</body>
</html>