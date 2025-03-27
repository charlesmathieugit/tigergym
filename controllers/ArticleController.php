<?php

namespace Controllers;

use Models\ArticleModel;
use Twig\Environment;

class ArticleController {
    private $twig;
    private $articleModel;

    public function __construct($db, Environment $twig) {
        $this->twig = $twig;
        $this->articleModel = new ArticleModel($db);
    }

    public function show($id) {
        $article = $this->articleModel->getArticleById($id);
        
        if (!$article) {
            // Rediriger vers la page 404 ou la page d'accueil
            header('Location: /');
            exit;
        }

        $relatedArticles = $this->articleModel->getRelatedArticles($article['category'], $article['id']);

        echo $this->twig->render('article.html.twig', [
            'article' => $article,
            'relatedArticles' => $relatedArticles
        ]);
    }

    public function category($category) {
        $validCategories = ['machines', 'vetements', 'complements'];
        
        if (!in_array($category, $validCategories)) {
            header('Location: /');
            exit;
        }

        $articles = $this->articleModel->getArticlesByCategory($category);

        echo $this->twig->render('category.html.twig', [
            'category' => $category,
            'articles' => $articles
        ]);
    }
}