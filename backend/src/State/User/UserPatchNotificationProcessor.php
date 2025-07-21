<?php

namespace App\State\User;

use App\Entity\User;
use App\Mapper\UserMapper;
use App\Dto\User\PushSubscriptionDTO;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use ApiPlatform\Doctrine\Common\State\PersistProcessor;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Processor pour l'enregistrement de la souscription push de l'utilisateur connecté.
 */
final class UserPatchNotificationProcessor implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: PersistProcessor::class)]
        private ProcessorInterface $persistProcessor,
        private TokenStorageInterface $tokenStorage,
        private NormalizerInterface $normalizer,
        private UserMapper $userMapper
    ) {}

    /**
     * Enregistre la souscription Push de l'utilisateur connecté
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): JsonResponse
    {
        $token = $this->tokenStorage->getToken();
        if (!$token || !is_object($token->getUser())) {
            return new JsonResponse(['message' => 'Non autorisé.'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        /** @var User $user */
        $user = $token->getUser();

        /** @var PushSubscriptionDTO $data */
        if (isset($data->pushNotificationsEnabled)) {
            $user->setPushNotificationsEnabled($data->pushNotificationsEnabled);

            // Si désactivation, vide les champs
            if (!$data->pushNotificationsEnabled) {
                $user->setPushEndpoint(null);
                $user->setPushAuth(null);
                $user->setPushP256dh(null);
            }
        }

        // Si présence des champs, alors on update
        if ($data->endpoint) $user->setPushEndpoint($data->endpoint);
        if ($data->auth)     $user->setPushAuth($data->auth);
        if ($data->p256dh)   $user->setPushP256dh($data->p256dh);

        $this->persistProcessor->process($user, $operation, $uriVariables, $context);

        $dto = $this->userMapper->toReadDto($user);

        return new JsonResponse(
            $this->normalizer->normalize($dto, null, ['groups' => ['read:me']]),
            JsonResponse::HTTP_OK
        );
    }
}
