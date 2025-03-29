<?php

namespace Controllers;

use Models\ArticleModel;
use Twig\Environment;

class ArticleController {
    private $twig;
    private $articleModel;
    private $categoryMapping = [
        'machines' => 'Machines',
        'vetements' => 'Vêtements',
        'vetements-hommes' => 'Vêtements Hommes',
        'vetements-femmes' => 'Vêtements Femmes',
        'complements' => 'Compléments'
    ];

    private $templateMapping = [
        'machines' => 'machines.html.twig',
        'vetements' => 'vetements.html.twig',
        'vetements-hommes' => 'vetements-hommes.html.twig',
        'vetements-femmes' => 'vetements-femmes.html.twig',
        'complements' => 'complements.html.twig'
    ];

    public function __construct($db, Environment $twig) {
        $this->twig = $twig;
        $this->articleModel = new ArticleModel($db);
    }

    public function show($id) {
        $article = $this->articleModel->getArticleById($id);
        
        if (!$article) {
            header('Location: /tigergym/');
            exit;
        }


        // Récupération des articles liés
        $relatedArticles = $this->articleModel->getRelatedArticles($article['category'], $article['id']);

        echo $this->twig->render('article.html.twig', [
            'article' => $article,
            'relatedArticles' => $relatedArticles,
            'app' => [
                'request' => [
                    'pathInfo' => '/tigergym/article/' . $id
                ]
            ]
        ]);
    }

    public function category($category) {
        error_log("=== Début ArticleController::category ===");
        error_log("Catégorie demandée : '" . $category . "'");
        
        $validCategories = ['machines', 'vetements', 'vetements-hommes', 'vetements-femmes', 'complements'];
        
        if (!in_array($category, $validCategories)) {
            error_log("Catégorie invalide : '" . $category . "'");
            header('Location: /tigergym/');
            exit;
        }

        error_log("Récupération des articles pour la catégorie : '" . $category . "'");
        $articles = $this->articleModel->getArticlesByCategory($category);
        
        if (empty($articles)) {
            error_log("!!! ATTENTION : Aucun article trouvé pour la catégorie '" . $category . "'");
        } else {
            error_log("Nombre d'articles trouvés : " . count($articles));
            foreach ($articles as $article) {
                error_log("Article - ID: {$article['id']}, Nom: {$article['name']}, Catégorie: {$article['category']}");
            }
        }

        $displayCategory = $this->categoryMapping[$category] ?? ucfirst($category);
        $template = $this->templateMapping[$category] ?? $category . '.html.twig';
        
        error_log("Template utilisé : " . $template);
        error_log("Display category : " . $displayCategory);
        error_log("Données passées à la vue :");
        error_log("- category: " . $category);
        error_log("- displayCategory: " . $displayCategory);
        error_log("- articles: " . json_encode($articles));

        try {
            echo $this->twig->render($template, [
                'category' => $category,
                'displayCategory' => $displayCategory,
                'articles' => $articles,
                'app' => [
                    'request' => [
                        'pathInfo' => '/tigergym/' . $category
                    ]
                ]
            ]);
            error_log("=== Rendu de la vue réussi ===");
        } catch (\Exception $e) {
            error_log("!!! ERREUR lors du rendu de la vue : " . $e->getMessage());
            error_log("Stack trace : " . $e->getTraceAsString());
            throw $e;
        }
    }
}