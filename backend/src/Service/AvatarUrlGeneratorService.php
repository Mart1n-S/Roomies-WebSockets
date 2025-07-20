<?php

namespace App\Service;

use App\Entity\User;

/**
 * Service pour générer l'URL publique de l'avatar d'un utilisateur.
 *
 * Utilisé pour fournir au frontend le chemin complet (URL) vers l'avatar,
 * en tenant compte du cas où l'utilisateur n'a pas d'image (avatar par défaut).
 */
class AvatarUrlGeneratorService
{
    public function __construct(
        private readonly string $avatarPublicPath, // Chemin public
        private readonly string $baseUrl           // Base URL de l'application 
    ) {}

    /**
     * Génère l'URL complète de l'avatar d'un utilisateur.
     *
     * @param User|null $user L'utilisateur (ou null)
     * @return string         URL absolue de l'avatar
     */
    public function generate(?User $user): string
    {
        // Utilise l'image personnalisée, sinon l'avatar par défaut
        $avatarFile = $user?->getImageName() ?: 'default-avatar.svg';
        // Concatène l'URL de base, le chemin et le nom de fichier proprement
        return rtrim($this->baseUrl . $this->avatarPublicPath, '/') . '/' . $avatarFile;
    }
}
