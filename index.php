    <?php
    session_start();
    require 'vendor/autoload.php';

    use Controllers\HomeController;
    use Controllers\UserController;
    use Database\Database;
    use Middlewares\AuthMiddleware;

    // Créer une instance du routeur
    $router = new AltoRouter();

    // Définir le chemin de base
    $router->setBasePath('/base');




    // Définir les routes
    $router->map('GET', '/', function () {
        $db = Database::getInstance();
        $homeController = new HomeController($db);
        $homeController->index();
    });

   

    // inscription
    $router->map('GET', '/inscription', function () {
        
        $db = Database::getInstance();
        $userController = new UserController($db);
        $userController->inscription();
    });
    $router->map('POST', '/inscription', function () {
        
        $db = Database::getInstance();
        $userController = new UserController($db);
        $userController->inscription(); 
    });

    // connection
    $router->map('GET', '/connection', function () {
        
        $db = Database::getInstance();
        $userController = new UserController($db);
        $userController->index();
    });
    $router->map('GET', '/deconnection', function () {
        $db = Database::getInstance();
        $userController = new UserController($db);
        $userController ->deconnection();
    });
    $router->map('POST', '/connection', function () {
        
        $db = Database::getInstance();
        $userController = new UserController($db);
        $userController->connection();
    });

    // route Admin
    $router->map('GET', '/admin', function () {
      
        AuthMiddleware::auth();
        $db = Database::getInstance();
        $userController = new UserController($db);
        $userController->admin();
    });
   
    // Matcher et gérer la requête
    $match = $router->match();

    if (is_array($match) && is_callable($match['target'])) {
        // Débogage des paramètres de la route
        

        // Appeler la fonction cible
        call_user_func_array($match['target'], $match['params']);
    } else {
        // Aucun chemin correspondant, renvoyer une erreur 404
         ($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
        echo 'Page introuvable.';
    }
