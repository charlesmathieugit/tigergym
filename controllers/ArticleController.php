<?php

namespace Controllers;

use Controllers\Controller;
use Models\ArticleModel;
use Controllers\RatingController;
use Controllers\CommentsController;
use PDO;
use Twig\Environment;

class ArticleController extends Controller {
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

    public function __construct(PDO $db, Environment $twig = null) {
        parent::__construct($db, $twig);
        $this->articleModel = new ArticleModel($db);
    }

    public function show($id) {
        // Récupérer l'article
        $stmt = $this->db->prepare("
            SELECT * FROM articles WHERE id = :id
        ");
        $stmt->execute(['id' => $id]);
        $article = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$article) {
            http_response_code(404);
            echo "Article non trouvé";
            return;
        }

        // Récupérer les données de notation
        $ratingController = new RatingController($this->db, $this->twig);
        $ratingData = $ratingController->getArticleRatingData($id);
        
        error_log("=== Données de notation pour l'article $id ===");
        error_log("Note moyenne: " . $ratingData['average_rating']);
        error_log("Nombre de votes: " . $ratingData['rating_count']);
        error_log("Note de l'utilisateur: " . ($ratingData['user_rating'] ?? 'non noté'));
        
        // Récupérer les commentaires
        $commentController = new CommentsController($this->db, $this->twig);
        $commentData = $commentController->getArticleCommentData($id);

        // Préparer les données utilisateur
        $userData = [
            'user' => [
                'id' => $_SESSION['user_id'] ?? null,
                'is_logged_in' => isset($_SESSION['user_id']),
                'username' => $_SESSION['username'] ?? null
            ]
        ];

        // Fusionner toutes les données
        $data = array_merge(
            ['article' => array_merge($article, [
                'average_rating' => $ratingData['average_rating'],
                'rating_count' => $ratingData['rating_count'],
                'user_rating' => $ratingData['user_rating'],
                'comments' => $commentData['comments'] ?? [],
                'comment_count' => $commentData['comment_count'] ?? 0
            ])],
            $userData
        );

        error_log("=== Données envoyées au template ===");
        error_log(print_r($data, true));

        // Rendre la vue
        echo $this->twig->render('article.html.twig', $data);
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

        // Préparer les données utilisateur
        $userData = [
            'id' => $_SESSION['user_id'] ?? null,
            'is_logged_in' => isset($_SESSION['user_id']),
            'username' => $_SESSION['username'] ?? null
        ];

        try {
            echo $this->twig->render($template, [
                'articles' => $articles,
                'category' => $displayCategory,
                'user' => $userData,
                'app' => [
                    'debug' => true,
                    'user' => $userData
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