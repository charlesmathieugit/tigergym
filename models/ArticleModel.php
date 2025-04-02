<?php

namespace Models;

use PDO;

class ArticleModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getFeaturedArticles() {
        $query = "SELECT * FROM articles WHERE category = 'machines' ORDER BY created_at DESC LIMIT 6";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLatestArticles() {
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
            if ($category === 'vetements') {
                $query = "SELECT * FROM articles WHERE category IN ('vetements-hommes', 'vetements-femmes') ORDER BY created_at DESC";
                $stmt = $this->db->prepare($query);
            } else {
                $query = "SELECT * FROM articles WHERE category = :category ORDER BY created_at DESC";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':category', $category, PDO::PARAM_STR);
            }
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

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

    // Méthodes d'administration
    public function getAllArticles() {
        $query = "SELECT * FROM articles ORDER BY created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createArticle($data) {
        try {
            $query = "INSERT INTO articles (title, description, category, price, image_url, external_link, created_at) 
                     VALUES (:title, :description, :category, :price, :image_url, :external_link, NOW())";
            
            $stmt = $this->db->prepare($query);
            $success = $stmt->execute([
                'title' => $data['title'],
                'description' => $data['description'],
                'category' => $data['category'],
                'price' => $data['price'],
                'image_url' => $data['image_url'],
                'external_link' => $data['external_link']
            ]);

            if (!$success) {
                throw new \Exception("Erreur lors de la création de l'article");
            }

            return $this->db->lastInsertId();

        } catch (\Exception $e) {
            error_log("Erreur création article : " . $e->getMessage());
            throw $e;
        }
    }

    public function updateArticle($id, $data) {
        try {
            $query = "UPDATE articles 
                     SET title = :title, 
                         description = :description, 
                         category = :category, 
                         price = :price, 
                         image_url = :image_url, 
                         external_link = :external_link,
                         updated_at = NOW()
                     WHERE id = :id";
            
            $stmt = $this->db->prepare($query);
            $success = $stmt->execute([
                'id' => $id,
                'title' => $data['title'],
                'description' => $data['description'],
                'category' => $data['category'],
                'price' => $data['price'],
                'image_url' => $data['image_url'],
                'external_link' => $data['external_link']
            ]);

            if (!$success) {
                throw new \Exception("Erreur lors de la mise à jour de l'article");
            }

            return true;

        } catch (\Exception $e) {
            error_log("Erreur mise à jour article : " . $e->getMessage());
            throw $e;
        }
    }

    public function deleteArticle($id) {
        try {
            $query = "DELETE FROM articles WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $success = $stmt->execute(['id' => $id]);

            if (!$success) {
                throw new \Exception("Erreur lors de la suppression de l'article");
            }

            return true;

        } catch (\Exception $e) {
            error_log("Erreur suppression article : " . $e->getMessage());
            throw $e;
        }
    }
}