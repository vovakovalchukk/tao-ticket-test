<?php

namespace App\Controller;

use Exception;
use Symfony\Component\HttpFoundation\Request;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/', name: 'app_login')]
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

    #[Route(path: '/logout', name: 'logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/connect2/{service}", name="connect_service")
     * @throws Exception
     */
    public function connectService(string $service, ClientRegistry $clientRegistry): Response
    {
        if (!in_array($service, ['facebook_main', 'google'])) {
            throw new Exception('Invalid service');
        }

        return $clientRegistry
            ->getClient($service)
            ->redirect(['email'], []);
    }

    /**
     * @Route("/connect/facebook/check", name="connect_service_check")
     * @throws Exception
     */
    public function connectServiceCheck(string $service, Request $request, GuardAuthenticatorHandler $guardHandler, ClientRegistry $clientRegistry): Response
    {
        dd(123);
        if (!in_array($service, ['facebook_main', 'google'])) {
            throw new \Exception('Invalid service');
        }

        $client = $clientRegistry->getClient($service);
        $accessToken = $client->getAccessToken();
        $user = $client->fetchUserFromToken($accessToken);

        // Handle authentication using Symfony Authenticator
        $authenticator = new FacebookAuthenticator($this->entityManager);
        $credentials = $authenticator->getCredentials($request);
        $user = $authenticator->getUser($credentials, $this->userRepository);
        $authenticatedToken = $authenticator->createAuthenticatedToken($user, 'facebook');
        $guardHandler->authenticateWithToken($authenticatedToken, $request, 'main');

        return $this->redirectToRoute('home');
    }
}
