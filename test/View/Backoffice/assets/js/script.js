document.addEventListener('DOMContentLoaded', function() {
      // Sidebar navigation
      const sidebarLinks = document.querySelectorAll('.sidebar-link');
      const tabContents = document.querySelectorAll('.tab-content');
      
      sidebarLinks.forEach(link => {
        link.addEventListener('click', function(e) {
          e.preventDefault();
          
          // Update active link
          sidebarLinks.forEach(l => l.classList.remove('active'));
          this.classList.add('active');
          
          // Show corresponding content
          const target = this.getAttribute('href').substring(1);
          tabContents.forEach(content => {
            content.classList.remove('active');
            if (content.id === `${target}-content`) {
              content.classList.add('active');
            }
          });
        });
      });
      
      // Tab navigation
      const tabs = document.querySelectorAll('.tab');
      
      tabs.forEach(tab => {
        tab.addEventListener('click', function() {
          // Update active tab
          tabs.forEach(t => t.classList.remove('active'));
          this.classList.add('active');
        });
      });
      
      // Search functionality
      const searchInput = document.querySelector('.search-bar input');
      
      searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        // In a real application, this would filter content
        console.log('Searching for:', searchTerm);
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