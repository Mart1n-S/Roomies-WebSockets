# Roomies 🕹️

**Roomies** est une application web de jeux en ligne et de chat en temps réel, pensée pour créer des moments conviviaux entre amis ou avec de nouveaux joueurs !

## Principe du projet 🫣

Roomies permet de jouer à des mini-jeux contre d’autres personnes, tout en discutant en temps réel grâce à une messagerie instantanée basée sur les WebSockets.

L’application propose :
- Un **chat global** ouvert à tous les utilisateurs, pour discuter librement et rencontrer d’autres joueurs.
- Un système d’**ajout d’amis** : invite, recherche et accepte tes amis grâce à un code unique.
- Des **discussions privées** avec tes amis : chaque amitié crée automatiquement un salon privé où échanger en toute simplicité.
- La possibilité de **créer des groupes de discussion** : rassemble plusieurs amis dans un salon privé de groupe pour discuter ensemble.
- Un **mini-jeu intégré : le Morpion** (Tic-Tac-Toe), jouable à deux.  
  Les parties sont organisées via un **lobby** où tu peux :
    - Créer un salon de jeu,
    - Filtrer les salons (trouver une partie libre, voir des parties en cours, etc.),
    - Rejoindre une partie disponible,
    - Regarder une partie en tant que spectateur.
- Une expérience 100% temps réel : toutes les interactions (chat, jeux, gestion des groupes…) sont synchronisées en direct grâce aux WebSockets.

Roomies a été pensé comme un “Discord du jeu en ligne”, pour favoriser le jeu social, les échanges spontanés, et le fun !

---

## Stack technique 🛠️

Roomies repose sur une architecture moderne fullstack :

- **Backend** :  
   **Symfony 6.4 (PHP)**  
  → API RESTful, WebSocket, authentification, gestion des utilisateurs, logique métier, etc.

  - **Tests backend** :  
    Utilisation de **PHPUnit** pour les tests unitaires et fonctionnels du backend.

- **Base de données** :  
  **MySQ**L
  → Stockage des utilisateurs, rooms, messages, amitiés, jeux…

- **Frontend** :  
  **Vue.js 3** + **TypeScript**  
  → SPA moderne, gestion de l’interface, communication temps réel (WebSocket), expérience utilisateur soignée.

  - **Tests frontend** :  
    Utilisation de **Cypress** pour les tests end-to-end (E2E) du parcours utilisateur et la vérification de l’UI en conditions réelles,  
    ainsi que **Vitest** pour les tests unitaires et fonctionnels des composants Vue et de la logique métier.



- **WebSocket** :  
  Intégré côté backend (Symfony) via **Ratchet** pour la gestion du temps réel (chat, jeux…).

- **Docker** :  
  Toute l’application est **containerisée** (backend, frontend, base de données, outils) pour une installation simplifiée.

- **Reverse Proxy / HTTPS** :  
  **Nginx** est utilisé en reverse proxy pour :
  - la gestion du HTTPS (certificat auto-signé en dev)
  - le routage vers les containers backend / WebSocket
  - la redirection automatique de HTTP vers HTTPS

- **Administration BDD** :
**phpMyAdmin** disponible en container pour explorer/modifier facilement la base de données MySQL.

- **Emails de dev** : 
  **Mailpit** utilisé pour intercepter et visualiser les mails d’inscription ou de réinitialisation de mot de passe sans envoyer de vrais mails en dehors du projet (pratique pour le debug).

## 🚀 Installation du projet Roomies

L’installation a été pensée pour être **facile** grâce à Docker.  
Toutes les dépendances (backend, frontend, MySQL, phpMyAdmin, Mailpit, Nginx…) sont containerisées.

### 1. Cloner le dépôt

```bash
git clone https://github.com/Mart1n-S/Roomies-WebSockets.git
cd Roomies-WebSockets
```

### 2. Initialiser le Backend Symfony

```bash
cd backend
composer install
```

- **Copier la configuration d’exemple** et l’adapter si besoin (normalement tout est prêt pour un usage local) :
  ```bash
  cp .env .env.local
  ```

#### Générer les clés JWT (obligatoire pour l’authentification)

```bash
php bin/console lexik:jwt:generate-keypair
```
> Les clés seront créées dans `config/jwt/`

#### Générer un certificat TLS auto-signé pour le WebSocket (dev)

```bash
mkdir -p certs
openssl req -x509 -newkey rsa:4096 -nodes -keyout certs/key.pem -out certs/cert.pem -days 365 -subj "/CN=localhost"
```

#### Générer les clefs VAPID pour les notifications push
```bash
web-push generate-vapid-keys
```
Placer les clés générées dans le fichier `.env.local` 

Et ajouter la clef publique dans le fichier `.env` du frontend 
### 3. Initialiser le Frontend Vue.js

```bash
cd ../frontend
npm install
```

### 4. Générer les certificats pour Nginx (si besoin)

