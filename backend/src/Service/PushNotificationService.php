<?php

namespace App\Service;

use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

/**
 * Service d'envoi de notifications push web via VAPID (Web Push Protocol).
 *
 * Utilise la librairie Minishlink/WebPush pour envoyer des notifications aux abonnés.
 */
class PushNotificationService
{
    private $webPush;

    /**
     * Initialise le service avec les clés VAPID et le sujet.
     *
     * @param string $vapidPublicKey  La clé publique VAPID
     * @param string $vapidPrivateKey La clé privée VAPID
     * @param string $vapidSubject    Le sujet VAPID (ex: mailto:admin@roomies.com)
     */
    public function __construct(string $vapidPublicKey, string $vapidPrivateKey, string $vapidSubject)
    {
        $this->webPush = new WebPush([
            'VAPID' => [
                'subject'    => $vapidSubject,
                'publicKey'  => $vapidPublicKey,
                'privateKey' => $vapidPrivateKey,
            ],
        ]);
    }

    /**
     * Enfile et envoie une notification push à un abonné spécifique.
     *
     * @param string $endpoint  L'URL d'abonnement push (PushSubscription.endpoint)
     * @param string $p256dh    La clé publique du client (PushSubscription.keys.p256dh)
     * @param string $auth      Le token d'authentification du client (PushSubscription.keys.auth)
     * @param array  $payload   Les données à envoyer à afficher dans la notif (title, body, icon, etc.)
     */
    public function sendNotification(string $endpoint, string $p256dh, string $auth, array $payload): void
    {
        $subscription = Subscription::create([
            'endpoint'        => $endpoint,
            'publicKey'       => $p256dh,
            'authToken'       => $auth,
            'contentEncoding' => 'aesgcm', // ou 'aes128gcm' selon le navigateur du client
        ]);

        // Ajoute la notif à la file d'envoi
        $this->webPush->queueNotification(
            $subscription,
            json_encode($payload)
        );

        // Envoie les notifications de la file (flush synchrone ici)
        foreach ($this->webPush->flush() as $report) {
        }
    }
}
