// sign up 

const signupForm = document.getElementById("signupForm");
if (signupForm) {

    signupForm.addEventListener("submit", function(event) {
    event.preventDefault();


     let erreur = "";
     const  last_name = document.getElementById("signup-last-name").value.trim();
     const  name  = document.getElementById("signup-name").value.trim();
     const  birthday = document.getElementById("birthday").value;
     const  email  = document.getElementById("signup-email").value.trim();
     const  number = document.getElementById("phone-number").value.trim();     
     const  password= document.getElementById("signup-password").value.trim();     
     const  confirm = document.getElementById("signup-confirm").value.trim();     
      

    if (!last_name){    
       erreur= "Veuillez entrer votre nom.";
     }     
    else if (!name){
       erreur = "Veuillez entrer votre prénom.";
    }
    else if (!birthday){
       erreur = "Veuillez entrer une date de naissance.";
      }
    else if (new Date(birthday) > new Date()) {
       erreur = "La date de naissance ne peut pas être dans le futur.";
    }

    else if (!email){
       erreur = "Veuillez entrer un e-mail.";
      }
    else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)){
        erreur = "Veuillez entrer un e-mail valide.";   
    }
    else if (!number){
        erreur = "Veuillez entrer un numéro de téléphonne.";
    }
    else if (number.length < 12){
        erreur = "Veuillez entrer un numéro valide." ;
    }
    else if (!password){
        erreur = "Veuillez créer un mot de passe." ;
    }
    else if (password.length < 8) {
        erreur = "Le mot de passe doit contenir au moin 8 caractères." ;
    }
    else if (!confirm){
        erreur = "Veuillez confirmer votre mot de passe."; 
    }
    else if (confirm !== password){
        erreur = "Les mots de passe ne correspondent pas." ;
    }

    
   if (erreur) {
    document.getElementById("signup-control").innerHTML = '<i class="fas fa-exclamation-triangle"></i> ' + erreur;
    document.getElementById("signup-control").style.display = "block";
} else {
    document.getElementById("signup-control").innerHTML = "";
    document.getElementById("signup-control").style.display = "none";
}


})
}




//sign in 

const loginForm = document.getElementById("loginForm");
if (loginForm) {
loginForm.addEventListener("submit", function(event) {
event.preventDefault(); 
  
let erreur = "";
const  email  = document.getElementById("login-email").value.trim();   
const  password= document.getElementById("login-password").value.trim();   


    if (!email){
       erreur = "Veuillez entrer votre e-mail.";
      }
    else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)){
        erreur = "Veuillez entrer un e-mail valide.";   
    }

    else if (!password){
        erreur = "Veuillez entrer votre mot de passe." ;
    }
    else if (password.length < 8) {
        erreur = "Le mot de passe doit contenir au moin 8 caractères." ;
    }



if (erreur) {
    document.getElementById("login-control").innerHTML = '<i class="fas fa-exclamation-triangle"></i> ' + erreur;
    document.getElementById("login-control").style.display = "block";
} else {
    document.getElementById("login-control").innerHTML = "";
    document.getElementById("login-control").style.display = "none";
}

}) }


