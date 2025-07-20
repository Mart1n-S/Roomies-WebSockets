<?php

namespace App\Security\Exception;

/**
 * Exception personnalisée pour la gestion du rate limiting (trop de requêtes).
 *
 * Permet de signaler à l'utilisateur qu'il a dépassé la limite de tentatives autorisées
 * et de fournir un message d'attente dynamique avant un nouvel essai.
 */
class TooManyRequestsException  extends \Exception
{
    /** @var int Nombre de minutes à attendre avant une nouvelle tentative */
    private int $retryAfterMinutes;
    /** @var string Message personnalisé affiché à l'utilisateur */
    private string $customMessage;

    /**
     * Constructeur de l'exception.
     *
     * @param int $retryAfterTimestamp Timestamp unix de la prochaine autorisation de tentative
     * @param string $customMessage    Message d'erreur personnalisable (optionnel)
     */
    public function __construct(int $retryAfterTimestamp, string $customMessage = 'Trop de tentatives. Veuillez réessayer dans :')
    {
        // Calcule le nombre de minutes restantes à partir du timestamp fourni
        $this->retryAfterMinutes = ceil(($retryAfterTimestamp - time()) / 60);
        $this->customMessage = $customMessage;

        // Appelle le constructeur parent avec le message personnalisé
        parent::__construct($customMessage);
    }

    /**
     * Retourne le message d'erreur avec le temps d'attente intégré.
     *
     * @return string
     */
    public function getMessageKey(): string
    {
        return "{$this->customMessage} {$this->retryAfterMinutes} minutes.";
    }

    /**
     * Retourne le nombre de minutes à attendre avant un nouvel essai.
     *
     * @return int
     */
    public function getRetryAfterMinutes(): int
    {
        return $this->retryAfterMinutes;
    }
}
