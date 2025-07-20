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

/**
 * Handler WebSocket pour l’envoi de messages texte dans une room (privée ou de groupe).
 *
 * Fonctionnement :
 * - Valide l’authentification et la légitimité du membre dans la room.
 * - Crée et sauvegarde le message en base.
 * - Diffuse le message à tous les membres connectés de la room en temps réel.
 * - Envoie un feedback d’erreur à l’émetteur en cas de souci.
 */
class SendMessageHandler implements WebSocketHandlerInterface
{
    public function __construct(
        private readonly MessageRepository $messageRepository,
        private readonly RoomRepository $roomRepository,
        private readonly RoomUserRepository $roomMemberRepository,
        private readonly ConnectionRegistry $registry,
        private readonly AvatarUrlGeneratorService $avatarUrlGeneratorService,
    ) {}

    /**
     * Indique que ce handler gère le type de message 'message'
     */
    public function supports(string $type): bool
    {
        return $type === 'message';
    }

    /**
     * Traite la réception d’un message par WebSocket, le sauvegarde, et le broadcast à tous les membres.
     *
     * @param ConnectionInterface $conn   Connexion de l’expéditeur (WebSocket)
     * @param array $message              Doit contenir 'roomId' et 'content'
     */
    public function handle(ConnectionInterface $conn, array $message): void
    {
        try {
            // 1. Vérifie que l’utilisateur est bien connecté/authentifié
            if (!isset($conn->user) || !$conn->user instanceof UserInterface) {
                throw new \RuntimeException('Utilisateur non authentifié.');
            }

            $roomId = $message['roomId'] ?? null;
            $content = $message['content'] ?? null;

            // 2. Vérifie que les paramètres obligatoires sont présents
            if (!$roomId || !$content) {
                throw new \InvalidArgumentException('roomId et content sont requis.');
            }

            /** @var User $sender */
            $sender = $conn->user;

            // 3. Recherche la room cible et vérifie l’existence
            $room = $this->roomRepository->find($roomId);
            if (!$room) {
                throw new \RuntimeException("Room introuvable.");
            }

            // 4. Vérifie que l’utilisateur est bien membre de la room
            $isMember = $this->roomMemberRepository->findOneBy([
                'room' => $room,
                'user' => $sender
            ]);

            if (!$isMember) {
                throw new \RuntimeException("Tu ne fais pas partie de cette room.");
            }

            // 5. Crée et sauvegarde le message dans la base de données
            $chatMessage = new Message();
            $chatMessage->setRoom($room);
            $chatMessage->setSender($sender);
            $chatMessage->setContent($content);

            $this->messageRepository->save($chatMessage, true);

            // 6. Prépare le payload à envoyer à tous les membres (UX front : avatar, pseudo, etc.)
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

            // 7. Broadcast à tous les membres connectés de la room
            foreach ($room->getMembers() as $roomUser) {
                $user = $roomUser->getUser();
                if (!$user) continue;

                $connTarget = $this->registry->getConnection($user->getId());
                if ($connTarget) {
                    $connTarget->send(json_encode($response));
                }
            }
        } catch (\Throwable $e) {
            // 8. Gestion d’erreur : feedback explicite à l’expéditeur
            $conn->send(json_encode([
                'type' => 'send_message_error',
                'message' => $e->getMessage()
            ]));
        }
    }
}
