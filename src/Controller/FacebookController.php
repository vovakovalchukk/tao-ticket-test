<?php

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\Provider\FacebookClient;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\FacebookUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FacebookController extends AbstractController
{

    /**
     * Link to this controller to start the "connect" process
     *
     * @Route("/connect/facebook", name="connect_facebook")
     */
    public function connectAction(ClientRegistry $clientRegistry)
    {
        // will redirect to Facebook!
        return $clientRegistry
            ->getClient('facebook_main') // key used in config/packages/knpu_oauth2_client.yaml
            ->redirect([
                'public_profile', 'email' // the scopes you want to access
            ]);
    }

    /**
     * After going to Facebook, you're redirected back here
     * because this is the "redirect_route" you configured
     * in config/packages/knpu_oauth2_client.yaml
     *
     * @Route("/connect/facebook/check", name="connect_facebook_check")
     */
    public function connectCheckAction(Request $request, ClientRegistry $clientRegistry)
    {
        // ** if you want to *authenticate* the user, then
        // leave this method blank and create a Guard authenticator
        // (read below)

        /** @var FacebookClient $client */
        $client = $clientRegistry->getClient('facebook_main');

        //try {
            // the exact class depends on which provider you're using
            /** @var FacebookUser $user */
            //$user = $client->fetchUser();
            /*$accessToken = $client->getAccessToken();
            $provider = $client->getOAuth2Provider();
            $longLivedToken = $provider->getLongLivedAccessToken($accessToken);*/

            // do something with all this new power!
            // e.g. $name = $user->getFirstName();
            //dd($user/*, $longLivedToken*/); die;
            // ...
       // } catch (IdentityProviderException $e) {
            // something went wrong!
            // probably you should return the reason to the user
         //   dd($e->getMessage()); die;
        //}

        return $this->render('base.html.twig');
    }

    /*#[Route('/facebook', name: 'app_facebook')]
    public function index(): Response
    {



        return $this->render('facebook/index.html.twig', [
            'controller_name' => 'FacebookController',
        ]);
    }*/
}
