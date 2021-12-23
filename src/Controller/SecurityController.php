<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\User\ForgetType;
use App\Form\User\UpdatePasswordType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route("/forget", name:"app_forget_password")]
    public function forgetPassword(Request $request, UserRepository $repository, MailerInterface $mailer)
    {
        $user = new User();
        $form = $this->createForm(ForgetType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // On vérifie que l'utilisateur existe en BDD
            $verifyUser = $repository->findOneBy(["email" => $form->get("forgetEmail")->getData()]);
            if ($verifyUser) {
                // On envoie un email de modification de password
                $email = (new TemplatedEmail())
                    ->from("no-reply@blog.fr")
                    ->to($verifyUser->getEmail())
                    ->subject("Blog.fr | Mot de passe oublié")
                    ->htmlTemplate("email/forget.html.twig")
                    ->context([
                        "user" => $verifyUser
                    ]);
                $mailer->send($email);
            }
        }

        return $this->render("security/forget.html.twig", [
            'form' => $form->createView()
        ]);
    }

    #[Route("/update/password/{id}", name: "app_update_password")]
    public function updatePassword(User $user, Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $em)
    {
        dump($user);
        $form = $this->createForm(UpdatePasswordType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // On vérifie que les 2 password sont identiques
            if ($form->get("passwordOne")->getData() === $form->get("passwordBis")->getData()) {
                // On encode le password
                $encodedPassword = $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('passwordOne')->getData()
                );

                $user->setPassword($encodedPassword);

                // On update le user
                $em->persist($user);
                $em->flush();

                $this->addFlash("success", "Le mot de passe a été changé");
                return $this->redirectToRoute("category_list");
            } else {
                $this->addFlash("danger", "Les passwords ne sont pas identiques");
                return $this->redirectToRoute("app_update_password", ["id" => $user->getId()]);
            }
        }

        return $this->render("security/password.html.twig",
        [
            "form" => $form->createView()
        ]);
    }
}
