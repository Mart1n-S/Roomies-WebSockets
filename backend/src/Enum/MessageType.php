<?php

namespace App\Enum;

/**
 * Les types de messages possibles dans le système de messagerie.
 */
enum MessageType: string
{
    case Text = 'text';
    case File = 'file';
    case Image = 'image';
}
