# frontend

This template should help get you started developing with Vue 3 in Vite.

## Recommended IDE Setup

[VSCode](https://code.visualstudio.com/) + [Volar](https://marketplace.visualstudio.com/items?itemName=Vue.volar) (and disable Vetur).

## Type Support for `.vue` Imports in TS

TypeScript cannot handle type information for `.vue` imports by default, so we replace the `tsc` CLI with `vue-tsc` for type checking. In editors, we need [Volar](https://marketplace.visualstudio.com/items?itemName=Vue.volar) to make the TypeScript language service aware of `.vue` types.

## Customize configuration

See [Vite Configuration Reference](https://vite.dev/config/).

## Project Setup

```sh
npm install
```

### Compile and Hot-Reload for Development

```sh
npm run dev
```

### Type-Check, Compile and Minify for Production

```sh
npm run build
```

### Run Unit Tests with [Vitest](https://vitest.dev/)

```sh
npm run test:unit
```

### Run End-to-End Tests with [Cypress](https://www.cypress.io/)

```sh
npm run test:e2e:dev
```

This runs the end-to-end tests against the Vite development server.
It is much faster than the production build.

But it's still recommended to test the production build with `test:e2e` before deploying (e.g. in CI environments):

```sh
npm run build
npm run test:e2e
```

### Lint with [ESLint](https://eslint.org/)

```sh
npm run lint
```




TODO: Finaliser l'authentification
- Ajouter le register => ok
- ajouter le mot de passe oublié => OK
- Voir pour mettre dans les toasts les message reussu par le bacjkend s'il y a sinon mettre un personalisé =>ok
- Ajouter la validation de l'email => ok
- ajouter la demande de nouvelle envoie de confirmation d'email => ok 
- Cote front rendre coherent validite duree cookies et validite duree jwt et refresh token =>ok
- Mettre à jour le refresh token et le fixe => ok rien a faire car des quil est invalide il est delete
- Si cookies ou jwt expiré envoyer le refresh token => Ok 
- 🧙‍♂️ La mise en place de docker semble ok penser a tout rechecker => OK
- Ajouter l'ouverture de la websocket a la connexion => ok 
- Ajouter la connexion si cookies ou jwt valide => ok --> a faire :  ouverture de la websocket => ok
  
- Ajouter la gestion de la déconnexion -> backend ok reste a mettre cote front

- creer le dashboard
- voir google doc pour la suite
- ajouter test a la fin