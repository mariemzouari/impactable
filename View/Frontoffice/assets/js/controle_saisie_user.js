
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



// sign up 
const signupForm = document.getElementById("signupForm");
if (signupForm) {
    signupForm.addEventListener("submit", function(event) {
        
        const last_name = document.getElementById("signup-last-name").value.trim();
        const name = document.getElementById("signup-name").value.trim();
        const birthday = document.getElementById("signup-birthday").value;
        const email = document.getElementById("signup-email").value.trim();
        const number = document.getElementById("signup-phone").value.trim();     
        const password = document.getElementById("signup-password").value.trim();     
        const confirm = document.getElementById("signup-confirm").value.trim();     
        
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
            displayMessage("signup-last-name","Veuillez entrer votre nom." );
            isValid = false;
            
        } 
        else displaynone("signup-last-name");

        if (!name){
            displayMessage("signup-name","Veuillez entrer votre prénom." );
            isValid =false;
        }
        else displaynone("signup-name");

       if (!birthday){
            displayMessage("signup-birthday", "Veuillez entrer une date de naissance.");
            isValid =false;
        }
        else if (new Date(birthday) > new Date()) {
            displayMessage("signup-birthday", "La date de naissance ne peut pas être dans le futur.");
            isValid =false;
        }
        else displaynone("signup-birthday");



        if (!email){
            displayMessage("signup-email", "Veuillez entrer un e-mail.");
            isValid =false;
        }
       else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)){
            displayMessage("signup-email", "Veuillez entrer un e-mail valide.");
            isValid =false; 
        }
        else displaynone("signup-email");


        if (!number){
            displayMessage("signup-phone", "Veuillez entrer un numéro de téléphone.");
            isValid =false; 
        }
       else if (number.length < 8){ 
            displayMessage("signup-phone", "Veuillez entrer un numéro valide.");
            isValid =false;
        }
        else displaynone("signup-phone");

       
       if (!password){
            displayMessage("signup-password", "Veuillez créer un mot de passe." );
            isValid =false;
        }
        else if (password.length < 8) {
            displayMessage("signup-password", "Le mot de passe doit contenir au moins 8 caractères." );
            isValid =false;
        }
        else displaynone("signup-password");


        if (!confirm){
            displayMessage("signup-confirm", "Veuillez confirmer votre mot de passe." );
            isValid =false; 
        }
        else if (confirm !== password){
            displayMessage("signup-confirm", "Les mots de passe ne correspondent pas." );
            isValid =false; 
        }
        else displaynone("signup-confirm");

       if (!isValid){
        event.preventDefault();
       }

        
    });
}

// sign in 
const loginForm = document.getElementById("loginForm");
if (loginForm) {
    loginForm.addEventListener("submit", function(event) {
       
        const email = document.getElementById("login-email").value.trim();   
        const password = document.getElementById("login-password").value.trim();  
        
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
        

        if (!email){
            displayMessage("login-email", "Veuillez entrer votre e-mail." );
            isValid =false;
        }
        else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)){
            displayMessage("login-email", "Veuillez entrer un e-mail valide." );
            isValid =false;
        }
        else displaynone("login-email");


        if (!password){
            displayMessage("login-password", "Veuillez entrer votre mot de passe." );
            isValid =false;
        }
        else if (password.length < 8) {
            displayMessage("login-password", "Le mot de passe doit contenir au moins 8 caractères." );
            isValid =false;

        }
        else displaynone("login-password");

        
         if (!isValid){
        event.preventDefault();
       }


    });
}

// modification profil 
const profileForm = document.getElementById("profileForm");
if (profileForm) {
    profileForm.addEventListener("submit", function(event) {
        let erreur = "";

        // photo 
        const photo = document.getElementById("avatarInput").files[0];

        // user infos
        const last_name = document.getElementById("last-name-profile").value.trim();
        const name = document.getElementById("name-profile").value.trim();
        const birthday = document.getElementById("birthday-profile").value;
        const email = document.getElementById("email-profile").value.trim();
        const number = document.getElementById("phone-profile").value.trim();     

        // profile
        const bio = document.getElementById("bio-profile").value.trim();
        const city = document.getElementById("city-profile").value.trim();
        const country = document.getElementById("country-profile").value.trim();
        const linkedin = document.getElementById("linkedin-profile").value.trim();

        // mots de passes
        const password = document.getElementById("password-profile").value.trim();
        const new_password = document.getElementById("new-password-profile").value.trim();
        const confirm = document.getElementById("confirm-profile").value.trim();


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

        

        // password
        if(password || new_password || confirm) {
            if (!password){
                displayMessage("security", "Veuillez écrire votre ancien mot de passe."  );
                isValid =false;
            }
            else if ( password.length < 8) {
                displayMessage("security", "Le mot de passe doit contenir au moins 8 caractères."  );
                isValid =false;
            }
            else if (!new_password){
              displayMessage("security", "Veuillez créer un nouveau mot de passe."  );
              isValid =false;   
            }
            else if ( new_password.length < 8) {
                displayMessage("security", "Le nouveau mot de passe doit contenir au moins 8 caractères.");
                isValid =false;
            }
            else if (!confirm) {
              displayMessage("security", "Veuillez confirmer votre mot de passe."  );
              isValid =false;
            }
            else if ( confirm.length < 8) {
                displayMessage("security", "La confirmation doit contenir au moins 8 caractères." );
                isValid =false;
            }
            else if ( new_password !== confirm) {
                displayMessage("security", "Les mots de passe ne correspondent pas."  );
                isValid =false;
            }
            else  displaynone("security");
   
            
        }

       
        if (!isValid){
        event.preventDefault();
       }



    });
}











