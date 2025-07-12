FROM node:lts-alpine 

WORKDIR /app

COPY ../../frontend/ ./

RUN npm install

EXPOSE 5173

CMD ["npm", "run", "dev", "--", "--host"]