// modification profil 
const profileForm = document.getElementById("profileForm");
if (profileForm) {

    profileForm.addEventListener("submit", function(event) {
    event.preventDefault();

    let erreur = "";


    // photo 
     const  photo  = document.getElementById("avatarInput").files[0];

    //user infos
     const  last_name = document.getElementById("last-name-profile").value.trim();
     const  name  = document.getElementById("name-profile").value.trim();
     const  birthday = document.getElementById("birthday-profile").value;
     const  email  = document.getElementById("email-profile").value.trim();
     const  number = document.getElementById("phone-number-profile").value.trim();     
     

   //profile
   
    const  bio  = document.getElementById("bio-profile").value.trim();
    const  city  = document.getElementById("city-profile").value.trim();
    const  country  = document.getElementById("country-profile").value.trim();
    const  linkedin  = document.getElementById("linkedin-profile").value.trim();

    //mots de passes
    const  password  = document.getElementById("password-profile").value.trim();
    const  new_password  = document.getElementById("new-password-profile").value.trim();
    const  confirm  = document.getElementById("confirm-profile").value.trim();

    //photo    
    
        
    
    if (photo && !photo.type.startsWith('image/')){
        erreur ="Veuillez selectionner une image." ;
    }
    else if(photo && photo.size > 5 * 1024 * 1024){
        erreur = "L'image est trop lourde." ;
    } 
   
   //infos user

    else if (!last_name){    
       erreur= "Veuillez entrer votre nom.";
     }     
    
    else if (!name){
       erreur = "Veuillez entrer votre prénom.";
    }
    else if (!birthday){
       erreur = "Veuillez entrer une date de naissance.";
      }
    else if (new Date(birthday) > new Date()) {
       erreur = "La date de naissance ne peut pas être dans le futur.";
    }

    else if (!email){
       erreur = "Veuillez entrer un e-mail.";
      }
    else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)){
        erreur = "Veuillez entrer un e-mail valide.";   
    }
    
    else if (!number){
        erreur = "Veuillez entrer un numéro de téléphonne.";
    }
    else if (number.length < 12){
        erreur = "Veuillez entrer un numéro valide." ;
    }

    //profil
    else if (bio && bio.length > 200) {
        erreur = "La bio ne doit pas dépasser 200 caractères.";
            }
    
    else if (city && city.length > 20) {
         erreur = "La ville ne doit pas dépasser 20 caractères.";
            }
    else if (country && country.length > 20) {
        erreur = "Le pays ne doit pas dépasser 20 caractères.";
            }
    else if (linkedin && !linkedin.includes('linkedin.com')) {
        erreur = "Veuillez entrer une URL LinkedIn valide.";
            }



    // password
    else if(password){

    if (password.length < 8) {
        erreur = "Le mot de passe doit contenir au moin 8 caractères." ;
    }
    else if (!new_password){
        erreur = "Veuillez entrer un nouveau mot de passe." ;
    }
    else if (new_password.length <8 ){
        erreur = "Le mot de passe doit contenir au moin 8 caractères." ;
    }
    else if (!confirm){
        erreur = "Veuillez confirmer votre nouveau mot de passe."; 
    }
    else if (confirm !== new_password){
        erreur = "Les mots de passe ne correspondent pas." ;
    }

    }

    else if(new_password){

    if (!password){
        erreur = "Veuillez entrer votre ancien mot de passe." ;
    }
    else if (password.length < 8) {
        erreur = "Le mot de passe doit contenir au moin 8 caractères." ;
    }
    else if (new_password.length <8 ){
        erreur = "Le mot de passe doit contenir au moin 8 caractères." ;
    }
    else if (!confirm){
        erreur = "Veuillez confirmer votre mot de passe."; 
    }
    else if (confirm !== new_password){
        erreur = "Les mots de passe ne correspondent pas." ;
    }


    }

    else if(confirm){
    if (confirm.length <8 ){
        erreur = "Le mot de passe doit contenir au moin 8 caractères." ;
    }
    if (!password){
        erreur = "Veuillez entrer votre ancien mot de passe." ;
    }
    else if (password.length < 8) {
        erreur = "Le mot de passe doit contenir au moin 8 caractères." ;
    }
    else if (!new_password){
        erreur = "Veuillez entrer un nouveau mot de passe." ;
    }
    else if (new_password.length <8 ){
        erreur = "Le mot de passe doit contenir au moin 8 caractères." ;
    }
    else if (confirm !== new_password){
        erreur = "Les mots de passe ne correspondent pas." ;
    }


    }

   
   
if (erreur) {
    document.getElementById("profile-control").innerHTML = '<i class="fas fa-exclamation-triangle"></i> ' + erreur;
    document.getElementById("profile-control").style.display = "block";
} else {
    document.getElementById("profile-control").innerHTML = "";
    document.getElementById("profile-control").style.display = "none";
}

    }) }