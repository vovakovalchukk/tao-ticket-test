<?php

namespace App\Security\CustomAuth;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
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

class LinkedinAuthenticator extends BaseCustomAuthenticator
{
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

                $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email, 'source' => 'linkedin']);

                if ($existingUser) {
                    return $existingUser;
                }

                $user = new User();

                $user->setEmail($email);
                $user->setFirstName($linkedinUser->getFirstName());
                $user->setLastName($linkedinUser->getLastName());
                $user->setLastLogin(new \DateTime());
                $user->setPassword($this->passwordHasher->hashPassword($user, random_bytes(10)));
                $user->setSource('linkedin');

                $this->entityManager->persist($user);
                $this->entityManager->flush();

                return $user;
            })
        );
    }
}


