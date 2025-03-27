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
        $query = "SELECT * FROM articles WHERE category = :category ORDER BY created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':category', $category, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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