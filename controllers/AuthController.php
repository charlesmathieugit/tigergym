<?php
namespace Controllers;

use Models\UserModel;
use PDO;

class AuthController extends Controller {
    private $userModel;

    public function __construct(PDO $db) {
        parent::__construct($db);
        $this->userModel = new UserModel($db);
    }

    public function showLoginForm() {
        $this->render('auth/connection.html.twig', [
            'title' => 'Connexion - TigerGym',
            'h1' => 'Connexion',
            'error' => $_SESSION['error'] ?? null
        ]);
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
                $_SESSION['user_id'] = $user->id;
                $_SESSION['user_email'] = $user->email;
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
        return $this->render('auth/register.html.twig', [
            'title' => 'Inscription - TigerGym',
            'h1' => 'Inscription',
            'error' => $_SESSION['error'] ?? null
        ]);
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userData = [
                'email' => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL),
                'password' => $_POST['password'] ?? '',
                'firstname' => htmlspecialchars($_POST['firstname'] ?? '', ENT_QUOTES, 'UTF-8'),
                'lastname' => htmlspecialchars($_POST['lastname'] ?? '', ENT_QUOTES, 'UTF-8')
            ];

            if (empty($userData['email']) || empty($userData['password']) || 
                empty($userData['firstname']) || empty($userData['lastname'])) {
                $_SESSION['error'] = 'Veuillez remplir tous les champs';
                header('Location: /tigergym/inscription');
                exit();
            }

            if (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = 'Email invalide';
                header('Location: /tigergym/inscription');
                exit();
            }

            if ($this->userModel->getUserByEmail($userData['email'])) {
                $_SESSION['error'] = 'Cet email est déjà utilisé';
                header('Location: /tigergym/inscription');
                exit();
            }

            if ($this->userModel->register($userData)) {
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