<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PostController extends AbstractController
{
    #[Route('/post', name: 'post')]
    public function index(): Response
    {
        return $this->render('post/index.html.twig', [
            'controller_name' => 'PostController',
        ]);
    }

    #[Route('/post/save', name: 'post_save')]
    public function save(Request $request, ManagerRegistry $mr)
    {
        // On créé un nouvel article
        $post = new Post;
        // On appelle la class PostType qui contient les informations du formulaire pour ajouter un article
        // et on créé le formulaire lié
        $form = $this->createForm(PostType::class, $post);

        $form->handleRequest($request);

        // Si les données sont ok
        if ($form->isSubmitted() && $form->isValid()) {
            // On ajoute le createdAt à l'article
            $post->setCreatedAt(new \DateTime());
            // On le persist et l'enregistre en BDD
            $em = $mr->getManager();
            $em->persist($post);
            $em->flush();

            // On génère un message flash qui apparaîtra sur la page d'accueil pour valider l'enregistrement
            // de l'article auprès de l'utilisateur
            $this->addFlash("success", "Article bien enregistré");

            // On retourne sur la page d'accueil
            return $this->redirectToRoute("category_list");
        }
        // On charge le template save en lui passant le formulaire dont on a besoin
        // Attention le formulaire est toujours passé avec ->createView()
        return $this->render("post/save.html.twig", [
            'form' => $form->createView()
        ]);

    }
}
