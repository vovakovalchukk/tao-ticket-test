<?php

namespace App\Controller;

use App\Form\LoginFormType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home()
    {
        return $this->render('base.html.twig');
    }

    /**
     * @Route("/login", name="login")
     */
    public function login(Request $request, AuthenticationUtils $authenticationUtils)
    {
        // Get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // Get the last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        // Create the login form
        /*$form = $this->createForm(LoginFormType::class, [
            'email' => $lastUsername,
        ]);*/

        // Handle the login form submission
        /*$form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Perform the authentication
            $data = $form->getData();

            return $this->redirectToRoute('home');
        }*/

        // Render the login form
        /*return $this->render('home.html.twig', [
            'login_form' => $form->createView(),
            'error' => $error,
        ]);*/

        return $this->render('login/index.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);

    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {
        // This code is never executed
    }
}
