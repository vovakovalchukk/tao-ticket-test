<?php

namespace App\Controller\CustomAuth;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
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

    }
}
