<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * Contrainte de validation pour garantir l’unicité d’une propriété sur une entité donnée.
 *
 * À utiliser sur une propriété d'entité pour vérifier que sa valeur est unique en base.
 * Nécessite un validateur dédié (voir UniqueFieldValidator).
 *
 * Exemple d’utilisation :
 * #[UniqueField(entityClass: User::class, field: 'email')]
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class UniqueField extends Constraint
{
    /** @var string Classe de l'entité concernée */
    public string $entityClass;

    /** @var string Nom du champ à vérifier pour l'unicité */
    public string $field;

    /** @var string Message d’erreur personnalisé si la valeur existe déjà */
    public string $message = 'Cette valeur est déjà utilisée.';

    /**
     * Constructeur avec options personnalisées (attribut ou annotation).
     *
     * @param array $options          Options Symfony (optionnel)
     * @param string|null $entityClass Classe cible (obligatoire)
     * @param string|null $field       Champ cible (obligatoire)
     * @param string|null $message     Message d’erreur (optionnel)
     * @param mixed $groups           Groupes de validation (optionnel)
     * @param mixed $payload          Payload Symfony (optionnel)
     */
    public function __construct(
        array $options = [],
        ?string $entityClass = null,
        ?string $field = null,
        ?string $message = null,
        mixed $groups = null,
        mixed $payload = null
    ) {
        parent::__construct($options, $groups, $payload);

        // Récupère les paramètres depuis les arguments ou les options du tableau
        $this->entityClass = $entityClass ?? $options['entityClass'] ?? throw new \InvalidArgumentException('Missing option "entityClass"');
        $this->field = $field ?? $options['field'] ?? throw new \InvalidArgumentException('Missing option "field"');
        $this->message = $message ?? $options['message'] ?? $this->message;
    }

    /**
     * Indique que cette contrainte s’applique sur une propriété (et non la classe entière).
     *
     * @return string
     */
    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
