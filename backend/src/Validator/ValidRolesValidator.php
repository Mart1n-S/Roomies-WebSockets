<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

/**
 * Validateur pour la contrainte ValidRoles.
 *
 * Vérifie que chaque rôle d’un tableau correspond à un rôle autorisé.
 */
class ValidRolesValidator extends ConstraintValidator
{
    /**
     * Valide que la valeur soumise ne contient que des rôles autorisés.
     *
     * @param mixed $value        Valeur à valider (doit être un tableau de rôles)
     * @param Constraint $constraint Instance de ValidRoles
     */
    public function validate($value, Constraint $constraint)
    {
        // Vérifie que la contrainte est bien du bon type
        if (!$constraint instanceof ValidRoles) {
            throw new UnexpectedTypeException($constraint, ValidRoles::class);
        }

        // Ignore la validation si la valeur est vide (optionnel)
        if (null === $value || '' === $value) {
            return;
        }

        // Le champ doit être un tableau (sinon exception)
        if (!is_array($value)) {
            throw new UnexpectedValueException($value, 'array');
        }

        // Vérifie chaque rôle du tableau
        foreach ($value as $role) {
            if (!in_array($role, $constraint->validRoles, true)) {
                // Ajoute une violation avec un message personnalisé pour le rôle incorrect
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ role }}', $role)
                    ->setParameter('{{ validRoles }}', implode(', ', $constraint->validRoles))
                    ->addViolation();
            }
        }
    }
}
