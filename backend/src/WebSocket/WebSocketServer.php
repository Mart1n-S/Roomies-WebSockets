<?php

namespace App\WebSocket;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use App\WebSocket\Router\MessageRouter;
use App\Security\WebSocketAuthenticator;
use App\WebSocket\Handler\UserStatusHandler;
use App\State\Websocket\Group\GroupReadProvider;
use App\WebSocket\Connection\ConnectionRegistry;
use App\WebSocket\Connection\GlobalChatRegistry;
use App\WebSocket\Connection\GameRoomPlayersRegistry;
use Symfony\Component\Serializer\SerializerInterface;
use App\State\WebSocket\Group\PrivateRoomReadProvider;



class WebSocketServer implements MessageComponentInterface
{
    public function __construct(
        private readonly MessageRouter $router,
        private readonly WebSocketAuthenticator $authenticator,
        private readonly ConnectionRegistry $registry,
        private readonly GlobalChatRegistry $globalChatRegistry,
        private readonly GameRoomPlayersRegistry $gameRoomPlayersRegistry,
        private readonly GroupReadProvider $groupReadProvider,
        private readonly PrivateRoomReadProvider $privateRoomReadProvider,
        private readonly SerializerInterface $serializer,
        private readonly UserStatusHandler $userStatusHandler
    ) {}

    public function onOpen(ConnectionInterface $conn): void
    {
        echo "🔌 Nouvelle connexion WebSocket (non authentifiée)\n";
    }

    public function onMessage(ConnectionInterface $from, $msg): void
    {
        $data = json_decode($msg, true);

        if (!is_array($data) || !isset($data['type'])) {
            $from->send(json_encode([
                'type' => 'error',
                'message' => 'Format de message invalide.'
            ]));
            return;
        }

        if ($data['type'] === 'authenticate') {
            $token = $data['token'] ?? null;

            if (!$token) {
                $from->send(json_encode([
                    'type' => 'error',
                    'message' => 'Token manquant.'
                ]));
                $from->close();
                return;
            }

            try {
                echo "🔐 Tentative d'authentification avec token : $token\n";
                $user = $this->authenticator->authenticate($token);

                if (!$user) {
                    throw new \RuntimeException('Utilisateur introuvable.');
                }

                $from->user = $user;
                $this->registry->register($user->getId(), $from);

                $from->send(json_encode([
                    'type' => 'authenticated',
                    'message' => 'Connexion sécurisée.'
                ]));

                echo "✅ Utilisateur authentifié via message : {$user->getEmail()} (ID: {$user->getId()})\n";

                $this->userStatusHandler->notifyFriendsAboutStatusChange($from, true);
                $this->userStatusHandler->sendOnlineFriendsList($from);
                $this->userStatusHandler->notifyGroupMembersAboutStatusChange($from, true);
                $this->userStatusHandler->sendOnlineGroupMembersList($from);



                // Envoi des groupes
                $groups = $this->groupReadProvider->getGroupsForUser($user);

                $json = $this->serializer->serialize($groups, 'json', ['groups' => ['read:group']]);

                $from->send(json_encode([
                    'type' => 'init_groups',
                    'data' => json_decode($json, true),
                ]));

                // Envoi des discussions privées
                $privateRooms = $this->privateRoomReadProvider->getPrivateRoomsForUser($user);

                $privateJson = $this->serializer->serialize($privateRooms, 'json', [
                    'groups' => ['read:group'],
                ]);

                $from->send(json_encode([
                    'type' => 'init_private_rooms',
                    'data' => json_decode($privateJson, true),
                ]));
            } catch (\Throwable $e) {
                $from->send(json_encode([
                    'type' => 'error',
                    'message' => 'Token invalide ou expiré.'
                ]));
                echo "❌ Erreur d'auth WebSocket : " . $e->getMessage() . "\n";
                $from->close();
            }

            return;
        }

        if (!isset($from->user)) {
            $from->send(json_encode([
                'type' => 'error',
                'message' => 'Non authentifié.'
            ]));
            return;
        }

        // Route le message JSON au handler approprié
        $this->router->handle($from, $msg);
    }

    public function onClose(ConnectionInterface $conn): void
    {
        if (isset($conn->user)) {
            $this->registry->unregister($conn->user->getId());
            $this->userStatusHandler->notifyFriendsAboutStatusChange($conn, false);
            $this->userStatusHandler->notifyGroupMembersAboutStatusChange($conn, false);
            $this->globalChatRegistry->remove($conn);
            echo "🔴 Déconnexion de l'utilisateur ID {$conn->user->getId()}\n";
        } else {
            echo "🔌 Déconnexion d'un client non authentifié\n";
        }
    }

    public function onError(ConnectionInterface $conn, \Exception $e): void
    {
        echo "❌ Erreur WebSocket : " . $e->getMessage() . "\n";
        $conn->close();
    }
}
