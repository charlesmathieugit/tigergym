<?php

namespace Controllers;

use PDO;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

abstract class Controller
{
    protected $db;
    protected $twig;

    public function __construct(PDO $db)
    {
        $this->db = $db;
        
        $loader = new FilesystemLoader(__DIR__ . '/../views');
        $this->twig = new Environment($loader, [
            'debug' => true,
            'cache' => false
        ]);
        
        // Ajouter une variable globale pour l'état de connexion
        $this->twig->addGlobal('user', [
            'is_logged_in' => isset($_SESSION['user_id']),
            'id' => $_SESSION['user_id'] ?? null,
            'email' => $_SESSION['user_email'] ?? null
        ]);
    }

    protected function render(string $template, array $data = []): string {
        try {
            echo $this->twig->render($template, $data);
        } catch (\Twig\Error\Error $e) {
            echo "Error rendering template: " . $e->getMessage();
            error_log($e->getMessage());
        }
    }

    public function setSession(string $key, $value): void {
        $_SESSION[$key] = $value;
    }

    public function getSession(string $key) {
        return $_SESSION[$key] ?? null;
    }

    public function unsetSession(string $key): void {
        unset($_SESSION[$key]);
    }
}
