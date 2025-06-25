<?php

namespace App\State\User;

use App\Entity\User;
use App\Dto\User\UserCreateDTO;
use App\Repository\UserRepository;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use ApiPlatform\Doctrine\Common\State\PersistProcessor;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @implements ProcessorInterface<UserCreateDTO, User>
 */
final class UserCreateProcessor implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: PersistProcessor::class)]
        private ProcessorInterface $persistProcessor,
        private UserPasswordHasherInterface $passwordHasher,
        private UserRepository $userRepository,
    ) {}

    /**
     * Méthode principale appelée lors de l’opération POST /user.
     *
     * @param mixed $data DTO contenant les données d'inscription
     * @param Operation $operation Opération API en cours
     * @param array $uriVariables Variables de l'URL
     * @param array $context Contexte d'exécution
     * @return JsonResponse Une réponse JSON indiquant le succès de la création ou les erreurs de validation.
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): JsonResponse
    {
        // Vérification de sécurité
        if (!$data instanceof UserCreateDTO) {
            return new JsonResponse(['message' => 'Requête invalide.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $user = new User();
        $user->setEmail($data->email);
        $user->setPseudo($data->pseudo);
        $user->setRoles(['ROLE_USER']);

        // Avatar : si un fichier a été transmis, VichUploader va gérer le déplacement
        if ($data->avatar !== null) {
            $user->setAvatar($data->avatar); // UploadedFile sera géré automatiquement
        }

        // Hashage du mot de passe
        $hashedPassword = $this->passwordHasher->hashPassword($user, $data->password);
        $user->setPassword($hashedPassword);

        // Génération d'un code ami unique
        do {
            $friendCode = strtoupper(bin2hex(random_bytes(10)));
        } while ($this->userRepository->findOneBy(['friendCode' => $friendCode]) !== null);

        $user->setFriendCode($friendCode);

        // Persistance via API Platform
        $this->persistProcessor->process($user, $operation, $uriVariables, $context);

        return new JsonResponse([
            'message' => 'Utilisateur créé avec succès.'
        ], JsonResponse::HTTP_CREATED);
    }
}
