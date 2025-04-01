<?php
namespace Controllers;

use Models\UserModel;
use PDO;
use Twig\Environment;

class AuthController extends Controller {
    private $userModel;

    public function __construct(PDO $db, Environment $twig = null) {
        parent::__construct($db, $twig);
        $this->userModel = new UserModel($db);
    }

    public function showLoginForm() {
        echo $this->render('auth/connection.html.twig', [
            'title' => 'Connexion - TigerGym',
            'h1' => 'Connexion',
            'error' => $_SESSION['error'] ?? null
        ]);
        
        // Effacer le message d'erreur après l'avoir affiché
        unset($_SESSION['error']);
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                $_SESSION['error'] = 'Veuillez remplir tous les champs';
                header('Location: /tigergym/connexion');
                exit();
            }

            if ($user = $this->userModel->authenticate($email, $password)) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['is_logged_in'] = true;
                unset($_SESSION['error']);
                header('Location: /tigergym');
                exit();
            } else {
                $_SESSION['error'] = 'Email ou mot de passe incorrect';
                header('Location: /tigergym/connexion');
                exit();
            }
        }
    }

    public function showRegisterForm() {
        echo $this->render('auth/inscription.html.twig', [
            'title' => 'Inscription - TigerGym',
            'h1' => 'Inscription',
            'error' => $_SESSION['error'] ?? null
        ]);
        
        // Effacer le message d'erreur après l'avoir affiché
        unset($_SESSION['error']);
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? '';
            $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);

            error_log("=== Données d'inscription ===");
            error_log("Email: " . $email);
            error_log("Username: " . $username);
            error_log("Password présent: " . (!empty($password) ? 'oui' : 'non'));

            if (empty($email) || empty($password) || empty($username)) {
                $_SESSION['error'] = 'Veuillez remplir tous les champs';
                header('Location: /tigergym/inscription');
                exit();
            }

            // Vérifier si l'email existe déjà
            if ($this->userModel->getUserByEmail($email)) {
                $_SESSION['error'] = 'Cet email est déjà utilisé';
                header('Location: /tigergym/inscription');
                exit();
            }

            // Créer l'utilisateur
            if ($this->userModel->register($email, $password, $username)) {
                $_SESSION['success'] = 'Inscription réussie ! Vous pouvez maintenant vous connecter.';
                header('Location: /tigergym/connexion');
                exit();
            } else {
                $_SESSION['error'] = 'Une erreur est survenue lors de l\'inscription';
                header('Location: /tigergym/inscription');
                exit();
            }
        }
    }

    public function logout() {
        // Supprimer toutes les variables de session
        session_unset();
        
        // Détruire la session
        session_destroy();
        
        // Rediriger vers la page d'accueil
        header('Location: /tigergym');
        exit();
    }
}