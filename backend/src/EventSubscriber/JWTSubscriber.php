<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Subscriber qui enrichit le payload du JWT lors de sa création.
 *
 * Ce subscriber ajoute des informations personnalisées (id, pseudo)
 * dans le token JWT lors de l'authentification de l'utilisateur.
 */
class JWTSubscriber implements EventSubscriberInterface
{
    /**
     * Événement déclenché lors de la création du JWT.
     *
     * Permet d'ajouter des données personnalisées dans le payload du token.
     *
     * @param JWTCreatedEvent $event
     * @return void
     */
    public function onLexikJwtAuthenticationOnJwtCreated(JWTCreatedEvent $event): void
    {
        // Récupère le payload et l'utilisateur courant
        $payload = $event->getData();
        $user = $event->getUser();

        // Si l'utilisateur est bien une instance de User
        // Ajoute l'id et le pseudo au payload du JWT
        if ($user instanceof User) {
            $payload['id'] = $user->getId();
            $payload['pseudo'] = $user->getPseudo();
        }

        // Met à jour le payload du token avec les nouvelles données
        $event->setData($payload);
    }

    /**
     * Déclare l'événement écouté par ce subscriber.
     *
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        // Souscrit à l'événement de création du JWT de LexikJWTAuthenticationBundle
        return [
            'lexik_jwt_authentication.on_jwt_created' => 'onLexikJwtAuthenticationOnJwtCreated',
        ];
    }
}
