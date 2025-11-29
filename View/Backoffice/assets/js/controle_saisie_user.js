// mot de passe strong, weak 
function passwordStrong(idPass, idStrength) {
  const password = document.getElementById(idPass);
  const strength = document.getElementById(idStrength);

    password.addEventListener("input", function () {
    const val = password.value;
    let score = 0;

  
    if (val.length >= 8) score++;
    if (val.length >= 12) score++; 
    if (/\d/.test(val)) score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[a-z]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;


    if (score <= 2) {
      strength.innerHTML = 'Weak';
      strength.style.color = "red";
    } 
    else if (score <= 4) {
      strength.innerHTML =  "Medium";
      strength.style.color = "orange";
    } 
    else {
      strength.innerHTML = "Strong";
      strength.style.color = "green";
    }
  });
}



//add user

document.addEventListener('DOMContentLoaded', function() {
    const useraddForm = document.getElementById("useraddForm");
    
    if (useraddForm) {
        useraddForm.addEventListener("submit", function(event) {
            
            const last_name = document.getElementById("add-last-name").value.trim();
            const name = document.getElementById("add-name").value.trim();
            const birthday = document.getElementById("add-birthday").value;
            const email = document.getElementById("add-email").value.trim();
            const number = document.getElementById("add-phone").value.trim();     
            const password = document.getElementById("add-password").value.trim();     
            const confirm = document.getElementById("add-confirm").value.trim();


            var isValid = true;
       
        
        // Fonction pour afficher les messages
        function displayMessage(id, message) {
        var element = document.getElementById(id + "-error");
        element.style.display = "block";
        element.innerHTML= '<i class="fas fa-exclamation-triangle"></i> '+ message;
        }

        //function for display none
        function displaynone(id){
            var element = document.getElementById(id + "-error");
             element.style.display = "none";
        }

        if (!last_name){    
        displayMessage("add-last-name","Veuillez entrer votre nom." );
        isValid = false;
            
        } 
        else displaynone("add-last-name");

        if (!name){
            displayMessage("add-name","Veuillez entrer votre prénom." );
            isValid =false;
        }
        else displaynone("add-name");

       if (!birthday){
            displayMessage("add-birthday", "Veuillez entrer une date de naissance.");
            isValid =false;
        }
        else if (new Date(birthday) > new Date()) {
            displayMessage("add-birthday", "La date de naissance ne peut pas être dans le futur.");
            isValid =false;
        }
        else displaynone("add-birthday");



        if (!email){
            displayMessage("add-email", "Veuillez entrer un e-mail.");
            isValid =false;
        }
       else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)){
            displayMessage("add-email", "Veuillez entrer un e-mail valide.");
            isValid =false; 
        }
        else displaynone("add-email");


        if (!number){
            displayMessage("add-phone", "Veuillez entrer un numéro de téléphone.");
            isValid =false; 
        }
       else if (number.length < 8){ 
            displayMessage("add-phone", "Veuillez entrer un numéro valide.");
            isValid =false;
        }
        else displaynone("add-phone");

       
       if (!password){
            displayMessage("add-password", "Veuillez créer un mot de passe." );
            isValid =false;
        }
        else if (password.length < 8) {
            displayMessage("add-password", "Le mot de passe doit contenir au moins 8 caractères." );
            isValid =false;
        }
        else displaynone("add-password");


        if (!confirm){
            displayMessage("add-confirm", "Veuillez confirmer votre mot de passe." );
            isValid =false; 
        }
        else if (confirm !== password){
            displayMessage("add-confirm", "Les mots de passe ne correspondent pas." );
            isValid =false; 
        }
        else displaynone("add-confirm");

       if (!isValid){
        event.preventDefault();
       }
             
          
        });
    }
});

// modifier un utilisateur 
const usereditForm = document.getElementById("usereditForm");
if (usereditForm) {
    usereditForm.addEventListener("submit", function(event) {
        console.log(" Validation modification en cours...");
        
        const photo = document.getElementById("avatarInput").files[0];
        const last_name = document.getElementById("edit-nom").value.trim();
        const name = document.getElementById("edit-prenom").value.trim();
        const birthday = document.getElementById("edit-date-naissance").value;
        const email = document.getElementById("edit-email").value.trim();
        const number = document.getElementById("edit-telephone").value.trim();     
        const bio = document.getElementById("edit-bio").value.trim();
        const city = document.getElementById("edit-ville").value.trim();
        const country = document.getElementById("edit-pays").value.trim();
        const linkedin = document.getElementById("edit-linkedin").value.trim();

        var isValid = true;
       
        
        // Fonction pour afficher les messages
        function displayMessage(id, message) {
        var element = document.getElementById(id + "-error");
        element.style.display = "block";
        element.innerHTML= '<i class="fas fa-exclamation-triangle"></i> '+ message;
        }

        //function for display none
        function displaynone(id){
            var element = document.getElementById(id + "-error");
             element.style.display = "none";
        }

        // photo    
        if (photo && !photo.type.startsWith('image/')){
            displayMessage("avatarInput", "Veuillez sélectionner une image.");
            isValid =false;
        }
        else if(photo && photo.size > 5 * 1024 * 1024){
            displayMessage("avatarInput", "L'image est trop lourde." );
            isValid =false;
        } 
        else displaynone("avatarInput");


        // infos user
        if (!last_name){   
            displayMessage("info-perso", "Veuillez entrer votre nom." );
            isValid =false;
        }  

        else if (!name){
            displayMessage("info-perso", "Veuillez entrer votre prénom." );
            isValid =false;
        }
        else if (!birthday){
            displayMessage("info-perso", "Veuillez entrer une date de naissance." );
            isValid =false;
        }
        else if (new Date(birthday) > new Date()) {
            displayMessage("info-perso", "La date de naissance ne peut pas être dans le futur." );
            isValid =false;
        }
        else if (!email){
            displayMessage("info-perso", "Veuillez entrer un e-mail." );
            isValid =false;
        }
        else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)){
            displayMessage("info-perso", "Veuillez entrer un e-mail valide.");
            isValid =false;
        }
        else if (!number){
            displayMessage("info-perso", "Veuillez entrer un numéro de téléphone." );
            isValid =false;
        }
        else if (number.length < 8){ 
            displayMessage("info-perso", "Veuillez entrer un numéro valide." );
            isValid =false;
        }
        else  displaynone("info-perso");


        // profil
        if (bio && bio.length > 200) {
            displayMessage("info-pro", "La bio ne doit pas dépasser 200 caractères.");
            isValid =false;
        }
        else if (city && city.length > 20) {
            displayMessage("info-pro", "La ville ne doit pas dépasser 20 caractères.");
            isValid =false;
        }
        else if (country && country.length > 20) {
            displayMessage("info-pro", "Le pays ne doit pas dépasser 20 caractères." );
            isValid =false;
        }
        else if (linkedin && !linkedin.includes('linkedin.com')) {
            displayMessage("info-pro", "Veuillez entrer une URL LinkedIn valide." );
            isValid =false;
        }
        else  displaynone("info-pro");

    

       
        if (!isValid){
        event.preventDefault();
       }
        






    });
}

