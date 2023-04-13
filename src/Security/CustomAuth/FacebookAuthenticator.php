<?php

namespace App\Security\CustomAuth;

use League\OAuth2\Client\Provider\FacebookUser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class FacebookAuthenticator extends BaseCustomAuthenticator
{
    private string $source = 'facebook';

    public function supports(Request $request): ?bool
    {
        return $request->attributes->get('_route') === 'connect_facebook_check';
    }

    public function authenticate(Request $request): Passport
    {
        $client = $this->clientRegistry->getClient('facebook_main');
        $accessToken = $this->fetchAccessToken($client);

        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function() use ($accessToken, $client) {

                /** @var FacebookUser $facebookUser */
                $facebookUser = $client->fetchUserFromToken($accessToken);
                $email = $facebookUser->getEmail();

                $existingUser = $this->userRepository->findOneBy(['email' => $email, 'source' => $this->source]);

                if ($existingUser) {
                    return $existingUser;
                }

                return $this->userRepository->createFromCustomAuth([
                    'email' => $email,
                    'first_name' => $facebookUser->getFirstName(),
                    'last_name' => $facebookUser->getLastName(),
                    'source' => $this->source
                ]);
            })
        );
    }
}


