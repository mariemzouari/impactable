<?php
session_start();

require_once __DIR__ . '/../../google_config.php';
require_once __DIR__ . '/../../Controller/UtilisateurController.php';
require_once __DIR__ . '/../../Controller/ProfileController.php';
require_once __DIR__ . '/../../Model/UtilisateurClass.php';
require_once __DIR__ . '/../../Model/ProfileClass.php';

$userC = new UtilisateurController();
$profileC = new ProfileController();

if (isset($_GET['code'])) {

    // get Google token
    $token = $google_client->fetchAccessTokenWithAuthCode($_GET['code']);
    $google_client->setAccessToken($token);

    $google_service = new Google\Service\Oauth2($google_client);
    $data = $google_service->userinfo->get();

    $email = $data['email'];
    $name = $data['givenName'];
    $lastname = $data['familyName'];
    $picture = $data['picture'];

    // vérifie si utilisateur existe
    $existing_user = $userC->verifyEmail($email);

    if ($existing_user) {

        // Connecter
        $_SESSION['user_id'] = $existing_user['Id_utilisateur'];
        $_SESSION['nom'] = $existing_user['nom'];
        $_SESSION['prenom'] = $existing_user['prenom'];
        $_SESSION['role'] = $existing_user['role'];

         if($_SESSION['role'] == "user"){
                header('Location: Profile.php');
                exit;}       
                
                else {
                header('Location: ../Backoffice/index.php');
                exit;}
        header("Location: dashboard.php");
        exit;
    }

    // sinon création
    $newUser = new Utilisateur([
        'nom' => $lastname,
        'prenom' => $name,
        'email' => $email,
        'numero_tel' => '',
        'date_naissance' => null,
        'mot_de_passe' => "google_oauth",
        'genre' => "prefere_ne_pas_dire",
        'role' => "user",
        'type_handicap' => "aucun"
    ]);

    $user_id = $userC->addUser($newUser);

    // Créer un profil vide avec la photo Google
    $newProfile = new Profil([
        'Id_utilisateur' => $user_id,
        'photo_profil' => $picture,
        'bio' => '',
        'ville' => '',
        'pays' => '',
        'profession' => '',
        'competences' => '',
        'linkedin' => '',
    ]);

    $profileC->addProfile($newProfile);

    // Connexion auto
    $_SESSION['user_id'] = $user_id;
    $_SESSION['nom'] = $lastname;
    $_SESSION['prenom'] = $name;

    header("Location: Profile.php");
    exit;
}
 ?>