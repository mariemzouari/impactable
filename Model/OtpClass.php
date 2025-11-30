<?php

class OTP {

    private ?int $Id_password;
    private ?int $Id_utilisateur;
    private ?string $code;
    private ?string $expires_at;
    private ?int $used;
    private ?string $creation_time;

    // constructor
    public function __construct($data = []) {
        $this->Id_password     = $data['Id_password'] ?? null;
        $this->Id_utilisateur  = $data['Id_utilisateur'] ?? null; 
        $this->code            = $data['code'] ?? null;
        $this->expires_at      = $data['expires_at'] ?? null;
        $this->used            = $data['used'] ?? 0;
        $this->creation_time   = $data['creation_time'] ?? date("Y-m-d H:i:s");
    }

    // getters
    public function getId_password() { return $this->Id_password; }
    public function getId_utilisateur() { return $this->Id_utilisateur; }
    public function getCode() { return $this->code; }
    public function getExpires_at() { return $this->expires_at; }
    public function getUsed() { return $this->used; }
    public function getCreation_time() { return $this->creation_time; }

    // setters
    public function setId_password($id) { $this->Id_password = $id; }
    public function setId_utilisateur($id) { $this->Id_utilisateur = $id; }
    public function setCode($code) { $this->code = $code; }
    public function setExpires_at($datetime) { $this->expires_at = $datetime; }
    public function setUsed($used) { $this->used = $used; }
    public function setCreation_time($datetime) { $this->creation_time = $datetime; }

    
}
