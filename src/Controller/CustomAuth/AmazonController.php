<?php

namespace App\Controller\CustomAuth;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
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

    }
}
