    <?php
session_start();
require 'vendor/autoload.php';

use Controllers\HomeController;
use Controllers\UserController;
use Controllers\ArticleController;
use Database\Database;
use Middlewares\AuthMiddleware;

// Configuration de Twig
$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/views');
$twig = new \Twig\Environment($loader, [
    'cache' => false,
    'debug' => true
]);

// Créer une instance du routeur
$router = new AltoRouter();

// Définir le chemin de base
$router->setBasePath('/tigergym');

// Définir les routes
$router->map('GET', '/', function () use ($twig) {
    $db = Database::getInstance();
    $homeController = new HomeController($db, $twig);
    $homeController->index();
});

// inscription
$router->map('GET', '/inscription', function () use ($twig) {
    $db = Database::getInstance();
    $userController = new UserController($db, $twig);
    $userController->inscription();
});

$router->map('POST', '/inscription', function () use ($twig) {
    $db = Database::getInstance();
    $userController = new UserController($db, $twig);
    $userController->inscription(); 
});

// connection
$router->map('GET', '/connection', function () use ($twig) {
    $db = Database::getInstance();
    $userController = new UserController($db, $twig);
    $userController->index();
});

$router->map('GET', '/deconnection', function () use ($twig) {
    $db = Database::getInstance();
    $userController = new UserController($db, $twig);
    $userController->deconnection();
});

$router->map('POST', '/connection', function () use ($twig) {
    $db = Database::getInstance();
    $userController = new UserController($db, $twig);
    $userController->connection();
});

// route Admin
$router->map('GET', '/admin', function () use ($twig) {
    AuthMiddleware::auth();
    $db = Database::getInstance();
    $userController = new UserController($db, $twig);
    $userController->admin();
});

// Routes pour les catégories
$db = Database::getInstance();
$articleController = new ArticleController($db, $twig);

$router->map('GET', '/machines', function() use ($articleController) {
    $articleController->category('machines');
});

$router->map('GET', '/vetements', function() use ($articleController) {
    $articleController->category('vetements');
});

$router->map('GET', '/vetements-hommes', function() use ($articleController) {
    $articleController->category('vetements-hommes');
});

$router->map('GET', '/vetements-femmes', function() use ($articleController) {
    $articleController->category('vetements-femmes');
});

$router->map('GET', '/complements', function() use ($articleController) {
    $articleController->category('complements');
});

$router->map('GET', '/article/[i:id]', function($id) use ($twig) {
    $db = Database::getInstance();
    $controller = new ArticleController($db, $twig);
    $controller->show($id);
});

$router->map('GET', '/categories/[*:category]', function($category) use ($twig) {
    $db = Database::getInstance();
    $controller = new ArticleController($db, $twig);
    $controller->category($category);
});

// Matcher et gérer la requête
$match = $router->match();

if (is_array($match) && is_callable($match['target'])) {
    call_user_func_array($match['target'], $match['params']);
} else {
    // Page 404
    header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
    echo $twig->render('404.html.twig');
}
