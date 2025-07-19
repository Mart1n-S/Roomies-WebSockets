<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Message;
use App\Entity\Friendship;
use App\Security\EmailVerifier;
use App\Repository\RoomRepository;
use App\Repository\UserRepository;
use Symfony\Component\Mime\Address;
use App\Repository\MessageRepository;
use App\Repository\RoomUserRepository;
use App\Repository\FriendshipRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\EventListener\UserRegistrationListener;
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
}
