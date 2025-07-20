<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Security\Exception\TooManyRequestsException;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

/**
 * Authenticator personnalisé pour l'API /api/login.
 *
 * Gère l'authentification des utilisateurs via un endpoint JSON.
 * Intègre un système de rate limiting pour éviter le bruteforce.
 */
class ApiAuthenticator extends AbstractAuthenticator
{
    private $entityManager;
    private $rateLimiterFactory;

    /**
     * Constructeur.
     *
     * @param EntityManagerInterface $entityManager
     * @param RateLimiterFactory $rateLimiterFactory
     */
    public function __construct(EntityManagerInterface $entityManager, RateLimiterFactory $rateLimiterFactory)
    {
        $this->entityManager = $entityManager;
        $this->rateLimiterFactory = $rateLimiterFactory;
    }

    /**
     * Détermine si l'authenticator doit être utilisé pour cette requête.
     *
     * @param Request $request
     * @return bool|null
     */
    public function supports(Request $request): ?bool
    {
        // L'authenticator s'applique uniquement pour les POST sur /api/login
        return $request->getMethod() === 'POST' && $request->getPathInfo() === '/api/login';
    }

    /**
     * Tente d'authentifier l'utilisateur à partir de la requête.
     *
     * @param Request $request
     * @return Passport
     * @throws AuthenticationException
     * @throws TooManyRequestsException
     */
    public function authenticate(Request $request): Passport
    {
        // Récupère les identifiants depuis le JSON de la requête
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        // Vérifie la présence des champs obligatoires
        if (!isset($email) || !isset($password)) {
            throw new AuthenticationException('L\'email et le mot de passe sont requis.');
        }

        // Combine l'email et l'IP pour le rate limiting
        $clientIp = $request->getClientIp();
        $limiterKey = $email . '_' . $clientIp;

        // Consomme un "jeton" du rate limiter (brute force protection)
        $limiter = $this->rateLimiterFactory->create($limiterKey);
        $limit = $limiter->consume();

        if (!$limit->isAccepted()) {
            // Trop de tentatives => on lève une exception personnalisée
            throw new TooManyRequestsException($limit->getRetryAfter()->getTimestamp(), 'Trop de tentatives de connexion. Veuillez réessayer dans :');
        }

        // Recherche l'utilisateur par email
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user) {
            throw new AuthenticationException('Les identifiants sont incorrects.');
        }

        // Vérification du mot de passe (hash PHP natif)
        if (!password_verify($password, $user->getPassword())) {
            throw new AuthenticationException('Les identifiants sont incorrects.');
        }

        // Vérification de l'activation du compte (email vérifié)
        if ($user->isVerified() === false) {
            throw new AuthenticationException('Votre compte n\'a pas été vérifié.');
        }

        // Authentification réussie : retourne un Passport valide
        return new SelfValidatingPassport(
            new UserBadge($user->getEmail())
        );
    }

    /**
     * Gère le succès d'authentification (peut être personnalisé, ici null = gestion par le firewall).
     *
     * @param Request $request
     * @param TokenInterface $token
     * @param string $firewallName
     * @return Response|null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // Null = laisser la main au système principal (JWT, etc.)
        return null;
    }

    /**
     * Gère l'échec d'authentification en retournant une réponse JSON adaptée.
     *
     * @param Request $request
     * @param AuthenticationException $exception
     * @return Response|null
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        // Cas particulier : rate limiting (HTTP 429)
        if ($exception instanceof TooManyRequestsException) {
            return new JsonResponse([
                'message' => $exception->getMessageKey()
            ], Response::HTTP_TOO_MANY_REQUESTS);
        }

        // Cas générique : credentials invalides (HTTP 401)
        return new JsonResponse([
            'message' => $exception->getMessage()
        ], Response::HTTP_UNAUTHORIZED);
    }
}
