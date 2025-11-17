
// ajouter un utilisateur 
const useraddForm = document.getElementById("useraddForm");
if (useraddForm) {

    useraddForm.addEventListener("submit", function(event) {
    event.preventDefault();


     let erreur = "";
     const  last_name = document.getElementById("add-last-name").value.trim();
     const  name  = document.getElementById("add-name").value.trim();
     const  birthday = document.getElementById("add-birthday").value;
     const  email  = document.getElementById("add-email").value.trim();
     const  number = document.getElementById("add-phone").value.trim();     
     const  password= document.getElementById("add-password").value.trim();     
     const  confirm = document.getElementById("add-confirm").value.trim();     
      

    if (!last_name){    
       erreur= "Veuillez entrer votre nom.";
     }     
    else if (!name){
       erreur = "Veuillez entrer votre prénom.";
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
    else if (!birthday){
       erreur = "Veuillez entrer une date de naissance.";
      }
    else if (new Date(birthday) > new Date()) {
       erreur = "La date de naissance ne peut pas être dans le futur.";
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
    document.getElementById("useradd-control").innerHTML = '<i class="fas fa-exclamation-triangle"></i> ' + erreur;
    document.getElementById("useradd-control").style.display = "block";
} else {
    document.getElementById("useradd-control").innerHTML = "";
    document.getElementById("useradd-control").style.display = "none";
}


})
}

// modifier un utilisateur 


const usereditForm = document.getElementById("usereditForm");
if (usereditForm) {

    usereditForm.addEventListener("submit", function(event) {
     event.preventDefault();
    let erreur = "";

    //user infos
     const  last_name = document.getElementById("edit-nom").value.trim();
     const  name  = document.getElementById("edit-prenom").value.trim();
     const  birthday = document.getElementById("edit-date-naissance").value;
     const  email  = document.getElementById("edit-email").value.trim();
     const  number = document.getElementById("edit-telephone").value.trim();     
     

   //profile
   
    const  bio  = document.getElementById("edit-bio").value.trim();
    const  city  = document.getElementById("edit-ville").value.trim();
    const  country  = document.getElementById("edit-pays").value.trim();
    const  linkedin  = document.getElementById("edit-linkedin").value.trim();


   
   //infos user

    if (!last_name){    
       erreur= "Veuillez entrer votre nom.";
     }     
    else if (!name){
       erreur = "Veuillez entrer votre prénom.";
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
    else if (!birthday){
       erreur = "Veuillez entrer une date de naissance.";
      }
    else if (new Date(birthday) > new Date()) {
       erreur = "La date de naissance ne peut pas être dans le futur.";
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



    
   
   
if (erreur) {
    document.getElementById("useredit-control").innerHTML = '<i class="fas fa-exclamation-triangle"></i> ' + erreur;
    document.getElementById("useredit-control").style.display = "block";
} else {
    document.getElementById("useredit-control").innerHTML = "";
    document.getElementById("useredit-control").style.display = "none";
}

    }) }