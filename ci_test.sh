#!/usr/bin/env bash

set -o errexit
set -o pipefail

function check_env_var() {
    if [[ -z "${!1}" ]] ; then
        echo "Error: Env var \"$1\" is required."
        exit 1
    fi
}

if [[ -e /app/vendor/bin/paratest ]] ; then
    cd /app
    export APP_ENV=testing
    php artisan config:cache
    npx vite build
    vendor/bin/paratest
else
    echo "Can't find paratest!" 1>&2
    exit 1
fi
