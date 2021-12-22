<?php

namespace App\Controller;

use App\Entity\Category;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

#[
    Route("/categorie", name: "category_"),
    IsGranted("ROLE_USER")
]
class CategorieController extends AbstractController
{

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @Security("is_granted(['ROLE_USER', 'ROLE_ADMIN'])")
     *
     * @return Response
     */
    #[Route('/', name: 'list', methods: "GET")]
    public function index(): Response
    {
        // Version 6
        $categories = $this->doctrine->getRepository(Category::class)->findAll();

        // Version 5
        // $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();

        return $this->render('categorie/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[
        Route("/save", name: "save", methods: ["POST", "GET"]),
        IsGranted("ROLE_ADMIN")
    ]
    public function save(Request $request): Response
    {
        // On créé un objet Category que l'on veut ajouter à la BDD
        $category = new Category;
        // On génère un formulaire grâce au controller auquel on associe la Category
        // On créé les champs de notre formulaire faisant référence à la Category
        $form = $this->createFormBuilder($category)
                    ->add('name', TextType::class)
                    ->add('Ajouter', SubmitType::class)
                    ->getForm();
        
        // On récupère l'objet Request qui contient toutes les informations venant d'un formulaire
        // et on les associe au formulaire généré
        $form->handleRequest($request);

        // Si le formulaire est soumit et a des données valides alors
        if ($form->isSubmitted() && $form->isValid()) {
            // On fait appel au ManagerRegistry, on l'utilise pour charger l'ObjectManager et ainsi
            $em = $this->doctrine->getManager();
            // On persist la catégory à ajouter en BDD
            $em->persist($category);
            // On l'ajoute en BDD
            $em->flush();

            return $this->redirectToRoute("category_single", ["id" => $category->getId()]);
        }

        return $this->render("categorie/save.html.twig", [
            'form' => $form->createView()
        ]);
    }

    #[Route("/single/{id}", name: "single", methods: "GET")]
    public function show(Category $category): Response
    {
        return $this->render('categorie/show.html.twig', [
            'category' => $category
        ]);
    }

    #[Route("/update/{id}", name: "update", methods: ["POST", "GET"])]
    public function update(Category $category, Request $request): Response
    {
        $this->denyAccessUnlessGranted("ROLE_SUPER_ADMIN");
        $form = $this->createFormBuilder($category)
            ->add('name', TextType::class, [
            "label" => "Nom de la catégorie" 
        ])
            ->add("Modifier", SubmitType::class)
            ->getForm();
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Symfony 6
            $em = $this->doctrine->getManager();
            // Symfony 5
            //$em = $this->getDoctrine()->getManager();

            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute("category_single", ['id' => $category->getId()]);
        }

        return $this->render("categorie/update.html.twig", [
            'form' => $form->createView()
        ]);
    }

    #[Route("/delete/{id}", name: "delete")]
    public function delete(Category $category): Response
    {
        if ($this->isGranted("ROLE_SUPER_ADMIN")) {
            $em = $this->doctrine->getManager();
            $em->remove($category);
            $em->flush();
        }
        return $this->redirectToRoute("category_list");
    }
}
