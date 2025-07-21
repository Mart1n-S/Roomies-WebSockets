<?php

namespace App\Dto\User;

/**
 * DtO pour les abonnements de push notifications des utilisateurs.
 */
class PushSubscriptionDTO
{
    /**
     * L'endpoint de la subscription.
     */
    public ?string $endpoint = null;

    /**
     * La clé d'authentification pour la subscription.
     */
    public ?string $auth = null;

    /**
     * La clé publique de la subscription.
     */
    public ?string $p256dh = null;

    /**
     * Indique si les notifications push sont activées.
     */
    public ?bool $pushNotificationsEnabled = null;
}
