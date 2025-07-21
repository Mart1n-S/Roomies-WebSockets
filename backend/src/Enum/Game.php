<?php

namespace App\Enum;

/**
 * Les types de jeux disponibles dans l'application.
 */
enum Game: string
{
    case Morpion = 'morpion';
    case Puissance4 = 'puissance4';
}
