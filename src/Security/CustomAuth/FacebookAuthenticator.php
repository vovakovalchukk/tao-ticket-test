<?php

namespace App\Security\CustomAuth;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use League\OAuth2\Client\Provider\FacebookUser;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class FacebookAuthenticator extends BaseCustomAuthenticator
{
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

                $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email, 'source' => 'facebook']);

                if ($existingUser) {
                    return $existingUser;
                }

                $user = new User();

                $user->setEmail($email);
                $user->setFirstName($facebookUser->getFirstName());
                $user->setLastName($facebookUser->getLastName());
                $user->setLastLogin(new \DateTime());
                $user->setPassword($this->passwordHasher->hashPassword($user, random_bytes(10)));
                $user->setSource('facebook');

                $this->entityManager->persist($user);
                $this->entityManager->flush();

                return $user;
            })
        );
    }
}


