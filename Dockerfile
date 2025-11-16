FROM gcr.io/blackcart-cicd/php-8-dot-3-fpm-alpine:bf3fc8ab26b01d9a28c814775409076d17ab1f5e-x86_64 AS base
ARG APP_DIR="/app" BRANCH_NAME COMMIT_SHA REPO_NAME SHORT_SHA GITHUB_TOKEN

ENV APP_DIR="$APP_DIR" \
    BRANCH_NAME=$BRANCH_NAME \
    COMMIT_SHA=$COMMIT_SHA \
    REPO_NAME=$REPO_NAME \
    SHORT_SHA=$SHORT_SHA \
    DOCUMENT_ROOT="/app/public" \
    DD_VERSION="${BRANCH_NAME}-${SHORT_SHA}" \
    FRONT_CONTROLLER_FILE="index.php" \
    GITHUB_TOKEN="$GITHUB_TOKEN"

COPY --chown=www-data:www-data . $APP_DIR/
COPY build/bin/entrypoint /usr/local/bin/entrypoint
COPY build/bin/run_composer /usr/local/bin/run_composer
RUN apk add nodejs npm ruby-dev build-base libffi-dev && \
    mkdir -p /app/database/data && \
    touch /app/database/data/blackcart.sqlite3 && \
    cd $APP_DIR && \
    install_composer && \
    gem install bundler && \
    npm install && \
    cd -
RUN chown -R www-data:www-data /var/lib/nginx
VOLUME /app
WORKDIR $APP_DIR

FROM base AS env
ARG APP_ENV
ENV APP_ENV=$APP_ENV

# Run JS build
RUN rm -f .env.local && \
    npx vite build && \
    su -s /bin/bash -m www-data -pc "/usr/local/bin/php -d auto_prepend_file='' /usr/local/bin/composer run post-build-cmd"
