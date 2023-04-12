<?php

namespace App\Controller\CustomAuth;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LinkedinController extends AbstractController
{

    /**
     * Link to this controller to start the "connect" process
     *
     * @Route("/connect/linkedin", name="connect_linkedin")
     */
    public function connectAction(ClientRegistry $clientRegistry)
    {
        // will redirect to linkedin!
        return $clientRegistry
            ->getClient('linkedin_main') // key used in config/packages/knpu_oauth2_client.yaml
            ->redirect(['r_liteprofile','r_emailaddress'], []);
    }

    /**
     * After going to linkedin, you're redirected back here
     * because this is the "redirect_route" you configured
     * in config/packages/knpu_oauth2_client.yaml
     *
     * @Route("/connect/linkedin/check", name="connect_linkedin_check")
     */
    public function connectCheckAction(Request $request, ClientRegistry $clientRegistry)
    {

    }
}
