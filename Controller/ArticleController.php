<?php
require_once(__DIR__ . '/../CONFIGRRATION/config.php');
require_once(__DIR__ . '/../MODEL/Article.php');

class ArticleController {

    // ======================
    // LISTE DES ARTICLES
    // ======================
    public function listArticles($statut = null) {
        if ($statut) {
            $sql = "SELECT * FROM articles WHERE statut = :statut ORDER BY date_creation DESC";
            $db = config::getConnexion();
            try {
                $query = $db->prepare($sql);
                $query->execute(['statut' => $statut]);
                return $query;
            } catch (Exception $e) {
                die('Error:' . $e->getMessage());
            }
        } else {
            $sql = "SELECT * FROM articles";
            $db = config::getConnexion();
            try {
                $list = $db->query($sql);
                return $list;
            } catch (Exception $e) {
                die('Error:' . $e->getMessage());
            }
        }
    }

    // ======================
    // LISTER ARTICLES PAR STATUT
    // ======================
    public function listArticlesByStatus($statut = null) {
        if ($statut) {
            $sql = "SELECT * FROM articles WHERE statut = :statut ORDER BY date_creation DESC";
            $db = config::getConnexion();
            try {
                $query = $db->prepare($sql);
                $query->execute(['statut' => $statut]);
                return $query;
            } catch (Exception $e) {
                die('Error:' . $e->getMessage());
            }
        } else {
            return $this->listArticles();
        }
    }

    // ======================
    // SUPPRIMER ARTICLE
    // ======================
    public function deleteArticle($id) {
        $sql = "DELETE FROM articles WHERE id = :id";
        $db = config::getConnexion();
        $req = $db->prepare($sql);
        $req->bindValue(':id', $id);
        try {
            $req->execute();
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    // ======================
    // AJOUTER ARTICLE
    // ======================
    public function addArticle(Article $article, $statut = 'brouillon') {

        $sql = "INSERT INTO articles 
        (titre, auteur, date_creation, categorie, contenu, image, auteur_id, lieu, tags, statut, date_soumission)
        VALUES 
        (:titre, :auteur, :date_creation, :categorie, :contenu, :image, :auteur_id, :lieu, :tags, :statut, :date_soumission)";

        $db = config::getConnexion();

        try {
            $query = $db->prepare($sql);

            $query->execute([
                'titre'             => $article->getTitre(),
                'auteur'            => $article->getAuteur(),
                'date_creation'     => $article->getDateCreation(),
                'categorie'        => $article->getCategorie(),
                'contenu'           => $article->getContenu(),
                'image'             => $article->getImage(),
                'auteur_id'         => $article->getAuteurId(),
                'lieu'              => $article->getLieu(),
                'tags'              => json_encode($article->getTags()),
                'statut'            => $statut,
                'date_soumission'   => date('Y-m-d H:i:s')
            ]);

            return true;

        } catch (PDOException $e) {
            throw new Exception('Erreur lors de l\'ajout de l\'article: ' . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception('Erreur lors de l\'ajout de l\'article: ' . $e->getMessage());
        }
    }

    // ======================
    // APPROUVER ARTICLE
    // ======================
    public function approveArticle($id) {
        $sql = "UPDATE articles SET statut = 'publie', date_publication = :date_publication WHERE id = :id";
        $db = config::getConnexion();
        $req = $db->prepare($sql);
        $req->bindValue(':id', $id);
        $req->bindValue(':date_publication', date('Y-m-d H:i:s'));
        try {
            $req->execute();
            return true;
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    // ======================
    // MODIFIER ARTICLE
    // ======================
    public function updateArticle(Article $article, $id) {

        $sql = "UPDATE articles SET 
            titre = :titre,
            auteur = :auteur,
            date_creation = :date_creation,
            categorie = :categorie,
            contenu = :contenu,
            image = :image,
            auteur_id = :auteur_id,
            lieu = :lieu,
            tags = :tags
        WHERE id = :id";

        $db = config::getConnexion();

        try {
            $query = $db->prepare($sql);

            $query->execute([
                'id'                => $id,
                'titre'             => $article->getTitre(),
                'auteur'            => $article->getAuteur(),
                'date_creation'     => $article->getDateCreation(),
                'categorie'         => $article->getCategorie(),
                'contenu'           => $article->getContenu(),
                'image'             => $article->getImage(),
                'auteur_id'         => $article->getAuteurId(),
                'lieu'              => $article->getLieu(),
                'tags'              => json_encode($article->getTags())
            ]);

        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    // ======================
    // AFFICHER ARTICLE
    // ======================
    public function showArticle($id) {
        $sql="SELECT * FROM articles WHERE id = :id";
        $db = config::getConnexion();
        $query = $db->prepare($sql);

        try {
            $query->execute(['id' => $id]);
            $article = $query->fetch();
            return $article;
        } catch (Exception $e) {
            die('Error: '. $e->getMessage());
        }
    }

    // ======================
    // STATISTIQUES
    // ======================
    public function getStats() {
        $sql = "SELECT 
                COUNT(CASE WHEN statut = 'brouillon' THEN 1 END) as brouillons,
                COUNT(CASE WHEN statut = 'publie' THEN 1 END) as publies,
                COUNT(CASE WHEN statut = 'archive' THEN 1 END) as archives,
                COUNT(*) as total
                FROM articles";
        $db = config::getConnexion();
        return $db->query($sql)->fetch();
    }
}

?>

