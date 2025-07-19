<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use App\Entity\User;
use App\Mapper\UserMapper;
use App\Service\AvatarUrlGeneratorService;

class UserMapperTest extends TestCase
{
    /**
     * Teste que UserMapper::toReadDto() convertit correctement un User en UserReadDTO,
     * avec tous les champs principaux bien transmis et l’URL d’avatar générée à partir de l’image de l’utilisateur.
     * 
     * Ce test valide le fonctionnement de la méthode de mapping sur un cas “normal”.
     */
    public function testToReadDto()
    {
        // Création d'un User factice
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setPseudo('Testeur');
        $user->setRoles(['ROLE_USER']);
        $user->setFriendCode('47A350833EA98050C4E9');
        $user->setImageName('photo.jpg');

        // Service avatar (pas besoin de mock ici car il est très simple)
        $avatarService = new AvatarUrlGeneratorService('/media/', 'https://localhost');
        $mapper = new UserMapper($avatarService);

        // Act : on mappe
        $dto = $mapper->toReadDto($user);

        // Assert : on vérifie les valeurs attendues
        $this->assertEquals('test@example.com', $dto->email);
        $this->assertEquals('Testeur', $dto->pseudo);
        $this->assertEquals(['ROLE_USER'], $dto->roles);
        $this->assertEquals('47A350833EA98050C4E9', $dto->friendCode);
        $this->assertEquals('https://localhost/media/photo.jpg', $dto->avatar);
    }

    /**
     * Teste le cas où l'utilisateur n'a pas d'image personnalisée.
     * 
     * Le mapper doit alors générer une URL d’avatar qui pointe sur l’avatar par défaut.
     * Ce test vérifie le “fallback” prévu pour tous les utilisateurs sans image.
     */
    public function testToReadDtoWithDefaultAvatar()
    {
        $user = new User();
        // Pas d'image définie
        $user->setEmail('noimg@example.com');
        $user->setPseudo('NoImg');
        $user->setRoles(['ROLE_USER']);
        $user->setFriendCode('47A350833EA98050C4E9');
        $user->setImageName(null);

        $avatarService = new AvatarUrlGeneratorService('/media/', 'https://localhost');
        $mapper = new UserMapper($avatarService);

        $dto = $mapper->toReadDto($user);

        $this->assertEquals('https://localhost/media/default-avatar.svg', $dto->avatar);
    }

    /**
     * Teste que UserMapper::toReadDto() gère le cas d'un User sans email (ou email vide).
     * 
     * Selon ta logique, ici on vérifie que l'email est null dans le DTO.
     */
    public function testToReadDtoWithNoEmail()
    {
        $user = new User();
        // Pas d'email défini
        $user->setPseudo('Anon');
        $user->setRoles(['ROLE_USER']);
        $user->setFriendCode('47A350833EA98050C4E9');
        $user->setImageName('avatar.png');

        $avatarService = new AvatarUrlGeneratorService('/media/', 'https://localhost');
        $mapper = new UserMapper($avatarService);

        $dto = $mapper->toReadDto($user);

        $this->assertNull($dto->email, "L'email doit être null pour un utilisateur sans email");
        $this->assertEquals('Anon', $dto->pseudo);
        $this->assertEquals('47A350833EA98050C4E9', $dto->friendCode);
        $this->assertEquals('https://localhost/media/avatar.png', $dto->avatar);
    }

    /**
     * Teste le comportement de UserMapper::toReadDto quand AvatarUrlGeneratorService rencontre une erreur.
     * Permet de vérifier la robustesse du Mapper en cas d’exception côté service.
     */
    public function testToReadDtoThrowsIfAvatarServiceFails()
    {
        // Création d'un User fictif
        $user = new User();
        $user->setEmail('fail@example.com');
        $user->setPseudo('PanicUser');
        $user->setRoles(['ROLE_USER']);
        $user->setFriendCode('47A350833EA98050C4E9');

        // Mock du service pour forcer une exception
        $avatarServiceMock = $this->createMock(AvatarUrlGeneratorService::class);
        $avatarServiceMock
            ->method('generate')
            ->will($this->throwException(new \RuntimeException('Erreur de génération d’avatar')));

        $mapper = new UserMapper($avatarServiceMock);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Erreur de génération d’avatar');

        // L’appel doit lever une exception
        $mapper->toReadDto($user);
    }
}
