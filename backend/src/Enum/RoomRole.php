<?php

namespace App\Enum;

/**
 * Enumération des rôles possibles dans une room.
 *
 * - Owner : Le propriétaire de la room, a tous les droits.
 * - Admin : L'administrateur mis en place par le propriétaire, a des droits étendus.
 * - User : Un utilisateur classique avec des droits de base.
 */
enum RoomRole: string
{
    case Owner = 'owner';
    case Admin = 'admin';
    case User = 'user';
}
