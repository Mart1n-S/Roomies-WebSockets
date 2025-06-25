<?php

namespace App\Dto\Group;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class GroupCreateDTO
{
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 3,
        max: 30,
        minMessage: 'Le nom du groupe doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'Le nom du groupe ne peut pas dépasser {{ limit }} caractères.'
    )]
    #[Assert\Regex(
        pattern: '/^[A-Za-zÀ-ÿ0-9 ]+$/u',
        message: 'Le nom du groupe ne peut contenir que des lettres, des chiffres et des espaces.'
    )]
    #[Groups(['create:group'])]
    public string $name;

    /**
     * @var string[] Liste de codes amis (friendCodes)
     */
    #[Assert\Type('array')]
    #[Assert\Count(
        min: 2,
        minMessage: 'Un groupe doit contenir au moins 2 membres en plus de vous-même.'
    )]
    #[Assert\All([
        new Assert\NotBlank(
            message: 'Le code ami ne peut pas être vide.'
        ),
        new Assert\Length(
            exactly: 20,
            exactMessage: 'Le code ami doit contenir exactement 20 caractères.'
        ),
        new Assert\Regex(
            pattern: '/^[A-F0-9]{20}$/',
            message: 'Le code ami doit contenir uniquement des lettres majuscules (A-F) et des chiffres (0-9), sur 20 caractères.'
        ),
    ])]
    #[Groups(['create:group'])]
    public array $members = [];
}
