<?php

namespace App\WebSocket;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use App\WebSocket\Router\MessageRouter;
use App\Security\WebSocketAuthenticator;
use App\WebSocket\Connection\ConnectionRegistry;
use App\State\Websocket\Group\GroupReadProvider;
use Symfony\Component\Serializer\SerializerInterface;
use App\WebSocket\Handler\UserStatusHandler;


class WebSocketServer implements MessageComponentInterface
{
    public function __construct(
        private readonly MessageRouter $router,
        private readonly WebSocketAuthenticator $authenticator,
        private readonly ConnectionRegistry $registry,
        private readonly GroupReadProvider $groupReadProvider,
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


                // Envoi des groupes
                $groups = $this->groupReadProvider->getGroupsForUser($user);

                $json = $this->serializer->serialize($groups, 'json', ['groups' => ['read:group']]);

                $from->send(json_encode([
                    'type' => 'init_groups',
                    'data' => json_decode($json, true),
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

        // ✅ Route le message JSON au handler approprié
        $this->router->handle($from, $msg);
    }

    public function onClose(ConnectionInterface $conn): void
    {
        if (isset($conn->user)) {
            $this->registry->unregister($conn->user->getId());
            $this->userStatusHandler->notifyFriendsAboutStatusChange($conn, false);
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






// class WebSocketServer implements MessageComponentInterface
// {
//     /** @var ConnectionInterface[] */
//     private array $connections = [];

//     public function __construct(
//         private readonly MessageRouter $router,
//         private readonly WebSocketAuthenticator $authenticator
//     ) {}

//     public function onOpen(ConnectionInterface $conn): void
//     {
//         $query = $conn->httpRequest->getUri()->getQuery();
//         parse_str($query, $params);
//         $token = $params['token'] ?? null;

//         if (!$token) {
//             $conn->close();
//             return;
//         }

//         try {
//             $user = $this->authenticator->authenticate($token);

//             if (!$user) {
//                 throw new \RuntimeException('Utilisateur introuvable.');
//             }

//             $conn->user = $user;
//             $this->connections[$user->getId()] = $conn;

//             echo "✅ Connexion WebSocket ouverte pour l'utilisateur {$user->getEmail()} (ID: {$user->getId()})\n";
//         } catch (\Throwable $e) {
//             echo "❌ Erreur lors du décodage JWT: " . $e->getMessage() . "\n";
//             $conn->close();
//         }
//     }

//     public function onMessage(ConnectionInterface $from, $msg): void
//     {
//         if (!isset($from->user)) {
//             $from->send(json_encode(['type' => 'error', 'message' => 'Utilisateur non authentifié.']));
//             return;
//         }

//         $this->router->handle($from, $msg);
//     }

//     public function onClose(ConnectionInterface $conn): void
//     {
//         if (isset($conn->user)) {
//             unset($this->connections[$conn->user->getId()]);
//             echo "🔴 Connexion WebSocket fermée pour l'utilisateur ID {$conn->user->getId()}\n";
//         }
//     }

//     public function onError(ConnectionInterface $conn, \Exception $e): void
//     {
//         echo "❌ Erreur WebSocket : " . $e->getMessage() . "\n";
//         $conn->close();
//     }

//     public function getConnectionForUserId(int $userId): ?ConnectionInterface
//     {
//         return $this->connections[$userId] ?? null;
//     }
// }





 // public function onOpen(ConnectionInterface $conn): void
    // {
    //     echo "🔔 onOpen triggered";
    //     try {
    //         $query = $conn->httpRequest->getUri()->getQuery();
    //         parse_str($query, $params);
    //         $token = $params['token'] ?? null;

    //         if (!$token) {
    //             $conn->send(json_encode(['type' => 'error', 'message' => 'Token WebSocket manquant']));
    //             $conn->close();
    //             return;
    //         }

    //         $user = $this->authenticator->authenticate($token);

    //         if (!$user) {
    //             $conn->send(json_encode(['type' => 'error', 'message' => 'Token invalide ou expiré']));
    //             $conn->close();
    //             return;
    //         }

    //         $conn->user = $user;
    //         $conn->authenticated = true;
    //         $this->connections[$user->getId()] = $conn;

    //         $conn->send(json_encode([
    //             'type' => 'auth_success',
    //             'user' => [
    //                 'id' => $user->getId(),
    //                 'email' => $user->getEmail(),
    //                 'friendCode' => $user->getFriendCode(),
    //             ]
    //         ]));

    //         echo "✅ Connexion WebSocket ouverte pour : " . $user->getEmail() . PHP_EOL;
    //     } catch (\Throwable $e) {
    //         $conn->send(json_encode([
    //             'type' => 'error',
    //             'message' => 'Erreur lors de l’ouverture de la connexion WebSocket.'
    //         ]));

    //         // Fermer proprement
    //         $conn->close();

    //         // Log technique côté serveur
    //         echo "❌ Exception dans onOpen() : " . $e->getMessage() . PHP_EOL;
    //     }
    // }


    // public function onOpen(ConnectionInterface $conn)
    // {
    //     // Extraire les cookies
    //     $cookies = $conn->httpRequest->getHeader('Cookie');

    //     $jwt = null;
    //     foreach ($cookies as $cookieHeader) {
    //         foreach (explode(';', $cookieHeader) as $cookie) {
    //             $parts = explode('=', trim($cookie), 2);
    //             if ($parts[0] === 'BEARER' && isset($parts[1])) {
    //                 $jwt = $parts[1];
    //                 break 2;
    //             }
    //         }
    //     }

    //     if (!$jwt) {
    //         $conn->send(json_encode(['error' => 'Aucun token JWT trouvé dans les cookies']));
    //         $conn->close();
    //         return;
    //     }
    //     echo "🔔 onOpen triggered" . PHP_EOL;
    //     $user = $this->authenticator->authenticate($jwt);
    //     if (!$user) {
    //         $conn->send(json_encode(['error' => 'Token invalide ou expiré']));
    //         $conn->close();
    //         return;
    //     }

    //     $conn->user = $user;
    //     echo "✅ Connexion WebSocket établie pour {$user->getEmail()}" . PHP_EOL;
    // }


    // public function onMessage(ConnectionInterface $from, $msg): void
    // {
    //     try {
    //         $data = json_decode($msg, true, 512, JSON_THROW_ON_ERROR);
    //     } catch (\JsonException $e) {
    //         $from->send(json_encode(['type' => 'error', 'message' => 'Message JSON invalide.']));
    //         return;
    //     }

    //     if (!isset($from->authenticated) || !$from->authenticated) {
    //         $from->send(json_encode(['type' => 'error', 'message' => 'Non authentifié.']));
    //         $from->close();
    //         return;
    //     }

    //     try {
    //         $this->router->handle($from, $data);
    //     } catch (\Throwable $e) {
    //         $from->send(json_encode(['type' => 'error', 'message' => 'Erreur traitement message.']));
    //         echo "❌ Erreur routeur : " . $e->getMessage() . PHP_EOL;
    //     }
    // }


// public function onMessage(ConnectionInterface $from, $msg): void
// {
//     $data = json_decode($msg, true);

//     // Étape d'authentification
//     if ($data['type'] === 'auth') {
//         try {
//             $token = $data['token'] ?? null;

//             if (!$token) {
//                 throw new \RuntimeException('Token manquant.');
//             }

//             $user = $this->authenticator->authenticate($token);

//             if (!$user) {
//                 throw new \RuntimeException('Token invalide.');
//             }

//             $from->user = $user;
//             $from->authenticated = true;
//             $this->connections[$user->getId()] = $from;

//             $from->send(json_encode([
//                 'type' => 'auth_success',
//                 'user' => [
//                     'id' => $user->getId(),
//                     'email' => $user->getEmail(),
//                     'friendCode' => $user->getFriendCode()
//                 ]
//             ]));

//             echo "🔓 Authentification réussie pour {$user->getEmail()} (ID: {$user->getId()})\n";
//         } catch (\Throwable $e) {
//             $from->send(json_encode(['type' => 'auth_error', 'message' => $e->getMessage()]));
//             $from->close();
//         }

//         return;
//     }

//     // Vérification : l'utilisateur doit être authentifié
//     if (!isset($from->authenticated) || !$from->authenticated) {
//         $from->send(json_encode(['type' => 'error', 'message' => 'Non authentifié.']));
//         $from->close();
//         return;
//     }

//     // Transmission au routeur
//     try {
//         $this->router->route($from, $data);
//     } catch (\Throwable $e) {
//         $from->send(json_encode(['type' => 'error', 'message' => $e->getMessage()]));
//     }
// }
