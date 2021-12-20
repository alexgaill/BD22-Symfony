<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController {

    /**
     * Symfony s'attend toujours à retourner une response dans les controller pour les affichage de page
     *
     * @return Response
     */
    public function index():Response
    {
        return new Response("<h1>Hello World!</h1>");
    }

    /**
     * On va gérer notre routeur au fur et à mesure des méthodes qu'on créé.
     * ça se fait grâce au système d'annotations de symfony pris en compte pour toutes les versions de PHP
     * @Route("/bye", name="bye")
     * @return Response
     */
    public function bye(): Response
    {
        return new Response ("<h1>Bye!</h1>");
    }

    #[
        Route("/hello2", "hello2")
    ]
    public function hello2(): Response
    {
        return new Response("<h1>Hello World! ça va?</h1>");
    }

    #[Route("/template", "firstTemplate")]
    public function template():Response
    {
        return $this->render("test/index.html.twig", [
            "controller_name" => "TestController"
        ]);
    }
}