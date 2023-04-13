<?php

namespace App\Security\CustomAuth;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class GoogleAuthenticator extends BaseCustomAuthenticator
{
    private string $source = 'google';
    public function supports(Request $request): ?bool
    {
        return $request->attributes->get('_route') === 'connect_google_check';
    }

    public function authenticate(Request $request): Passport
    {
        $client = $this->clientRegistry->getClient('google_main');
        $accessToken = $this->fetchAccessToken($client);

        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function() use ($accessToken, $client) {

                $googleUser = $client->fetchUserFromToken($accessToken)->toArray();
                $email = $googleUser['email'];

                $existingUser = $this->userRepository->findOneBy(['email' => $email, 'source' => $this->source]);

                if ($existingUser) {
                    return $existingUser;
                }

                return $this->userRepository->createFromCustomAuth([
                    'email' => $email,
                    'first_name' => $googleUser['given_name'],
                    'last_name' => $googleUser['family_name'],
                    'source' => $this->source
                ]);
            })
        );
    }
}


