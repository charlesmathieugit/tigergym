<?php

namespace Controllers;

use PDO;

class HomeController extends Controller
{
    public function __construct(PDO $database)
    {
        parent::__construct($database);
    }

    public function index()
    {
        $data = [
            "title" => "Page d'accueil",
            "h1" => "Bienvenue",
            "content" => "Ceci est la page d'accueil.",
            "fruits"=>["pommes","fraise"]
        ];

        $this->render("home.html.twig", $data);
    }
}
