<?php

namespace App\WebSocket\Handler;

use Ratchet\ConnectionInterface;
use App\Repository\RoomRepository;
use App\Repository\UserRepository;
use App\Repository\FriendshipRepository;
use App\WebSocket\Connection\ConnectionRegistry;
use App\WebSocket\Contract\WebSocketHandlerInterface;

class UserStatusHandler implements WebSocketHandlerInterface
{
    public function __construct(
        private readonly ConnectionRegistry $registry,
        private readonly UserRepository $userRepository,
        private readonly FriendshipRepository $friendshipRepository,
        private readonly RoomRepository $roomRepository,
    ) {}

    public function supports(string $type): bool
    {
        // On ne l'appelle pas manuellement, mais on veut pouvoir l’enregistrer
        return false;
    }

    public function handle(ConnectionInterface $conn, array $message): void
    {
        // Pas utilisé ici
    }

    public function notifyFriendsAboutStatusChange(ConnectionInterface $conn, bool $online): void
    {
        $user = $conn->user;
        if (!$user) return;

        $friendships = $this->friendshipRepository->findConfirmedFriendshipsForUser($user);

        foreach ($friendships as $friendship) {
            $friend = $friendship->getApplicant() === $user
                ? $friendship->getRecipient()
                : $friendship->getApplicant();

            $receiverConn = $this->registry->getConnection($friend->getId());
            if ($receiverConn) {
                $receiverConn->send(json_encode([
                    'type' => 'user-status',
                    'friendCode' => $user->getFriendCode(),
                    'online' => $online
                ]));
            }
        }
    }

    public function sendOnlineFriendsList(ConnectionInterface $conn): void
    {
        $user = $conn->user;
        if (!$user) return;

        $friendships = $this->friendshipRepository->findConfirmedFriendshipsForUser($user);

        $onlineFriendCodes = [];
        foreach ($friendships as $friendship) {
            $friend = $friendship->getApplicant() === $user
                ? $friendship->getRecipient()
                : $friendship->getApplicant();
            if ($this->registry->isConnected($friend->getId())) {
                $onlineFriendCodes[] = $friend->getFriendCode();
            }
        }

        $conn->send(json_encode([
            'type' => 'bulk-status',
            'onlineFriends' => $onlineFriendCodes
        ]));
    }

    public function notifyGroupMembersAboutStatusChange(ConnectionInterface $conn, bool $online): void
    {
        $user = $conn->user;
        if (!$user) return;

        $rooms = $this->roomRepository->findGroupsForUser($user);
        $sentTo = new \SplObjectStorage();

        foreach ($rooms as $room) {
            foreach ($room->getMembers() as $membership) {
                $member = $membership->getUser();

                if ($member->getId() === $user->getId()) {
                    continue;
                }

                $receiverConn = $this->registry->getConnection($member->getId());

                if ($receiverConn && !$sentTo->contains($receiverConn)) {
                    $receiverConn->send(json_encode([
                        'type' => 'user-status',
                        'friendCode' => $user->getFriendCode(),
                        'online' => $online
                    ]));

                    $sentTo->attach($receiverConn);
                }
            }
        }
    }

    public function sendOnlineGroupMembersList(ConnectionInterface $conn): void
    {
        $user = $conn->user;
        if (!$user) return;

        $rooms = $this->roomRepository->findGroupsForUser($user);
        $onlineCodes = [];

        foreach ($rooms as $room) {
            foreach ($room->getMembers() as $membership) {
                $member = $membership->getUser();

                if ($this->registry->isConnected($member->getId())) {
                    $onlineCodes[] = $member->getFriendCode();
                }
            }
        }

        $conn->send(json_encode([
            'type' => 'bulk-status',
            'onlineFriends' => array_values(array_unique($onlineCodes))
        ]));
    }
}