- Si tu veux régénérer les certificats pour le proxy Nginx :
  
  ⚠️ Normalement, les certificats sont déjà fournis dans le projet, mais pour les régénérer :

  ```bash
  cd ../docker/backend/certs
  openssl req -x509 -newkey rsa:2048 -nodes -keyout localhost.key -out localhost.crt -days 365 -subj "/CN=localhost"
  cd ../../..
  ```

> **Note** : Par défaut, les certificats fournis permettent d’utiliser le HTTPS local (auto-signé).  
> ⚠️⚠️ Le navigateur peut lever une exception de sécurité (normal en local) il faut l’accepter pour continuer. Pour cela il faut se rendre sur https://localhost/ et accepter le certificat auto-signé idem pour http://localhost:5173/.

### 5. Lancer l’ensemble de l’application

Depuis la racine du projet :

```bash
docker compose up -d
```
### 6. Initialiser la base de données de test ⚠️
Avant de lancer l’application, il faut préparer la base de données qui va servir pour l'exécution des tests
1. Ouvrir phpMyAdmin : [http://localhost:8080/](http://localhost:8080/)  
2. Se connecter avec les identifiants par défaut :
   - **Utilisateur** : `root`
   - **Mot de passe**  : `root`
3. Aller dans l'onglet **Importer** et importer le fichier d'initialisation de la base de données de test se trouvant dans le dossier `database` du projet :
   - Chemin : `database/init_bdd_roomies_test.sql`
4. Valider l'importation.

### 7. Exécuter les migrations et les fixtures
Pour préparer la base de données avec les tables nécessaires et les données de test, exécute les commandes suivantes :

```bash
# Se connecter au conteneur backend en mode interactif
docker compose exec backend bash

# Exécuter les migrations pour créer les tables
php bin/console d:m:m

# Charger les fixtures pour la bdd classique
php bin/console d:f:l

# ⚠️ Si une erreur de contrainte apparaît lors du chargement des fixtures (cela peut arriver à cause de l’ordre ou d’un bug de données), relance simplement la commande fixtures :
php bin/console d:f:l
```


## C’est tout ! 🎉  
- L’application sera accessible sur [http://localhost:5173/](http://localhost:5173/) (frontend) 
😶‍🌫️ Voici des identifiants de test :
  - **Email** : `user@user.com`
  - **Mot de passe** : `password` 
  
- Backend Symfony : [https://localhost/api/](https://localhost/api/)  pour accéder à la doc API (Swagger) et tester les endpoints.
  
- phpMyAdmin : [http://localhost:8080/](http://localhost:8080/)  
- Mailpit (emails de dev) : [http://localhost:8025/](http://localhost:8025/)

---

> **Note pratique** :  
> Si vous souhaitez tester l’application avec **deux utilisateurs en simultané** sur la même machine, il faut utiliser **deux navigateurs différents** (ex : Chrome + Firefox), **ou** ouvrir un onglet en **navigation privée**.
>
> ⚠️ **Attention** : En navigation privée, il faudra **autoriser les cookies tiers**, car l’authentification s’appuie sur des cookies sécurisés.

## 📋 Gestion des tests

L’application Roomies possède une couverture de tests aussi bien côté backend que côté frontend.

### Backend (Symfony)

- **Lancer les tests unitaires et fonctionnels** :
    1. Se connecter au container backend :
        ```bash
        docker compose exec backend bash
        ```
    2. Depuis le dossier `/app`, exécuter :
        ```bash
        php bin/phpunit

        # Pour sortir du container
        exit
        ```

> Les tests couvrent la logique métier, les entités, la sécurité et l’API.

---

### Frontend (Vue.js)

- **Tests unitaires et fonctionnels (Vitest)** :
    1. Se connecter au container frontend :
        ```bash
        docker compose exec frontend sh
        ```
    2. Lancer les tests :
        ```bash
        npx vitest
        ```

- **Tests end-to-end (E2E, Cypress)** :
  Pour les tests de bout en bout, qui simulent des parcours utilisateur complets il faut sortir du container frontend et lancer Cypress depuis le dossier frontend :
    1. Lancer Cypress :
        ```bash
        # pour sortir du container frontend
        exit

        # depuis le dossier frontend
        cd frontend

        # lancer Cypress
        npx cypress open
        ```
    2. Ouverture de l’interface Cypress pour lancer les scénarios de bout en bout.
    3. Choisir E2E Testing et sélectionner le navigateur (par exemple Chrome).
    4. Lancer les tests suivants :
        - `add-friend.spec.ts` : → Vérifie qu’un utilisateur peut envoyer une demande d’ami à un autre utilisateur via le code ami (parcours complet : login, navigation, saisie, validation, toast de succès).


        - `create-game.spec.ts` : → Vérifie la création d’une nouvelle partie de jeu (ex : Morpion) : connexion, navigation vers la section jeux, ouverture du lobby, création d’une partie via la modal, et vérification de la présence de la partie dans le lobby.
        - `private-message.spec.ts` : → Vérifie l’envoi et la réception d’un message privé entre deux utilisateurs : connexion, ouverture d’une discussion privée, envoi d’un message, et vérification de l’affichage du message dans le chat.


