<?php

namespace App\WebSocket\Handler;

use App\Mapper\FriendshipMapper;
use Ratchet\ConnectionInterface;
use App\Repository\UserRepository;
use App\Dto\Friendship\FriendshipCreateDTO;
use App\WebSocket\Connection\ConnectionRegistry;
use App\State\Friendship\FriendshipCreateProcessor;
use App\WebSocket\Contract\WebSocketHandlerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class FriendshipHandler implements WebSocketHandlerInterface
{
    public function __construct(
        private readonly FriendshipCreateProcessor $friendshipProcessor,
        private readonly SerializerInterface $serializer,
        private readonly ConnectionRegistry $connectionRegistry,
        private readonly UserRepository $userRepository,
        private readonly FriendshipMapper $friendshipMapper
    ) {}

    public function supports(string $type): bool
    {
        return $type === 'friend_request';
    }

    public function handle(ConnectionInterface $conn, array $message): void
    {
        try {
            if (!isset($conn->user) || !$conn->user instanceof UserInterface) {
                throw new \RuntimeException('Utilisateur non authentifié.');
            }

            $payload = $message['payload'] ?? null;
            if (!$payload || !isset($payload['friendCode'])) {
                throw new \InvalidArgumentException('Le champ friendCode est requis.');
            }

            $dto = new FriendshipCreateDTO();
            $dto->friendCode = $payload['friendCode'];

            $dtoOut = $this->friendshipProcessor->process($dto, $conn->user);

            $jsonData = $this->serializer->serialize($dtoOut, 'json', ['groups' => ['read:friendship']]);
            $decodedData = json_decode($jsonData, true);

            // 1. Envoi à l’émetteur de la demande (moi)
            $conn->send(json_encode([
                'type' => 'friend_request_success',
                'data' => $decodedData,
            ]));
            // 2. Envoi au destinataire (si connecté)
            $recipient = $dtoOut->friend;
            $friendCode = $recipient->friendCode ?? null;

            $targetUser = $this->connectionRegistry->getUserByFriendCode($friendCode);
            if ($targetUser) {
                $targetConn = $this->connectionRegistry->getConnection($targetUser->getId());

                if ($targetConn) {
                    $dtoForRecipient = clone $dtoOut;
                    // Transforme l'émetteur en DTO
                    $dtoForRecipient->friend = $this->friendshipMapper->mapUser($conn->user);

                    $jsonRecipient = $this->serializer->serialize($dtoForRecipient, 'json', ['groups' => ['read:friendship']]);
                    $decodedRecipient = json_decode($jsonRecipient, true);

                    $targetConn->send(json_encode([
                        'type' => 'friend_request_received',
                        'data' => $decodedRecipient,
                    ]));
                }
            }
        } catch (\Throwable $e) {
            $conn->send(json_encode([
                'type' => 'friend_request_error',
                'message' => $e->getMessage(),
            ]));
        }
    }
}
