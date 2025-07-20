<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * Contrainte de validation pour vérifier qu’un ou plusieurs rôles sont valides.
 *
 * À utiliser sur une propriété ou une méthode pour s’assurer
 * que la valeur soumise correspond bien à un rôle autorisé.
 *
 * Exemple :
 * #[ValidRoles(validRoles: ['ROLE_USER', 'ROLE_ADMIN', 'ROLE_SUPER_ADMIN'])]
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class ValidRoles extends Constraint
{
    /** @var string Message d’erreur personnalisable */
    public string $message = 'Le rôle "{{ role }}" n\'est pas valide. Les rôles valides sont : {{ validRoles }}';

    /** @var array Liste des rôles valides acceptés par la contrainte */
    public array $validRoles = ['ROLE_USER', 'ROLE_ADMIN'];

    /**
     * Constructeur pour configurer la contrainte.
     *
     * @param array|null $validRoles Liste des rôles valides (par défaut : ROLE_USER, ROLE_ADMIN)
     * @param string|null $message   Message d’erreur personnalisé
     * @param array|null $groups     Groupes de validation Symfony (optionnel)
     * @param mixed $payload         Payload Symfony (optionnel)
     */
    public function __construct(
        ?array $validRoles = null,
        ?string $message = null,
        ?array $groups = null,
        $payload = null
    ) {
        parent::__construct([], $groups, $payload);

        // Utilise la valeur fournie ou le tableau par défaut
        $this->validRoles = $validRoles ?? $this->validRoles;
        $this->message = $message ?? $this->message;
    }
}
