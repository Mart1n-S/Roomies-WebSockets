<?php

namespace App\WebSocket\Handler;

use App\Entity\User;
use Ratchet\ConnectionInterface;
use App\WebSocket\Contract\WebSocketHandlerInterface;
use App\Service\AvatarUrlGeneratorService;
use App\WebSocket\Connection\GameRoomPlayersRegistry;

/**
 * Handler WebSocket pour la gestion du chat dans une room de jeu (ex. Morpion, etc.).
 *
 * Permet à tout joueur connecté à une room d’envoyer un message de chat à tous les autres participants (joueurs et spectateurs).
 */
class GameChatHandler implements WebSocketHandlerInterface
{
    public function __construct(
        private readonly GameRoomPlayersRegistry $registry,
        private readonly AvatarUrlGeneratorService $avatarUrlGenerator
    ) {}

    /**
     * Indique si ce handler prend en charge le type de message reçu.
     *
     * @param string $type
     * @return bool
     */
    public function supports(string $type): bool
    {
        return $type === 'game_chat_message';
    }

    /**
     * Traite l’envoi d’un message de chat dans une room de jeu.
     *
     * @param ConnectionInterface $conn     Connexion WebSocket du joueur
     * @param array $message                Message reçu (doit contenir 'roomId' et 'content')
     */
    public function handle(ConnectionInterface $conn, array $message): void
    {
        /** @var User|null $user */
        $user = $conn->user ?? null;

        // Validation : utilisateur authentifié, roomId et content présents
        if (!$user || empty($message['roomId']) || empty($message['content'])) {
            return;
        }

        $roomId = (string) $message['roomId'];
        $userIdBin = $user->getId()->toBinary();

        // Sécurité : vérifier que l’utilisateur est bien dans la room (joueur ou spectateur)
        $connections = $this->registry->getPlayerConnections((int) $roomId);
        if (!isset($connections[$userIdBin])) {
            // L’utilisateur n’est pas autorisé à envoyer un message dans cette room
            return;
        }

        // Nettoyage et formatage du contenu
        $content = strip_tags($message['content']);

        // Préparation du payload à diffuser (avec métadonnées du sender)
        $payload = [
            'type' => 'game_chat_message',
            'message' => [
                'id' => uniqid('game_', true),
                'content' => $content,
                'createdAt' => (new \DateTime())->format(\DateTime::ATOM),
                'sender' => [
                    'friendCode' => $user->getFriendCode(),
                    'pseudo' => $user->getPseudo(),
                    'avatar' => $this->avatarUrlGenerator->generate($user),
                ],
                'roomId' => $roomId,
                'type' => 'Text',
            ],
        ];

        // Diffusion du message à tous les clients connectés à la room
        $this->broadcastToRoom($roomId, $payload);
    }

    /**
     * Diffuse le message à toutes les connexions présentes dans la room de jeu.
     *
     * @param string $roomId
     * @param array $payload
     */
    private function broadcastToRoom(string $roomId, array $payload): void
    {
        foreach ($this->registry->getConnectionsForRoom($roomId) as $conn) {
            $conn->send(json_encode($payload));
        }
    }
}
