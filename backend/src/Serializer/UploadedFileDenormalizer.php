<?php

namespace App\Serializer;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * Denormalizer personnalisé pour les fichiers uploadés.
 *
 * Permet à l'API de gérer correctement la désérialisation des fichiers reçus (ex : avatars),
 * en les laissant inchangés si ce sont déjà des objets File.
 */
final class UploadedFileDenormalizer implements DenormalizerInterface
{
    /**
     * "Désérialise" la donnée si besoin. Ici, retourne l'objet File tel quel.
     *
     * @param mixed  $data   Donnée reçue à désérialiser
     * @param string $type   Type attendu
     * @param string|null $format
     * @param array  $context
     * @return File
     */
    public function denormalize($data, string $type, ?string $format = null, array $context = []): File
    {
        // Aucun traitement : on retourne simplement l'objet File
        return $data;
    }

    /**
     * Vérifie si ce denormalizer doit être utilisé pour ce type de donnée.
     *
     * @param mixed  $data
     * @param string $type
     * @param string|null $format
     * @param array  $context
     * @return bool
     */
    public function supportsDenormalization($data, $type, $format = null, array $context = []): bool
    {
        // Ce denormalizer ne s'applique que si la donnée est déjà un objet File
        return $data instanceof File;
    }

    /**
     * Retourne la liste des types supportés par ce denormalizer.
     *
     * @return array
     */
    public function getSupportedTypes(): array
    {
        // File::class = support strict (true)
        return [
            File::class => true,
        ];
    }
}
