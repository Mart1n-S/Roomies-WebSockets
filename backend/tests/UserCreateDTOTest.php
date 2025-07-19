<?php

namespace App\Tests;

use App\Dto\User\UserCreateDTO;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Test unitaire sur les contraintes de validation du DTO UserCreateDTO.
 *
 * On vérifie ici que les règles de validation des champs (email, password, pseudo) 
 * fonctionnent bien et remontent les erreurs attendues.
 */
class UserCreateDTOTest extends KernelTestCase
{
    /**
     * Retourne le validator Symfony configuré.
     */
    private function getValidator(): ValidatorInterface
    {
        // Démarre le kernel pour accéder au container
        self::bootKernel();
        return self::getContainer()->get(ValidatorInterface::class);
    }

    /**
     * Teste la validation du DTO avec des données invalides.
     * 
     * On s’assure que :
     * - Chaque champ en erreur génère bien une violation,
     * - Les messages d’erreur correspondent à ceux définis dans le DTO.
     */
    public function testInvalidUserCreateDTO()
    {
        $validator = $this->getValidator();

        // Création d'un DTO avec des valeurs invalides
        $dto = new UserCreateDTO();
        $dto->email = 'pas-un-email';
        $dto->password = 'short';
        $dto->pseudo = 'X';

        $violations = $validator->validate($dto);

        // On s'attend à avoir des violations pour chaque champ
        $fields = [];
        foreach ($violations as $violation) {
            $fields[$violation->getPropertyPath()][] = $violation->getMessage();
        }

        // Vérifie que chaque champ problématique a généré au moins une erreur
        $this->assertArrayHasKey('email', $fields);
        $this->assertArrayHasKey('password', $fields);
        $this->assertArrayHasKey('pseudo', $fields);

        // Vérifie les messages d'erreur principaux
        $this->assertContains("L'adresse email n'est pas valide.", $fields['email']);
        $this->assertContains('Le mot de passe doit comporter au moins 16 caractères.', $fields['password']);
        $this->assertContains(
            'Le pseudo doit comporter entre 2 et 20 caractères et ne peut contenir que des lettres, des chiffres et des underscores (_).',
            $fields['pseudo']
        );
    }

    /**
     * Teste la validation avec des données valides.
     *
     * On vérifie que le DTO ne produit aucune violation dans ce cas.
     */
    public function testValidUserCreateDTO()
    {
        $validator = $this->getValidator();

        $dto = new UserCreateDTO();
        $dto->email = 'user' . uniqid() . '@example.com';
        $dto->password = 'UnSuperMot2Passe!TrèsLong';
        $dto->pseudo = 'PseudoCool';

        $violations = $validator->validate($dto);

        // Il ne doit pas y avoir d'erreur de validation
        $this->assertCount(0, $violations, 'Aucune violation attendue avec des données valides.');
    }
}
