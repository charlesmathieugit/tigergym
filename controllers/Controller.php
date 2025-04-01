<?php

namespace Controllers;

use PDO;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\Extension\DebugExtension;

abstract class Controller
{
    protected $db;
    protected $twig;

    public function __construct(PDO $db, Environment $twig = null)
    {
        $this->db = $db;
        
        if ($twig === null) {
            $loader = new FilesystemLoader(__DIR__ . '/../views');
            $this->twig = new Environment($loader, [
                'debug' => true,
                'cache' => false
            ]);
            $this->twig->addExtension(new DebugExtension());
        } else {
            $this->twig = $twig;
        }
        
        // Ajouter une variable globale pour l'état de connexion
        $this->twig->addGlobal('user', [
            'is_logged_in' => isset($_SESSION['user_id']),
            'id' => $_SESSION['user_id'] ?? null,
            'email' => $_SESSION['user_email'] ?? null,
            'username' => $_SESSION['username'] ?? null
        ]);
    }

    protected function render($template, $data = [])
    {
        return $this->twig->render($template, $data);
    }
}
