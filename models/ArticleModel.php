<?php

namespace Models;

use PDO;

class ArticleModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getFeaturedArticles() {
        // On prend les 6 premiers articles de la catégorie 'machines' comme articles à la une
        $query = "SELECT * FROM articles WHERE category = 'machines' ORDER BY created_at DESC LIMIT 6";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLatestArticles() {
        // On exclut les articles déjà affichés dans la section featured
        $query = "SELECT * FROM articles WHERE category != 'machines' ORDER BY created_at DESC LIMIT 12";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getArticleById($id) {
        $query = "SELECT * FROM articles WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getArticlesByCategory($category) {
        try {
            error_log("=== Debug ArticleModel::getArticlesByCategory ===");
            error_log("Catégorie recherchée : '" . $category . "'");

            // Vérifions d'abord toutes les catégories dans la base
            $checkQuery = "SELECT DISTINCT category FROM articles";
            $checkStmt = $this->db->query($checkQuery);
            $categories = $checkStmt->fetchAll(PDO::FETCH_COLUMN);
            error_log("Catégories existantes dans la BDD : " . implode("', '", $categories));

            // Comptons le nombre total d'articles
            $countQuery = "SELECT COUNT(*) as total FROM articles";
            $countStmt = $this->db->query($countQuery);
            $total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
            error_log("Nombre total d'articles dans la BDD : " . $total);

            // Maintenant notre requête principale
            $query = "SELECT * FROM articles WHERE category = :category ORDER BY created_at DESC";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':category', $category, PDO::PARAM_STR);
            
            // Debug de la requête avec la valeur réelle
            $debugQuery = str_replace(':category', "'" . $category . "'", $query);
            error_log("Requête SQL exécutée : " . $debugQuery);
            
            if (!$stmt->execute()) {
                error_log("Erreur d'exécution : " . implode(", ", $stmt->errorInfo()));
                return [];
            }

            $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("Nombre d'articles trouvés pour '" . $category . "' : " . count($articles));

            if (!empty($articles)) {
                foreach ($articles as $article) {
                    error_log("Article trouvé - ID: " . $article['id'] . 
                             ", Nom: " . $article['name'] . 
                             ", Catégorie exacte: '" . $article['category'] . "'");
                }
            }

            error_log("=== Fin Debug ===");
            return $articles;

        } catch (\PDOException $e) {
            error_log("Erreur PDO : " . $e->getMessage());
            return [];
        }
    }

    public function getRelatedArticles($category, $currentId, $limit = 3) {
        $query = "SELECT * FROM articles WHERE category = :category AND id != :currentId ORDER BY created_at DESC LIMIT :limit";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':category', $category, PDO::PARAM_STR);
        $stmt->bindParam(':currentId', $currentId, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}