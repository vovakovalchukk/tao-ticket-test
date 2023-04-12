<?php

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\Provider\AzureClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AzureController extends AbstractController
{

    /**
     * Link to this controller to start the "connect" process
     *
     * @Route("/connect/azure", name="connect_azure")
     */
    public function connectAction(ClientRegistry $clientRegistry)
    {
        // will redirect to azure!
        return $clientRegistry
            ->getClient('azure_main') // key used in config/packages/knpu_oauth2_client.yaml
            ->redirect(['openid profile email offline_access https://graph.microsoft.com/.default'], []);
    }

    /**
     * After going to azure, you're redirected back here
     * because this is the "redirect_route" you configured
     * in config/packages/knpu_oauth2_client.yaml
     *
     * @Route("/connect/azure/check", name="connect_azure_check")
     */
    public function connectCheckAction(Request $request, ClientRegistry $clientRegistry)
    {
        // ** if you want to *authenticate* the user, then
        // leave this method blank and create a Guard authenticator
        // (read below)

        /** @var AzureClient $client */
        $client = $clientRegistry->getClient('azure_main');

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

    /*#[Route('/azure', name: 'app_azure')]
    public function index(): Response
    {



        return $this->render('azure/index.html.twig', [
            'controller_name' => 'azureController',
        ]);
    }*/
}
