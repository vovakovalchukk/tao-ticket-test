<?php

namespace App\Security\CustomAuth;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class AmazonAuthenticator extends BaseCustomAuthenticator
{
    private string $source = 'amazon';

    public function supports(Request $request): ?bool
    {
        return $request->attributes->get('_route') === 'connect_amazon_check';
    }

    public function authenticate(Request $request): Passport
    {
        $client = $this->clientRegistry->getClient('amazon_main');
        $accessToken = $this->fetchAccessToken($client);

        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function() use ($accessToken, $client) {

                $amazonUser = $client->fetchUserFromToken($accessToken);
                $email = $amazonUser->getEmail();

                $existingUser = $this->userRepository->findOneBy(['email' => $email, 'source' => $this->source]);

                if ($existingUser) {
                    return $existingUser;
                }

                list($firstName, $lastName) = explode(' ', $amazonUser->getName());

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


