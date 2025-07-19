<?php

namespace App\Tests;

use App\Dto\GameRoom\CreateGameRoomDTO;
use App\Enum\Game;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Teste la validation du DTO CreateGameRoomDTO (nom de room et jeu).
 */
class CreateGameRoomDTOTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->validator = static::getContainer()->get(ValidatorInterface::class);
    }

    /**
     * Teste la validation échoue avec un nom vide et un jeu manquant.
     */
    public function testInvalidData()
    {
        $dto = new CreateGameRoomDTO();
        // Rien n'est initialisé (name vide, game absent)

        $violations = $this->validator->validate($dto);

        $this->assertGreaterThanOrEqual(2, count($violations));
        $messages = [];
        foreach ($violations as $violation) {
            $messages[] = $violation->getPropertyPath() . ': ' . $violation->getMessage();
        }

        $this->assertStringContainsString('Le nom est obligatoire.', implode("\n", $messages));
        $this->assertStringContainsString('Le jeu est obligatoire.', implode("\n", $messages));
    }

    /**
     * Teste la validation échoue avec un nom invalide (trop court, mauvais format).
     */
    public function testInvalidName()
    {
        $dto = new CreateGameRoomDTO();
        $dto->name = '@';
        $dto->game = Game::Morpion;

        $violations = $this->validator->validate($dto);

        $this->assertGreaterThanOrEqual(1, count($violations));
        $messages = [];
        foreach ($violations as $violation) {
            $messages[] = $violation->getPropertyPath() . ': ' . $violation->getMessage();
        }

        $this->assertStringContainsString('Le nom doit comporter entre 2 et 20 caractères', implode("\n", $messages));
    }

    /**
     * Teste la validation réussit avec des données valides.
     */
    public function testValidData()
    {
        $dto = new CreateGameRoomDTO();
        $dto->name = 'Room_01';
        $dto->game = Game::Morpion;

        $violations = $this->validator->validate($dto);
        $this->assertCount(0, $violations, "DTO valide ne doit générer aucune erreur");
    }
}
