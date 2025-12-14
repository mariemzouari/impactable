<?php
class Reponse {
    private ?int $id;
    private ?int $reclamationId;     // FK vers la réclamation
    private ?int $adminId;           // ID de l'admin/agent qui répond
    private ?string $contenu;        // Le texte de la réponse
    private ?DateTime $dateReponse;  // Date de création
    private ?DateTime $dernierUpdate;

    public function __construct(
        ?int $id,
        ?int $reclamationId,
        ?int $adminId,
        ?string $contenu,
        ?DateTime $dateReponse,
        ?DateTime $dernierUpdate
    ) {
        $this->id = $id;
        $this->reclamationId = $reclamationId;
        $this->adminId = $adminId;
        $this->contenu = $contenu;
        $this->dateReponse = $dateReponse;
        $this->dernierUpdate = $dernierUpdate;
    }

    // --- Getters & Setters ---
    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): void { $this->id = $id; }

    public function getReclamationId(): ?int { return $this->reclamationId; }
    public function setReclamationId(?int $reclamationId): void { $this->reclamationId = $reclamationId; }

    public function getAdminId(): ?int { return $this->adminId; }
    public function setAdminId(?int $adminId): void { $this->adminId = $adminId; }

    public function getContenu(): ?string { return $this->contenu; }
    public function setContenu(?string $contenu): void { $this->contenu = $contenu; }

    public function getDateReponse(): ?DateTime { return $this->dateReponse; }
    public function setDateReponse(?DateTime $dateReponse): void { $this->dateReponse = $dateReponse; }

    public function getDernierUpdate(): ?DateTime { return $this->dernierUpdate; }
    public function setDernierUpdate(?DateTime $dernierUpdate): void { $this->dernierUpdate = $dernierUpdate; }
}
?>
