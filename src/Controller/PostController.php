<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\File\File;

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

            $picture = $form->get('picture')->getData();
            $pictureName = md5(uniqid()).'.'. $picture->guessExtension();

            $picture->move(
                // $this->getParameter permet de récupérer la valeur d'un paramètre définit dans le fichier
                // de config services.yaml
                $this->getParameter('upload_file'),
                $pictureName
            );
            $post->setPicture($pictureName);


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

    #[
        Route("/post/single/{id}", name: "post_single")
    ]
    public function single(Post $post)
    {
        return $this->render("post/single.html.twig", [
            "post" => $post
        ]);
    }

    #[Route("/post/update/{id}", name: "post_update")]
    public function update(Post $post, Request $request, EntityManagerInterface $em): Response
    {
        // On récupère le nom de l'ancienne image
        // $oldPicture = $post->getPicture();

        // On génère un nouveau post auquel on passe le nom de l'ancienne image
        $oldPost = new Post();
        $oldPost->setPicture($post->getPicture());
        
        // Si l'image n'est pas null en BDD, on génère un fichier à partir de l'image pour pouvoir
        // faire fonctionner le formulaire car l'input file attend un fichier et nom le nom d'un fichier
        if($post->getPicture() !== null){
            $picture = new File($this->getParameter("upload_file") ."/". $post->getPicture());
            $post->setPicture($picture);
        }

        $form = $this->createForm(PostType::class, $post)
                // On ajoute un champs hiden avec l'ancien nom de l'image
                // ->add("oldPicture", HiddenType::class, [
                //     'data' => $oldPicture,
                //     'mapped' => false
                // ])
                ;
        $form->handleRequest($request);
        
        
        if ($form->isSubmitted() && $form->isValid()) {
            // On récupère le nom de l'ancienne image sur l'input hiddent
            // $oldPicture = $form->get("oldPicture")->getData();

            // Si on soumet une nouvelle image
            if ($form->get("picture")->getData() !== null ) {
                // On supprime l'ancienne pour ne pas garder d'images inutiles
                unlink($this->getParameter("upload_file") . "/" . $oldPost->getPicture());
                // On déplace l'image, on génère un nom unique qu'on ajoute en BDD
                $picture = $form->get('picture')->getData();
                $pictureName = md5(uniqid()).'.'. $picture->guessExtension();
                $picture->move(
                    $this->getParameter('upload_file'),
                    $pictureName
                );
                $post->setPicture($pictureName);
            } else {
                // Si on ne soumet pas de nouvelle image,
                // On réenregistre le nom de l'ancienne image en BDD
                $post->setPicture($oldPost->getPicture());
                // $post->setPicture($oldPicture);
            }
            
            $em->persist($post);
            $em->flush();
            return $this->redirectToRoute("category_list");
        }

        return $this->render("post/update.html.twig", [
            "form" => $form->createView()
        ]);
    }
}
