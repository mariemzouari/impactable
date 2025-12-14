<?php
class Reclamation {
    private ?int $id;
    private ?string $sujet;
    private ?string $description;
    private ?string $categorie;
    private ?string $priorite;
    private ?string $statut;
    private ?DateTime $dateCreation;
    private ?DateTime $derniereModification;
    private ?int $utilisateurId;
    private ?string $agentAttribue;

    // Constructor
    public function __construct(
        ?int $id,
        ?string $sujet,
        ?string $description,
        ?string $categorie,
        ?string $priorite,
        ?string $statut,
        ?DateTime $dateCreation,
        ?DateTime $derniereModification,
        ?int $utilisateurId,
        ?string $agentAttribue
    ) {
        $this->id = $id;
        $this->sujet = $sujet;
        $this->description = $description;
        $this->categorie = $categorie;
        $this->priorite = $priorite;
        $this->statut = $statut;
        $this->dateCreation = $dateCreation;
        $this->derniereModification = $derniereModification;
        $this->utilisateurId = $utilisateurId;
        $this->agentAttribue = $agentAttribue;
    }

    public function show() {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr>
                <th>ID</th>
                <th>Sujet</th>
                <th>Description</th>
                <th>Catégorie</th>
                <th>Priorité</th>
                <th>Statut</th>
                <th>Date Création</th>
                <th>Dernière Modification</th>
                <th>ID Utilisateur</th>
                <th>Agent Attribué</th>
            </tr>";

        echo "<tr>";
        echo "<td>{$this->id}</td>";
        echo "<td>{$this->sujet}</td>";
        echo "<td>{$this->description}</td>";
        echo "<td>{$this->categorie}</td>";
        echo "<td>{$this->priorite}</td>";
        echo "<td>{$this->statut}</td>";
        echo "<td>" . ($this->dateCreation ? $this->dateCreation->format('Y-m-d H:i') : '') . "</td>";
        echo "<td>" . ($this->derniereModification ? $this->derniereModification->format('Y-m-d H:i') : '') . "</td>";
        echo "<td>{$this->utilisateurId}</td>";
        echo "<td>{$this->agentAttribue}</td>";
        echo "</tr>";
        echo "</table>";
    }

    // Getters & Setters

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): void { $this->id = $id; }

    public function getSujet(): ?string { return $this->sujet; }
    public function setSujet(?string $sujet): void { $this->sujet = $sujet; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): void { $this->description = $description; }

    public function getCategorie(): ?string { return $this->categorie; }
    public function setCategorie(?string $categorie): void { $this->categorie = $categorie; }

    public function getPriorite(): ?string { return $this->priorite; }
    public function setPriorite(?string $priorite): void { $this->priorite = $priorite; }

    public function getStatut(): ?string { return $this->statut; }
    public function setStatut(?string $statut): void { $this->statut = $statut; }

    public function getDateCreation(): ?DateTime { return $this->dateCreation; }
    public function setDateCreation(?DateTime $dateCreation): void { $this->dateCreation = $dateCreation; }

    public function getDerniereModification(): ?DateTime { return $this->derniereModification; }
    public function setDerniereModification(?DateTime $derniereModification): void { $this->derniereModification = $derniereModification; }

    public function getUtilisateurId(): ?int { return $this->utilisateurId; }
    public function setUtilisateurId(?int $utilisateurId): void { $this->utilisateurId = $utilisateurId; }

    public function getAgentAttribue(): ?string { return $this->agentAttribue; }
    public function setAgentAttribue(?string $agentAttribue): void { $this->agentAttribue = $agentAttribue; }
}
?>
