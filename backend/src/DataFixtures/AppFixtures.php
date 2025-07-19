<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Enum\Game;

use App\Entity\GameRoom;

use App\Entity\Friendship;
use App\Entity\Message;
use App\Entity\Room;
use App\Entity\RoomUser;
use App\Enum\RoomRole;
use App\Enum\FriendshipStatus;
use App\Enum\MessageType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as Faker;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher) {}

    public function load(ObjectManager $manager): void
    {
        $faker = Faker::create('fr_FR');
        $users = [];

        $avatarNames = [
            'default-avatar2.svg',
            'default-avatar3.svg',
            'default-avatar4.svg',
            'default-avatar5.svg',
        ];

        $reservedFriendCodes = [
            '47A350833EA98050C4E9', // Ami 1
            '581193634B58880F5ECE', // Ami 2
            '03BDD87491F927C8E43C', // Non ami
            '7699D702D32C6C31A652', // userClassique (exemple, change si tu veux)
        ];

        // 1. Générer 20 utilisateurs uniques
        for ($i = 0; $i < 20; $i++) {
            do {
                $friendCode = strtoupper(bin2hex(random_bytes(10)));
            } while (
                in_array($friendCode, $reservedFriendCodes) ||
                in_array($friendCode, array_map(fn($u) => $u->getFriendCode(), $users))
            );

            $user = new User();
            $user->setPseudo($faker->unique()->userName);
            $user->setIsVerified(true);
            $user->setEmail($faker->unique()->safeEmail);
            $user->setFriendCode($friendCode);
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));
            $user->setImageName($faker->randomElement($avatarNames));
            $manager->persist($user);
            $users[] = $user;
        }

        // 2. Ajouter user@user.com (toujours en dernier)
        $userClassique = new User();
        $userClassique->setPseudo('userClassique');
        $userClassique->setIsVerified(true);
        $userClassique->setEmail('user@user.com');
        $userClassique->setFriendCode('7699D702D32C6C31A652');
        $userClassique->setPassword($this->passwordHasher->hashPassword($userClassique, 'password'));
        $manager->persist($userClassique);
        $users[] = $userClassique;

        // 2.1 Ajouter des amis fixes
        $friend1 = new User();
        $friend1->setPseudo('ami_fixe_1');
        $friend1->setIsVerified(true);
        $friend1->setEmail('ami1@roomies.test');
        $friend1->setFriendCode('47A350833EA98050C4E9');
        $friend1->setPassword($this->passwordHasher->hashPassword($friend1, 'password'));
        $friend1->setImageName($faker->randomElement($avatarNames));
        $manager->persist($friend1);
        $users[] = $friend1;

        $friend2 = new User();
        $friend2->setPseudo('ami_fixe_2');
        $friend2->setIsVerified(true);
        $friend2->setEmail('ami2@roomies.test');
        $friend2->setFriendCode('581193634B58880F5ECE');
        $friend2->setPassword($this->passwordHasher->hashPassword($friend2, 'password'));
        $friend2->setImageName($faker->randomElement($avatarNames));
        $manager->persist($friend2);
        $users[] = $friend2;

        // 2.2 Ajouter un utilisateur non ami fixe
        $nonFriend = new User();
        $nonFriend->setPseudo('non_ami_fixe');
        $nonFriend->setIsVerified(true);
        $nonFriend->setEmail('nonami@roomies.test');
        $nonFriend->setFriendCode('03BDD87491F927C8E43C');
        $nonFriend->setPassword($this->passwordHasher->hashPassword($nonFriend, 'password'));
        $nonFriend->setImageName($faker->randomElement($avatarNames));
        $manager->persist($nonFriend);
        $users[] = $nonFriend;


        // 3. Générer les amitiés SANS doublon ni inversion (bidirectionnel unique)
        $friendships = [];
        $friendPairs = []; // Stocke les [codeA, codeB]

        // a) user@user.com doit avoir exactement 2 amis choisis dans les 20 premiers
        $availableFriends = $users;
        array_pop($availableFriends); // on retire userClassique
        shuffle($availableFriends);
        $userFriends = array_slice($availableFriends, 0, 2);

        foreach ($userFriends as $friend) {
            $pair = [$userClassique->getFriendCode(), $friend->getFriendCode()];
            $friendPairs[] = $pair;
            $friendship = new Friendship();
            $friendship->setApplicant($userClassique);
            $friendship->setRecipient($friend);
            $friendship->setStatus(FriendshipStatus::Friend);
            $manager->persist($friendship);
            $friendships[] = $friendship;
        }
        // a.2) On ajoute les amis de userClassique dans la liste des utilisateurs
        $userFriendsFix = [$friend1, $friend2];
        foreach ($userFriendsFix as $friend) {
            $pair = [$userClassique->getFriendCode(), $friend->getFriendCode()];
            $friendPairs[] = $pair;
            $friendship = new Friendship();
            $friendship->setApplicant($userClassique);
            $friendship->setRecipient($friend);
            $friendship->setStatus(FriendshipStatus::Friend);
            $manager->persist($friendship);
            $friendships[] = $friendship;
        }

        // b) Pour les autres : 1 à 2 amis chacun, sans doublons ni inversion
        foreach ($users as $user) {
            if ($user->getEmail() === 'user@user.com') continue;
            $otherUsers = array_filter($users, fn($u) => $u !== $user);

            // Prend tous ceux déjà amis pour éviter de les recréer
            $alreadyFriendsWith = [$user->getFriendCode()];
            foreach ($friendPairs as $pair) {
                if ($pair[0] === $user->getFriendCode()) $alreadyFriendsWith[] = $pair[1];
                if ($pair[1] === $user->getFriendCode()) $alreadyFriendsWith[] = $pair[0];
            }

            $possibleFriends = array_values(
                array_filter($otherUsers, fn($u) => !in_array($u->getFriendCode(), $alreadyFriendsWith, true))
            );

            $n = min(mt_rand(1, 2), count($possibleFriends));
            if ($n === 0) continue;

            shuffle($possibleFriends);
            $toAdd = array_slice($possibleFriends, 0, $n);

            foreach ($toAdd as $friend) {
                // Empêche le doublon/inversion
                $pairCodes = [$user->getFriendCode(), $friend->getFriendCode()];
                $pairCodesSorted = $pairCodes;
                sort($pairCodesSorted, SORT_STRING);
                $exists = false;
                foreach ($friendPairs as $p) {
                    $pSorted = $p;
                    sort($pSorted, SORT_STRING);
                    if ($pSorted === $pairCodesSorted) {
                        $exists = true;
                        break;
                    }
                }
                if ($exists) continue;

                $friendship = new Friendship();
                $friendship->setApplicant($user);
                $friendship->setRecipient($friend);
                $friendship->setStatus(FriendshipStatus::Friend);
                $manager->persist($friendship);
                $friendships[] = $friendship;
                $friendPairs[] = $pairCodes;
            }
        }

        // 4. Pour CHAQUE amitié, créer un private room (isGroup=false), unique (pas de doublon pour 2 users)
        $privateRoomPairs = [];
        $privateRooms = [];
        $roomUsers = [];

        foreach ($friendships as $friendship) {
            $a = $friendship->getApplicant();
            $b = $friendship->getRecipient();

            $codes = [$a->getFriendCode(), $b->getFriendCode()];
            sort($codes, SORT_STRING);
            $pairKey = implode('-', $codes);

            if (isset($privateRoomPairs[$pairKey])) continue;

            $room = new Room();
            $room->setIsGroup(false); // nom = null par défaut
            $manager->persist($room);
            $privateRooms[] = $room;
            $privateRoomPairs[$pairKey] = $room;

            foreach ([$a, $b] as $user) {
                $roomUser = new RoomUser();
                $roomUser->setUser($user);
                $roomUser->setRoom($room);
                $roomUser->setRole(RoomRole::User);
                $manager->persist($roomUser);
                $roomUsers[spl_object_hash($room)][] = $user;
            }
        }

        foreach ($userFriendsFix as $friend) {
            $codes = [$userClassique->getFriendCode(), $friend->getFriendCode()];
            sort($codes, SORT_STRING);
            $pairKey = implode('-', $codes);

            if (isset($privateRoomPairs[$pairKey])) continue;

            $room = new Room();
            $room->setIsGroup(false); // nom = null par défaut
            $manager->persist($room);
            $privateRooms[] = $room;
            $privateRoomPairs[$pairKey] = $room;

            foreach ([$userClassique, $friend] as $user) {
                $roomUser = new RoomUser();
                $roomUser->setUser($user);
                $roomUser->setRoom($room);
                $roomUser->setRole(RoomRole::User);
                $manager->persist($roomUser);
                $roomUsers[spl_object_hash($room)][] = $user;
            }
        }



        // 5. Créer 2 rooms de groupe (isGroup=true), owner = userClassique, membres = amis + amis de ses amis (disjoints !)
        $groupRooms = [];

        // Trouve tous les membres à répartir
        $allMembers = $userFriends;
        foreach ($userFriends as $f) {
            foreach ($friendPairs as $pair) {
                if ($pair[0] === $f->getFriendCode()) $allMembers[] = self::findUserByFriendCode($users, $pair[1]);
                if ($pair[1] === $f->getFriendCode()) $allMembers[] = self::findUserByFriendCode($users, $pair[0]);
            }
        }
        $allMembers = array_filter(array_unique($allMembers, SORT_REGULAR));
        $membersToAssign = array_filter($allMembers, fn($m) => $m && $m !== $userClassique);
        $membersToAssign = array_unique($membersToAssign, SORT_REGULAR);
        shuffle($membersToAssign);
        $splitIndex = ceil(count($membersToAssign) / 2);
        $membersGroup1 = array_slice($membersToAssign, 0, $splitIndex);
        $membersGroup2 = array_slice($membersToAssign, $splitIndex);

        for ($i = 0; $i < 2; $i++) {
            $room = new Room();
            $room->setName('Groupe de ' . $userClassique->getPseudo() . ' #' . ($i + 1));
            $room->setIsGroup(true);
            $manager->persist($room);
            $groupRooms[] = $room;

            $roomUser = new RoomUser();
            $roomUser->setUser($userClassique);
            $roomUser->setRoom($room);
            $roomUser->setRole(RoomRole::Owner);
            $manager->persist($roomUser);
            $roomUsers[spl_object_hash($room)][] = $userClassique;

            $membersThisRoom = $i === 0 ? $membersGroup1 : $membersGroup2;
            foreach ($membersThisRoom as $member) {
                $roomUser = new RoomUser();
                $roomUser->setUser($member);
                $roomUser->setRoom($room);
                $roomUser->setRole(RoomRole::User);
                $manager->persist($roomUser);
                $roomUsers[spl_object_hash($room)][] = $member;
            }
        }

        // 6. Messages réalistes dans CHAQUE room (privée et de groupe)
        $allRooms = array_merge($privateRooms, $groupRooms);
        foreach ($allRooms as $room) {
            $participants = $roomUsers[spl_object_hash($room)] ?? [];
            if (empty($participants)) continue;

            $nbMessages = mt_rand(5, 12);
            $firstMsg = true;
            for ($j = 0; $j < $nbMessages; $j++) {
                $sender = $faker->randomElement($participants);

                $message = new Message();
                $message->setSender($sender);
                $message->setRoom($room);

                if ($room->isGroup()) {
                    $content = $faker->randomElement([
                        "Salut tout le monde !",
                        "On joue à quoi ce soir ?",
                        "Quelqu'un dispo pour un Morpion ?",
                        "J'ai trouvé un nouveau jeu à tester.",
                        "Qui veut créer un groupe privé ?",
                        "Vous allez bien ?",
                        "Hâte d'être ce week-end !"
                    ]);
                } else {
                    $content = $firstMsg
                        ? "Hey " . $sender->getPseudo() . " ! Ravi de discuter 😊"
                        : $faker->randomElement([
                            "Tu fais quoi ce soir ?",
                            "On se capte quand pour jouer ?",
                            "Trop cool la dernière partie !",
                            "À plus tard !"
                        ]);
                }
                $firstMsg = false;

                $message->setContent($content);
                $message->setType(MessageType::Text);
                $manager->persist($message);
            }
        }

        // Création de 2 GameRoom : une pour userClassique, une pour un autre user aléatoire

        // 1. GameRoom de userClassique
        $gameRoom1 = new GameRoom();
        $gameRoom1->setName('Gogo Morpion #1');
        $gameRoom1->setGame(Game::Morpion);
        $gameRoom1->setCreator($userClassique);
        $gameRoom1->setCreatedAt(new \DateTimeImmutable('-1 days'));
        $manager->persist($gameRoom1);

        // 2. GameRoom d’un autre user (pas userClassique)
        $otherUsers = array_filter($users, fn($u) => $u !== $userClassique);
        $randomUser = $faker->randomElement($otherUsers);

        $gameRoom2 = new GameRoom();
        $gameRoom2->setName('Tic Tac Toe #2');
        $gameRoom2->setGame(Game::Morpion);
        $gameRoom2->setCreator($randomUser);
        $gameRoom2->setCreatedAt(new \DateTimeImmutable('-2 hours'));
        $manager->persist($gameRoom2);


        $manager->flush();
    }

    private static function findUserByFriendCode(array $users, string $code): ?User
    {
        foreach ($users as $u) {
            if ($u->getFriendCode() === $code) return $u;
        }
        return null;
    }
}
