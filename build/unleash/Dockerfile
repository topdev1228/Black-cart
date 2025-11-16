FROM node:lts-alpine3.14

ARG APP_DIR=/app
ARG APP_ENV=local

COPY . $APP_DIR/
WORKDIR /app
RUN apk add --no-cache make gcc g++ python3 bash && \
    npm install && \
    apk del make gcc g++ python3
COPY database/migrations/*.js /app/node_modules/unleash-server/dist/migrations/

CMD /usr/local/bin/npm start
