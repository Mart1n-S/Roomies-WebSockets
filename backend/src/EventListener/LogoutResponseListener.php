<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ResponseEvent;

/**
 * Écouteur d'événement pour la déconnexion utilisateur.
 *
 * Ce listener est déclenché sur chaque réponse HTTP.
 * Il permet, lors d'un appel à l'endpoint `/api/logout`, de supprimer
 * les cookies d'authentification (`refresh_token` et `BEARER`).
 * Cela garantit que l'utilisateur est bien déconnecté côté client.
 */
class LogoutResponseListener
{
    /**
     * Méthode appelée lors de chaque événement de réponse HTTP.
     *
     * @param ResponseEvent $event
     * @return void
     */
    public function onKernelResponse(ResponseEvent $event): void
    {
        // Récupère la requête et la réponse courante
        $request = $event->getRequest();
        $response = $event->getResponse();

        // Vérifie si l'URL correspond à la route de logout de l'API
        if ($request->getPathInfo() === '/api/logout') {
            // Supprime le cookie 'refresh_token' (token de refresh JWT)
            // et le cookie 'BEARER' (access token), avec les attributs sécurisés
            $response->headers->clearCookie('refresh_token', '/', null, true, true, 'None');
            $response->headers->clearCookie('BEARER', '/', null, true, true, 'None');
        }
    }
}
