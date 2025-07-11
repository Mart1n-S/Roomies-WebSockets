<?php

// namespace App\DataFixtures;

// use App\Entity\User;
// use Faker\Factory as Faker;
// use Doctrine\Persistence\ObjectManager;
// use Doctrine\Bundle\FixturesBundle\Fixture;
// use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

// class AppFixtures extends Fixture
// {
//     public function __construct(private UserPasswordHasherInterface $passwordHasher) {}

//     public function load(ObjectManager $manager): void
//     {

//         // Créer une instance de Faker pour générer des données aléatoires
//         $faker = Faker::create();

//         // Créer 10 utilisateurs
//         for ($i = 0; $i < 10; $i++) {
//             $user = new User();
//             $user->setPseudo($faker->firstName);
//             $user->setIsVerified(true);
//             $user->setEmail($faker->unique()->safeEmail);
//             $user->setFriendCode(strtoupper(bin2hex(random_bytes(10))));
//             $user->setPassword($this->passwordHasher->hashPassword(
//                 $user,
//                 'password'
//             ));
//             $manager->persist($user);
//         }

//         // Création d'un utilisateur classique
//         $userClassique = new User();
//         $userClassique->setPseudo('userClassique');
//         $userClassique->setIsVerified(true);
//         $userClassique->setEmail('user@user.com');
//         $userClassique->setFriendCode(strtoupper(bin2hex(random_bytes(10))));
//         $userClassique->setPassword($this->passwordHasher->hashPassword(
//             $userClassique,
//             'password'
//         ));
//         $manager->persist($userClassique);


//         $manager->flush();
//     }
// }


namespace App\DataFixtures;

use App\Entity\User;
use App\Enum\RoomRole;


use App\Enum\MessageType;
use App\Enum\FriendshipStatus;

use App\Entity\Friendship;
use App\Entity\Message;
use App\Entity\Room;
use App\Entity\RoomUser;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as Faker;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher) {}

    public function load(ObjectManager $manager): void
    {
        $faker = Faker::create();
        $users = [];

        // 1. Créer 10 utilisateurs
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setPseudo($faker->userName);
            $user->setIsVerified(true);
            $user->setEmail($faker->unique()->safeEmail);
            $user->setFriendCode(strtoupper(bin2hex(random_bytes(10))));
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));

            $manager->persist($user);
            $users[] = $user;
        }

        // Création d'un utilisateur classique
        $userClassique = new User();
        $userClassique->setPseudo('userClassique');
        $userClassique->setIsVerified(true);
        $userClassique->setEmail('user@user.com');
        $userClassique->setFriendCode(strtoupper(bin2hex(random_bytes(10))));
        $userClassique->setPassword($this->passwordHasher->hashPassword(
            $userClassique,
            'password'
        ));
        $manager->persist($userClassique);
        $users[] = $userClassique;

        // 2. Créer 0 à 3 amis par utilisateur
        foreach ($users as $user) {
            $friendCount = rand(0, 3);
            $friendsAdded = [];

            while (count($friendsAdded) < $friendCount) {
                $friend = $users[array_rand($users)];
                if ($friend !== $user && !in_array($friend, $friendsAdded, true)) {
                    $friendship = new Friendship();
                    $friendship->setApplicant($user);
                    $friendship->setRecipient($friend);
                    $friendship->setStatus(FriendshipStatus::Friend);

                    $manager->persist($friendship);
                    $friendsAdded[] = $friend;
                }
            }
        }

        // 3. Créer 0 à 2 rooms par utilisateur
        $rooms = [];
        foreach ($users as $user) {
            $roomCount = rand(0, 2);
            for ($i = 0; $i < $roomCount; $i++) {
                $room = new Room();
                $room->setName($faker->word() . '-' . bin2hex(random_bytes(2)));
                $room->setIsGroup(true);
                $manager->persist($room);

                $rooms[] = [$room, $user]; // (Room, Owner)
            }
        }

        // 4. Créer 0 à 10 messages par utilisateur
        foreach ($rooms as [$room, $owner]) {
            $messageCount = rand(0, 10);
            for ($i = 0; $i < $messageCount; $i++) {
                $sender = $users[array_rand($users)];

                $message = new Message();
                $message->setSender($sender);
                $message->setRoom($room);
                $message->setContent($faker->sentence());
                $message->setType(MessageType::Text);

                $manager->persist($message);
            }
        }



        // 5. Associer des membres aux rooms via RoomUser (owner + membres aléatoires)
        foreach ($rooms as [$room, $owner]) {
            $participants = [$owner];
            $roomUser = new RoomUser();
            $roomUser->setUser($owner);
            $roomUser->setRoom($room);
            $roomUser->setRole(RoomRole::Owner);
            $manager->persist($roomUser);

            // Ajouter entre 1 et 5 membres
            $memberCount = rand(1, 5);
            while (count($participants) < $memberCount + 1) {
                $member = $users[array_rand($users)];
                if (!in_array($member, $participants, true)) {
                    $roomUser = new RoomUser();
                    $roomUser->setUser($member);
                    $roomUser->setRoom($room);
                    $roomUser->setRole(RoomRole::User);
                    $manager->persist($roomUser);
                    $participants[] = $member;
                }
            }
        }

        $manager->flush();
    }
}
