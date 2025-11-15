// Gestion de la photo de profil
document.addEventListener('DOMContentLoaded', function() {
  const avatarInput = document.getElementById('avatarInput');
  const avatarPreview = document.getElementById('avatarPreview');
  const avatarPlaceholder = document.getElementById('avatarPlaceholder');
  const avatarEditBtn = document.getElementById('avatarEditBtn');
  
  // Seul le bouton "Modifier" ouvre le sélecteur de fichier
  avatarEditBtn.addEventListener('click', function() {
    avatarInput.click();
  });
  
  // Aperçu quand un fichier est sélectionné
  avatarInput.addEventListener('change', function(e) {
    const file = e.target.files[0];
    
    if (file) {
      const reader = new FileReader();
      
      reader.onload = function(e) {
        avatarPreview.src = e.target.result;
        avatarPreview.style.display = 'block';
        avatarPlaceholder.style.display = 'none';
      };
      
      reader.readAsDataURL(file);
    }
  });
});