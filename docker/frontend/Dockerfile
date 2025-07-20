# Utilise l’image Node officielle (version LTS, légère Alpine)
FROM node:lts-alpine 

# Définit le dossier de travail (tout sera exécuté depuis ce dossier)
WORKDIR /app

# Copie le code source du frontend dans le conteneur (depuis le host vers le dossier de travail)
COPY ../../frontend/ ./

# Installe les dépendances npm (node_modules)
RUN npm install

# Ouvre le port 5173 (Vite.js par défaut)
EXPOSE 5173

# Commande de démarrage : lance le serveur de dev Vite (accessible de l’extérieur grâce à --host)
CMD ["npm", "run", "dev", "--", "--host"]
