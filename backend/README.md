# Roomies - Backend Symfony

## Prérequis

- PHP 8.1+
- Composer
- MySQL/MariaDB
- Node.js (pour le frontend)
- Docker (optionnel, via `compose.yaml`)

## Installation

1. **Cloner le dépôt**
2. **Installer les dépendances PHP**
   ```sh
   composer install
   ```
3. **Configurer les variables d'environnement**
   - Copier `.env` en `.env.local` et adapter les paramètres (BDD, mailer, etc.)

4. **Générer les clefs JWT**
   ```sh
   php bin/console lexik:jwt:generate-keypair
   ```

5. **Lancer les migrations**
   ```sh
   php bin/console doctrine:migrations:migrate
   ```

6. **Charger les fixtures (données de test)**
   ```sh
   php bin/console doctrine:fixtures:load
   ```

7. **Démarrer le serveur Symfony**
   ```sh
   symfony server:start
   ```

## Fonctionnalités principales

- **Authentification JWT** (LexikJWTAuthenticationBundle)
- **Inscription utilisateur** avec gestion d'avatar (upload via VichUploader)
- **Vérification d'email** (envoi de lien de confirmation, expiration)
- **Connexion / Déconnexion**
- **Refresh Token** (table `refresh_tokens`)
- **Réinitialisation de mot de passe** (envoi d'email avec token, expiration)
- **Modification du profil** (pseudo, mot de passe, avatar)
- **API RESTful** documentée (API Platform, OpenAPI/Swagger)
- **Rate Limiting** sur les endpoints sensibles (inscription, reset password)
- **Fixtures** pour générer des utilisateurs de test

## Structure des dossiers

- `src/ApiResource/` : Définition des ressources API Platform
- `src/Controller/` : Contrôleurs personnalisés (inscription, sécurité)
- `src/Dto/` : Objets de transfert de données (DTO)
- `src/Entity/` : Entités Doctrine (User, PasswordResetToken, etc.)
- `src/EventListener/` : Listeners pour les événements (inscription, login)
- `src/Repository/` : Repositories personnalisés
- `src/State/` : Processors et Providers API Platform
- `templates/` : Templates Twig pour les emails
- `migrations/` : Migrations Doctrine

## Points d'API importants

- `POST /api/user` : Inscription (multipart/form-data pour avatar)
- `POST /api/login` : Connexion
- `POST /api/token/refresh` : Rafraîchir le JWT
- `POST /api/request-password-reset` : Demander la réinitialisation du mot de passe
- `POST /api/reset-password` : Réinitialiser le mot de passe avec un token
- `POST /api/verify-email` : Vérifier l'email via le lien reçu
- `POST /api/request-new-confirmation-email` : Renvoyer un email de confirmation
- `GET /api/user` : Infos de l'utilisateur connecté
- `POST /api/user/edit` : Modifier pseudo, mot de passe ou avatar

## Emails envoyés

- **Confirmation d'inscription** : [templates/emails/confirmation_email.html.twig](templates/emails/confirmation_email.html.twig)
- **Réinitialisation de mot de passe** : [templates/emails/reset_password.html.twig](templates/emails/reset_password.html.twig)

## Notes

- Adapter la variable `frontend_url` dans la config pour que les liens envoyés par email pointent vers le bon frontend.
- Les tokens de réinitialisation et de confirmation expirent automatiquement.
- Les endpoints sensibles sont protégés contre le brute-force.

---
TODO:
- Ajouter des tests unitaires et fonctionnels
- Mettre en place la création d'une room (discussion privée) lorsqu'un utilisateur accepte une invitation => ok
- Mettre en place la création d'une room privée (ex: l'utilisateur supprime la discussion mais cela ne supprime pas réellement la room mais la invisible et lorsqu'il veut créer une discussion privée avec un utilisateur, il peut la retrouver) => uniquement coter front au moment de créer la room on vérifie si une room existe déjà avec l'utilisateur, si oui on la récupère, sinon on en crée une nouvelle.


- Mettre en place la suppression d'une room (discussion privée) -> ne pas faire de suppression réelle mais juste la rendre invisible pour l'utilisateur ducoup coter front voir pour rajouter un champ `isVisible` ou `isDeleted` pour les rooms
- Mettre en place la suppression d'un utilisateur (et donc de toutes ses rooms etc)
  
- Mettre en place la création de groupes (discussions de groupe) => OK
- Mettre en place le get d'une room en particulier
- Mettre en place le get de toutes les rooms aux quelles l'utilisateur appartient
- Mettre en place la route pour ajouter un utilisateur à une room => ok
- Mettre en place la route pour supprimer un utilisateur d'une room
- Mettre en place la route pour modifier le nom d'une room
- Mettre en place la route pour modifier le role d'un utilisateur dans une room => ok
- Mettre en place la route pour supprimer une room (goupe) pour le owner
- Mettre en place la route pour quitter une room (groupe) pour un utilisateur

















 1. Création d’un groupe
Qui : tout utilisateur

Action : crée une room avec isGroup = true, devient automatiquement owner, ajoute des amis (ou tout utilisateur selon ta future règle)

Route : POST /groups

Payload :

json
Copier
Modifier
{
  "name": "Nom du groupe",
  "members": ["uuid1", "uuid2", "..."] // optionnel
}
🧑‍💼 2. Ajout d’un membre
Qui :

Owner : peut ajouter n’importe qui

Admin : peut ajouter uniquement des amis ? (à définir)

User : peut ajouter (limité ou non selon les règles)

Route : POST /groups/{id}/members

Payload :

json
Copier
Modifier
{
  "userId": "uuid"
}
🎖️ 3. Changement de rôle
Qui : Owner uniquement

Action : promouvoir ou rétrograder un membre (Admin ↔ User)

Route : PATCH /groups/{id}/members/{userId}/role

Payload :

json
Copier
Modifier
{
  "role": "admin"
}
🧹 4. Suppression d’un membre
Qui :

Owner : peut supprimer n’importe qui sauf lui-même

Admin : peut supprimer seulement un user

Route : DELETE /groups/{id}/members/{userId}

🚪 5. Quitter un groupe
Qui : tout membre sauf le Owner

Route : POST /groups/{id}/leave

❌ 6. Supprimer un groupe
Qui : uniquement le Owner

Route : DELETE /groups/{id}

🗑️ 7. Effacer les messages d’un utilisateur supprimé
Automatique à la suppression ou au départ du membre : logique dans le RoomUserRepository ou via onDelete: CASCADE + suppression manuelle des messages.

🔒 À inclure :
Vérification des rôles dans chaque action

Système d’autorisation basé sur le rôle dans la room

Gestion des erreurs propres (ex: tentative de quitter le groupe par le owner)

Souhaites-tu que je t’aide à créer l’entité GroupResource pour gérer cette API ou bien tu préfères qu’on commence par une ou plusieurs routes précises (comme la création du groupe) ?






Vous avez dit :
quelques rectifications. Pour "2. Ajout d’un membre" tout le monde peut ajouter n’importe qui, et pour 5. Quitter un groupe , le owner peut quitter le groupe mais cela aura pour consequences de supprimer le groupe aussi. Sinon le reste est bien il me semble, commencons par créer POST /groups et le api resource, en effets la payload contiendra le nom du groupe et un tableau d'utilisateurs qui sera composé des friendCode





V2 RECAP


3️⃣ Attribution de rôle (promotion/dégradation)
Route : PATCH /groups/{id}/members/{memberId}/role

Payload : role (admin, user)

Règles :

Seul le Owner peut attribuer ou retirer le rôle Admin.

Impossible de modifier son propre rôle.

4️⃣ Exclusion d’un membre
Route : DELETE /groups/{id}/members/{memberId}

Règles :

Owner peut exclure n’importe qui.

Admin ne peut exclure que des User.

Quand un membre est retiré, tous ses messages sont supprimés.

5️⃣ Quitter un groupe
Route : POST /groups/{id}/leave

Règles :

Tout membre peut quitter un groupe.

Le Owner s’il quitte supprime directement le groupe en entier.

6️⃣ Suppression complète d’un groupe
Route : DELETE /groups/{id}

Règles :

Seul le Owner peut supprimer un groupe manuellement.

7️⃣ Visualisation d’un groupe
Route : GET /groups/{id}

Règles :

Retourne le GroupReadDTO complet.

8️⃣ Visualisation de mes groupes
Route : GET /groups

Règles :

Retourne la liste des groupes dont l’utilisateur est membre.