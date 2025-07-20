<?php

namespace App\WebSocket\Handler;

use App\Mapper\GameRoomMapper;
use Ratchet\ConnectionInterface;
use App\Repository\GameRoomRepository;
use App\WebSocket\Connection\GameRoomPlayersRegistry;
use App\WebSocket\Contract\WebSocketHandlerInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Handler WebSocket pour la récupération de la liste des rooms de jeu (lobby).
 *
 * Ce handler permet d’obtenir, en temps réel, la liste complète des rooms créées,
 * avec leurs informations sérialisées et le nombre de joueurs connectés dans chaque room.
 */
class GameRoomListHandler implements WebSocketHandlerInterface
{
    public function __construct(
        private readonly GameRoomRepository $gameRoomRepository,
        private readonly GameRoomMapper $gameRoomMapper,
        private readonly GameRoomPlayersRegistry $gameRoomPlayersRegistry,
        private readonly SerializerInterface $serializer
    ) {}

    /**
     * Détermine si ce handler prend en charge le type de message reçu.
     *
     * @param string $type
     * @return bool
     */
    public function supports(string $type): bool
    {
        return $type === 'game_room_list';
    }

    /**
     * Traite la demande de liste des rooms de jeu.
     *
     * Envoie au client la liste de toutes les rooms (avec leurs données DTO sérialisées et le nombre de joueurs connectés).
     *
     * @param ConnectionInterface $conn   Connexion du client WebSocket demandeur
     * @param array $message              Message reçu (contenu ignoré ici)
     */
    public function handle(ConnectionInterface $conn, array $message): void
    {
        try {
            // Récupère toutes les rooms depuis la base de données
            $rooms = $this->gameRoomRepository->findAll();

            $result = [];
            foreach ($rooms as $room) {
                // Map chaque room en DTO
                $dto = $this->gameRoomMapper->toReadDto($room);

                // Sérialise le DTO pour l’API front
                $jsonRoom = $this->serializer->serialize($dto, 'json', ['groups' => ['read:game_room']]);
                $arrRoom = json_decode($jsonRoom, true);

                // Ajoute le nombre de joueurs connectés dans cette room (pour affichage lobby)
                $arrRoom['playersCount'] = $this->gameRoomPlayersRegistry->getPlayerCount($room->getId());

                $result[] = $arrRoom;
            }

            // Envoie la liste complète au client demandeur
            $conn->send(json_encode([
                'type' => 'game_room_list',
                'rooms' => $result,
            ]));
        } catch (\Throwable $e) {
            // Gestion d’erreur : retourne un message explicite au client
            $conn->send(json_encode([
                'type' => 'game_room_error',
                'message' => $e->getMessage(),
            ]));
        }
    }
}
