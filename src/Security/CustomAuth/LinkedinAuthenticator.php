<?php

namespace App\Security\CustomAuth;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class LinkedinAuthenticator extends BaseCustomAuthenticator
{
    private string $source = 'linkedin';

    public function supports(Request $request): ?bool
    {
        return $request->attributes->get('_route') === 'connect_linkedin_check';
    }

    public function authenticate(Request $request): Passport
    {
        $client = $this->clientRegistry->getClient('linkedin_main');
        $accessToken = $this->fetchAccessToken($client);

        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function() use ($accessToken, $client) {

                $linkedinUser = $client->fetchUserFromToken($accessToken);
                $email = $linkedinUser->getEmail();

                $existingUser = $this->userRepository->findOneBy(['email' => $email, 'source' => $this->source]);

                if ($existingUser) {
                    return $existingUser;
                }

                return $this->userRepository->createFromCustomAuth([
                    'email' => $email,
                    'first_name' => $linkedinUser->getFirstName(),
                    'last_name' => $linkedinUser->getLastName(),
                    'source' => $this->source
                ]);
            })
        );
    }
}


