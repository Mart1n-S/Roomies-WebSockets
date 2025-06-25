<?php

namespace App\State\User;

use App\Entity\User;
use App\Mapper\UserMapper;
use App\Dto\User\UserPatchDTO;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use ApiPlatform\Doctrine\Common\State\PersistProcessor;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @implements ProcessorInterface<UserPatchDTO, User>
 */
final class UserPatchProcessor implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: PersistProcessor::class)]
        private ProcessorInterface $persistProcessor,
        private UserPasswordHasherInterface $passwordHasher,
        private TokenStorageInterface $tokenStorage,
        private NormalizerInterface $normalizer,
        private UserMapper $userMapper
    ) {}

    /**
     * Traite la mise à jour d’un utilisateur connecté :
     * - Pseudo
     * - Mot de passe (avec vérification de l’ancien)
     * - Avatar (fichier image)
     *
     * @param mixed $data Le DTO de type UserPatchDTO
     * @param Operation $operation Métadonnées API Platform
     * @param array $uriVariables Non utilisé ici
     * @param array $context Contexte d'exécution
     * @return JsonResponse
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): JsonResponse
    {
        // Vérifier l'utilisateur connecté
        $token = $this->tokenStorage->getToken();
        if (!$token || !is_object($token->getUser())) {
            return new JsonResponse(['message' => 'Non autorisé.'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        /** @var User $user */
        $user = $token->getUser();

        /** @var UserPatchDTO $data */
        // Mise à jour du pseudo
        if ($data->pseudo !== null) {
            $user->setPseudo($data->pseudo);
        }

        // Mise à jour de l’avatar si un fichier est uploadé
        if ($data->avatar !== null) {
            $user->setAvatar($data->avatar); // VichUploader s'en charge
        }

        // Changement de mot de passe sécurisé
        if ($data->newPassword !== null) {
            if (empty($data->currentPassword)) {
                throw new BadRequestHttpException('Le mot de passe actuel est requis pour changer le mot de passe.');
            }

            if (!$this->passwordHasher->isPasswordValid($user, $data->currentPassword)) {
                throw new BadRequestHttpException('Le mot de passe actuel est incorrect.');
            }

            $user->setPassword($this->passwordHasher->hashPassword($user, $data->newPassword));
        }

        // Persistance en base de données
        $this->persistProcessor->process($user, $operation, $uriVariables, $context);

        // Création du DTO de réponse (avec URL absolue de l’avatar)
        $dto = $this->userMapper->toReadDto($user);

        return new JsonResponse($this->normalizer->normalize($dto, null, ['groups' => ['read:me']]), JsonResponse::HTTP_OK);
    }
}
