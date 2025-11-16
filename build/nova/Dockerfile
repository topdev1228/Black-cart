FROM node:14-alpine3.14
ARG APP_DIR=/app
ENV APP_DIR=$APP_DIR
RUN apk add --no-cache make gcc g++ python3 python2 libsass automake autoconf libpng libpng-dev libjpeg bash parallel git
COPY build/bin/build_nova_components /usr/local/bin/
WORKDIR /app