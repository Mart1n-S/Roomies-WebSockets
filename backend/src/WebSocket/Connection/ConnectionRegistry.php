<?php

namespace App\WebSocket\Connection;

use App\Entity\User;
use Symfony\Component\Uid\Uuid;
use Ratchet\ConnectionInterface;

/**
 * Registre centralisant toutes les connexions WebSocket ouvertes par utilisateur.
 *
 * Permet de gérer les connexions actives, leur récupération rapide,
 * et d’effectuer des recherches ou actions ciblées (ex : envoyer un message privé).
 */
class ConnectionRegistry
{
    /** @var array<string, ConnectionInterface> Associe chaque userId (en binaire) à sa connexion WebSocket active */
    private array $connections = [];

    /**
     * Enregistre une nouvelle connexion WebSocket pour un utilisateur.
     *
     * @param Uuid $userId                  Identifiant utilisateur (UUID)
     * @param ConnectionInterface $conn      Connexion WebSocket Ratchet
     */
    public function register(Uuid $userId, ConnectionInterface $conn): void
    {
        $this->connections[$userId->toBinary()] = $conn;
    }

    /**
     * Supprime la connexion d’un utilisateur du registre (lors de la déconnexion).
     *
     * @param Uuid $userId
     */
    public function unregister(Uuid $userId): void
    {
        unset($this->connections[$userId->toBinary()]);
    }

    /**
     * Récupère la connexion active pour un utilisateur donné.
     *
     * @param Uuid $userId
     * @return ConnectionInterface|null
     */
    public function getConnection(Uuid $userId): ?ConnectionInterface
    {
        return $this->connections[$userId->toBinary()] ?? null;
    }

    /**
     * Retourne tous les identifiants utilisateurs actuellement connectés.
     *
     * @return string[] (UUID binaire)
     */
    public function getAllConnectedUserIds(): array
    {
        return array_keys($this->connections);
    }

    /**
     * Indique si un utilisateur est actuellement connecté via WebSocket.
     *
     * @param Uuid $userId
     * @return bool
     */
    public function isConnected(Uuid $userId): bool
    {
        return isset($this->connections[$userId->toBinary()]);
    }

    /**
     * Recherche un utilisateur connecté à partir de son friendCode.
     *
     * @param string $friendCode
     * @return User|null
     */
    public function getUserByFriendCode(string $friendCode): ?User
    {
        foreach ($this->connections as $conn) {
            // On suppose que la propriété "user" est définie sur la connexion
            if (isset($conn->user) && $conn->user instanceof User) {
                if ($conn->user->getFriendCode() === $friendCode) {
                    return $conn->user;
                }
            }
        }
        return null;
    }
}
