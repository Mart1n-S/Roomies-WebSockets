<?php

namespace App\Controller;

use App\Entity\User;
use App\Security\EmailVerifier;
use App\Repository\UserRepository;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\EventListener\UserRegistrationListener;
use App\Repository\RoomUserRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Security\Exception\TooManyRequestsException;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    public function __construct(private EmailVerifier $emailVerifier, private UserRepository $userRepository, private UserRegistrationListener $userRegistrationListener, private RateLimiterFactory $rateLimiterFactory,  private readonly string $publicKeyPath, private string $privateKey) {}

    /**
     * puis le frontend decode les paramètres et les envoie à cette route en post
     *
     * @param Request $request
     * @return Response
     */
    #[Route('/api/verify-email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request): Response
    {
        // Vérification si l'ID est présent dans les paramètres de la requête
        $userId = $request->query->get('id');
        if (!$userId) {
            return new RedirectResponse('https://localhost:5173/verified-email?status=error&message=missing_id');
        }

        try {
            // Récupérer l'utilisateur par ID
            $user = $this->userRepository->findOneBy(['id' => $userId, 'isVerified' => false]);

            if (!$user) {
                return new RedirectResponse('https://localhost:5173/verified-email?status=error&message=user_not_found_or_already_verified');
            }

            // Valider et confirmer l'email
            $this->emailVerifier->handleEmailConfirmation($request, $user);

            return new RedirectResponse('https://localhost:5173/verified-email?status=success');
        } catch (VerifyEmailExceptionInterface $exception) {
            // Gestion des erreurs liées à la vérification de l'email
            return new RedirectResponse('https://localhost:5173/verified-email?status=error&code=expired_link');
        }
    }

    /**
     * Route pour demander un nouvel email de confirmation lorsqu'un utilisateur n'a pas encore confirmé son email 
     * et que le lien de confirmation a expiré.
     *
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/api/request-new-confirmation-email', name: 'request_new_confirmation_email', methods: ['POST'])]
    public function requestNewConfirmationEmail(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;

        if (!$email) {
            return new JsonResponse(['error' => 'L\'email est requis.'], 400);
        }

        // Générer une clé unique en fonction de l'email et de l'IP
        $clientIp = $request->getClientIp();
        $limiterKey = $email . '_' . $clientIp;

        // Appliquer le rate limiter avec la clé unique
        $limiter = $this->rateLimiterFactory->create($limiterKey);
        $limit = $limiter->consume();

        if (!$limit->isAccepted()) {
            throw new TooManyRequestsException($limit->getRetryAfter()->getTimestamp(), 'Trop de demandes d\'email de confirmation. Veuillez réessayer dans :');
        }

        // Trouver l'utilisateur par email
        $user = $this->userRepository->findOneBy(['email' => $email]);

        if (!$user) {
            // Ne pas révéler que l'utilisateur n'existe pas
            return new JsonResponse(['message' => 'Si un compte existe, un nouvel email de confirmation sera envoyé.'], 200);
        }

        // Vérifier si l'utilisateur a déjà confirmé son email
        if ($user->isVerified()) {
            return new JsonResponse(['error' => 'L\'email a déjà été confirmé.'], 400);
        }

        // Renvoyer un nouvel email de confirmation
        try {
            $emailMessage = (new TemplatedEmail())
                ->from(new Address('no-reply@roomies.com', 'Roomies'))
                ->to($user->getEmail())
                ->subject('Merci de confirmer votre email')
                ->htmlTemplate('emails/confirmation_email.html.twig');

            // Envoie l'email de confirmation
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user, $emailMessage);
        } catch (\Throwable $e) {
            error_log('Erreur d\'envoi d\'email: ' . $e->getMessage());

            return new JsonResponse(['error' => 'Échec de l\'envoi de l\'email de confirmation. Veuillez réessayer ultérieurement.'], 500);
        }

        return new JsonResponse(['message' => 'Si un compte existe, un nouvel email de confirmation sera envoyé.'], 200);
    }
    // TODO: a supprimer utiliser pour des test
    #[Route('/testWS', name: 'ws-test')]
    public function testWebSocket(): Response
    {
        $token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE3NTExOTQ1MzcsImV4cCI6MTc1MTE5ODEzNywiaWQiOiIxMjMiLCJ1c2VybmFtZSI6InVzZXJAZXhhbXBsZS5jb20iLCJyb2xlcyI6WyJST0xFX1VTRVIiXX0.VV5gHY1rqVvozzSWIZgo7QD5NEFU6rUj1e_m3R6-cHLN_jhs6kvmFAnXoKs26Lj8qTEqp8Z__8dJa59pOnIAcqrikq2m-G7xhOWolQ1GWH9m_976qfOkoG9L87BeyPHnW5qQaCKAOyqMRv9mLzo8y82Xpqhl3r-kg6xCUhcRH06ELuGnp2eLyNy25Yq4H3Tf0hKbyKAP1MxzENSh0pyYbj24C-BbfAbThBmtjyWc-Qd4MZqstQbiYjVlXpcifdRLGqG4NZH9hlSHi6nthn0Rfiimcn7o8fsgboiZyiroKQhAHXNjlfEBihlNgAyg6ThLlAtW3uqb6XBoVvfw2LgX-A";

        try {
            $publicKey = file_get_contents($this->publicKeyPath);

            if (!$publicKey) {
                return new Response('❌ Erreur : Clé publique non lue ou vide !', 500);
            }

            if (!str_contains($publicKey, 'BEGIN PUBLIC KEY')) {
                return new Response('❌ Erreur : Fichier n’est pas une clé publique RSA valide !', 500);
            }

            $decoded = \Firebase\JWT\JWT::decode($token, new \Firebase\JWT\Key($publicKey, 'RS256'));

            return new Response('<pre>' . print_r($decoded, true) . '</pre>');
        } catch (\Throwable $e) {
            return new Response('❌ Erreur : ' . $e->getMessage(), 400);
        }
    }

    #[Route('/g', name: 'ws-tesgt')]
    public function g()
    {
        $privateKey = file_get_contents($this->privateKey);
        $privateKeyResource = openssl_get_privatekey($privateKey);

        if ($privateKeyResource === false) {
            throw new \RuntimeException('Clé privée invalide ou erreur d\'initialisation OpenSSL');
        }


        $payload = [
            'iat' => time(),
            'exp' => time() + 3600,
            'id' => '123',
            'username' => 'user@example.com',
            'roles' => ['ROLE_USER'],
        ];

        $jwt = \Firebase\JWT\JWT::encode($payload, $privateKeyResource, 'RS256');

        echo $jwt;
    }

    // TODO: a supprimer utiliser pour des test
    #[Route('/room', name: 'ws-room')]
    public function groom(RoomUserRepository $roomUserRepository, UserRepository $userRepository)
    {
        $user = $userRepository->findOneBy(['email' => "user@user.com"]);

        $roomUsers = $roomUserRepository->findGroupsForUser($user);

        foreach ($roomUsers as $roomUser) {
            $room = $roomUser->getRoom();
            dump($room);
        }

        dd($roomUsers);
    }
}
