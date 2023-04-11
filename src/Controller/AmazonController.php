<?php

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\Provider\AmazonClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AmazonController extends AbstractController
{

    /**
     * Link to this controller to start the "connect" process
     *
     * @Route("/connect/amazon", name="connect_amazon")
     */
    public function connectAction(ClientRegistry $clientRegistry)
    {
        // will redirect to amazon!
        return $clientRegistry
            ->getClient('amazon_main') // key used in config/packages/knpu_oauth2_client.yaml
            ->redirect(['profile'], []);
    }

    /**
     * After going to amazon, you're redirected back here
     * because this is the "redirect_route" you configured
     * in config/packages/knpu_oauth2_client.yaml
     *
     * @Route("/connect/amazon/check", name="connect_amazon_check")
     */
    public function connectCheckAction(Request $request, ClientRegistry $clientRegistry)
    {
        // ** if you want to *authenticate* the user, then
        // leave this method blank and create a Guard authenticator
        // (read below)

        /** @var AmazonClient $client */
        $client = $clientRegistry->getClient('amazon_main');

        //try {
            // the exact class depends on which provider you're using

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

    /*#[Route('/amazon', name: 'app_amazon')]
    public function index(): Response
    {



        return $this->render('amazon/index.html.twig', [
            'controller_name' => 'amazonController',
        ]);
    }*/
}
