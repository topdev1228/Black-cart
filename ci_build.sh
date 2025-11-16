#!/usr/bin/env bash

set -o errexit
set -o pipefail

function check_env_var() {
    if [[ -z "${!1}" ]] ; then
        echo "Error: Env var \"$1\" is required."
        exit 1
    fi
}

readonly ARCH="x86_64"

check_env_var "REPO_NAME"
check_env_var "COMMIT_SHA"
check_env_var "SHORT_SHA"
check_env_var "BRANCH_NAME"
check_env_var "PROJECT_ID"
check_env_var "APP_ENV"
check_env_var "COMPOSER_TOKEN"

export DOCKER_BUILDKIT=0
export COMPOSE_DOCKER_CLI_BUILD=0

echo "Building image for ${REPO_NAME}:${COMMIT_SHA}-${ARCH}"
docker buildx build --platform linux/amd64 \
    --build-arg REPO_NAME=${REPO_NAME} \
    --build-arg COMMIT_SHA=${COMMIT_SHA} \
    --build-arg SHORT_SHA=${SHORT_SHA} \
    --build-arg BRANCH_NAME=${BRANCH_NAME} \
    --build-arg APP_ENV=${APP_ENV} \
    --build-arg GITHUB_TOKEN=${COMPOSER_TOKEN} \
    --target=base \
    -t gcr.io/${PROJECT_ID}/${REPO_NAME}:${COMMIT_SHA}-${ARCH} \
    -f Dockerfile \
    .

echo "Building image for ${REPO_NAME}:staging-${COMMIT_SHA}-${ARCH}"
docker buildx build --platform linux/amd64 \
    --build-arg REPO_NAME=${REPO_NAME} \
    --build-arg COMMIT_SHA=${COMMIT_SHA} \
    --build-arg SHORT_SHA=${SHORT_SHA} \
    --build-arg BRANCH_NAME=${BRANCH_NAME} \
    --build-arg APP_ENV=staging \
    --build-arg GITHUB_TOKEN=${COMPOSER_TOKEN} \
    --target=env \
    -t gcr.io/${PROJECT_ID}/${REPO_NAME}:staging-${COMMIT_SHA}-${ARCH} \
    -f Dockerfile \
    .

echo "Building image for ${REPO_NAME}:production-${COMMIT_SHA}-${ARCH}"
docker buildx build --platform linux/amd64 \
    --build-arg REPO_NAME=${REPO_NAME} \
    --build-arg COMMIT_SHA=${COMMIT_SHA} \
    --build-arg SHORT_SHA=${SHORT_SHA} \
    --build-arg BRANCH_NAME=${BRANCH_NAME} \
    --build-arg APP_ENV=production \
    --build-arg GITHUB_TOKEN=${COMPOSER_TOKEN} \
    --target=env \
    -t gcr.io/${PROJECT_ID}/${REPO_NAME}:production-${COMMIT_SHA}-${ARCH} \
    -f Dockerfile \
    .
