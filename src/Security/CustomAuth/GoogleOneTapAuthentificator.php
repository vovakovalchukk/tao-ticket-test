<?php

namespace App\Security\CustomAuth;

use App\Repository\UserRepository;
use Firebase\JWT\JWT;
use Google_Client;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class GoogleOneTapAuthentificator extends BaseCustomAuthenticator
{
    private string $source = 'google';
    private ContainerBagInterface $containerBag;

    public function __construct(
        ClientRegistry $clientRegistry,
        RouterInterface $router,
        UserRepository $userRepository,
        ContainerBagInterface $containerBag
    ) {
        parent::__construct($clientRegistry, $router, $userRepository);
        $this->containerBag = $containerBag;
    }

    public function supports(Request $request): ?bool
    {
        return $request->attributes->get('_route') === 'connect_google_one_tap_check';
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function authenticate(Request $request): Passport
    {
        $jwt = new JWT;
        $jwt::$leeway = 1000;

        $client = new Google_Client([
            'client_id' => $this->containerBag->get('app.oauth_google_client_id', null),
            'jwt' =>  $jwt
        ]);

        $payload = $client->verifyIdToken($request->get('credential'));

        return new SelfValidatingPassport(
            new UserBadge($request->get('credential'), function() use ($payload) {

                $email = $payload['email'];

                $existingUser = $this->userRepository->findOneBy(['email' => $email, 'source' => $this->source]);

                if ($existingUser) {
                    return $existingUser;
                }

                $firstName = $payload['given_name'];
                $lastName = $payload['family_name'];

                return $this->userRepository->createFromCustomAuth([
                    'email' => $email,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'source' => $this->source
                ]);
            })
        );
    }
}


