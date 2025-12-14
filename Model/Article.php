<?php

class Article {

    private ?int $id;
    private ?string $titre;
    private ?string $auteur;
    private ?string $date_creation; // format YYYY-MM-DD
    private ?string $categorie;
    private ?string $contenu;
    private ?string $image;
    private ?int $auteur_id;
    private ?string $lieu;
    private ?array $tags;          // stocké JSON en BD

    // ==========================
    // CONSTRUCTEUR
    // ==========================
    public function __construct(
        ?int $id,
        ?string $titre,
        ?string $auteur,
        ?string $date_creation,
        ?string $categorie,
        ?string $contenu,
        ?string $image,
        ?int $auteur_id,
        ?string $lieu = null,
        ?array $tags = []
    ) {
        $this->id = $id;
        $this->titre = $titre;
        $this->auteur = $auteur;
        $this->date_creation = $date_creation;
        $this->categorie = $categorie;
        $this->contenu = $contenu;
        $this->image = $image;
        $this->auteur_id = $auteur_id;
        $this->lieu = $lieu;
        $this->tags = $tags;
    }

    // ==========================
    // AFFICHAGE (comme Project::show)
    // ==========================
    public function show() {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr>
                <th>ID</th>
                <th>Titre</th>
                <th>Auteur</th>
                <th>Date création</th>
                <th>Catégorie</th>
                <th>Contenu</th>
                <th>Image</th>
                <th>Auteur ID</th>
                <th>Lieu</th>
                <th>Tags</th>
              </tr>";

        echo "<tr>";
        echo "<td>{$this->id}</td>";
        echo "<td>{$this->titre}</td>";
        echo "<td>{$this->auteur}</td>";
        echo "<td>{$this->date_creation}</td>";
        echo "<td>{$this->categorie}</td>";
        echo "<td>{$this->contenu}</td>";
        echo "<td>{$this->image}</td>";
        echo "<td>{$this->auteur_id}</td>";
        echo "<td>{$this->lieu}</td>";
        echo "<td>" . implode(", ", $this->tags) . "</td>";
        echo "</tr>";

        echo "</table>";
    }

    // ==========================
    // GETTERS / SETTERS
    // ==========================

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): void { $this->id = $id; }

    public function getTitre(): ?string { return $this->titre; }
    public function setTitre(?string $titre): void { $this->titre = $titre; }

    public function getAuteur(): ?string { return $this->auteur; }
    public function setAuteur(?string $auteur): void { $this->auteur = $auteur; }

    public function getDateCreation(): ?string { return $this->date_creation; }
    public function setDateCreation(?string $date): void { $this->date_creation = $date; }

    public function getCategorie(): ?string { return $this->categorie; }
    public function setCategorie(?string $categorie): void { $this->categorie = $categorie; }

    public function getContenu(): ?string { return $this->contenu; }
    public function setContenu(?string $contenu): void { $this->contenu = $contenu; }

    public function getImage(): ?string { return $this->image; }
    public function setImage(?string $image): void { $this->image = $image; }

    public function getAuteurId(): ?int { return $this->auteur_id; }
    public function setAuteurId(?int $id): void { $this->auteur_id = $id; }

    public function getLieu(): ?string { return $this->lieu; }
    public function setLieu(?string $lieu): void { $this->lieu = $lieu; }

    public function getTags(): ?array { return $this->tags; }
    public function setTags(?array $t): void { $this->tags = $t; }

}
?>

