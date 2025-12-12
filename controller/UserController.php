<?php
require_once __DIR__ . '/../Model/UserModel.php';

class UserController {
    private $userModel;

    public function __construct(){
        $this->userModel = new UserModel();
    }

    public function register($data){
        return $this->userModel->register($data['nom'] ?? '', $data['prenom'] ?? '', $data['email'] ?? '', $data['mot_de_passe'] ?? '', $data['telephone'] ?? '', $data['date_naissance'] ?? null, $data['handicap'] ?? '');
    }

    public function login($email,$password){ return $this->userModel->login($email,$password); }
}

?>
