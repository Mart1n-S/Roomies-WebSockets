<?php

namespace App\Enum;

/**
 * Cette énumération définit les différents statuts possibles pour une demande d'ami.
 * Pending : La demande d'ami a été envoyée mais pas encore acceptée.
 * Friend : La demande d'ami a été acceptée.
 * 
 * Si la demande est refusée, elle est supprimée de la base de données.
 */
enum FriendshipStatus: string
{
    case Pending = 'pending';
    case Friend = 'friend';
}
