<?php

namespace App\Validator;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validateur associé à la contrainte UniqueField.
 *
 * Vérifie en base qu’aucune entité n’existe déjà avec la même valeur pour le champ ciblé.
 */
class UniqueFieldValidator extends ConstraintValidator
{
    private EntityManagerInterface $em;

    /**
     * Injection de l’EntityManager pour accéder à la base de données.
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Vérifie que la valeur du champ est bien unique pour l’entité/colonne cible.
     *
     * @param mixed $value           Valeur à valider
     * @param Constraint $constraint Instance de UniqueField
     * @return void
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        // Vérifie que la contrainte reçue est bien du bon type
        if (!$constraint instanceof UniqueField) {
            throw new UnexpectedTypeException($constraint, UniqueField::class);
        }

        // Ignore la validation si le champ est vide (laissez d’autres contraintes gérer ça)
        if (null === $value || '' === $value) {
            return;
        }

        // Recherche d’un enregistrement existant en base avec cette valeur
        $repository = $this->em->getRepository($constraint->entityClass);

        $existing = $repository->findOneBy([
            $constraint->field => $value
        ]);

        // Si un résultat existe, déclenche une violation (message d’erreur)
        if ($existing !== null) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
