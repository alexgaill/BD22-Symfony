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
        $post = new Post;
        $form = $this->createForm(PostType::class, $post);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $post->setCreatedAt(new \DateTime());
            $em = $mr->getManager();
            $em->persist($post);
            $em->flush();

            $this->addFlash("success", "Article bien enregistré");

            return $this->redirectToRoute("category_list");
        }

        return $this->render("post/save.html.twig", [
            'form' => $form->createView()
        ]);

    }
}
