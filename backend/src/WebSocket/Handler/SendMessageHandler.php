<?php

namespace App\WebSocket\Handler;

use App\Entity\User;
use App\Entity\Message;
use Ratchet\ConnectionInterface;
use App\Repository\RoomRepository;
use App\Service\AvatarUrlGeneratorService;
use App\Repository\MessageRepository;
use App\Repository\RoomUserRepository;
use App\WebSocket\Connection\ConnectionRegistry;
use App\WebSocket\Contract\WebSocketHandlerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class SendMessageHandler implements WebSocketHandlerInterface
{
    public function __construct(
        private readonly MessageRepository $messageRepository,
        private readonly RoomRepository $roomRepository,
        private readonly RoomUserRepository $roomMemberRepository,
        private readonly ConnectionRegistry $registry,
        private readonly AvatarUrlGeneratorService $avatarUrlGeneratorService,
    ) {}

    public function supports(string $type): bool
    {
        return $type === 'message';
    }

    public function handle(ConnectionInterface $conn, array $message): void
    {
        try {
            if (!isset($conn->user) || !$conn->user instanceof UserInterface) {
                throw new \RuntimeException('Utilisateur non authentifié.');
            }

            $roomId = $message['roomId'] ?? null;
            $content = $message['content'] ?? null;

            if (!$roomId || !$content) {
                throw new \InvalidArgumentException('roomId et content sont requis.');
            }

            /** @var User $sender */
            $sender = $conn->user;

            $room = $this->roomRepository->find($roomId);
            if (!$room) {
                throw new \RuntimeException("Room introuvable.");
            }

            // Vérifie que le user est bien membre
            $isMember = $this->roomMemberRepository->findOneBy([
                'room' => $room,
                'user' => $sender
            ]);

            if (!$isMember) {
                throw new \RuntimeException("Tu ne fais pas partie de cette room.");
            }

            // Création et sauvegarde du message
            $chatMessage = new Message();
            $chatMessage->setRoom($room);
            $chatMessage->setSender($sender);
            $chatMessage->setContent($content);

            $this->messageRepository->save($chatMessage, true);

            // Payload à envoyer aux membres
            $response = [
                'type' => 'new_message',
                'message' => [
                    'id' => $chatMessage->getId(),
                    'roomId' => $room->getId(),
                    'content' => $chatMessage->getContent(),
                    'createdAt' => $chatMessage->getCreatedAt()->format(\DateTime::ATOM),
                    'sender' => [
                        'friendCode' => $sender->getFriendCode(),
                        'pseudo' => $sender->getPseudo(),
                        'avatar' => $this->avatarUrlGeneratorService->generate($sender),
                    ],
                ]
            ];

            // Envoi à tous les membres de la room
            foreach ($room->getMembers() as $roomUser) {
                $user = $roomUser->getUser();
                if (!$user) continue;

                $connTarget = $this->registry->getConnection($user->getId());
                if ($connTarget) {
                    $connTarget->send(json_encode($response));
                }
            }
        } catch (\Throwable $e) {
            $conn->send(json_encode([
                'type' => 'send_message_error',
                'message' => $e->getMessage()
            ]));
        }
    }
}
