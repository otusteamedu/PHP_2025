#!/bin/bash

if [ -z "$CURRENT" ]; then
    echo "Please set CURRENT variable"
    exit 1
fi

if [ -z "$PREV" ]; then
    echo "Please set PREV variable"
    exit 1
fi

function downloadNewCode {
    sudo rm -rf $DEPLOY_DIR/releases/$CURRENT
    git clone http://gitlab-ci-token:${CI_JOB_TOKEN}@${CI_SERVER_FQDN}/${CI_PROJECT_PATH} $DEPLOY_DIR/releases/$CURRENT
}

function downloadConfig {
    sudo rm -rf $DEPLOY_DIR/config

    git clone http://gitlab-ci-token:${CI_JOB_TOKEN}@${CONFIG_REPO_URL} $DEPLOY_DIR/config

    if [ ! -d "$DEPLOY_DIR/config/$APP_ENV" ]; then
        echo "Config for env $APP_ENV not found"
        exit 1
    fi
}

function prepareConfig {
    cp $DEPLOY_DIR/config/$APP_ENV/nginx/*.conf $DEPLOY_DIR/nginx/

    cp $DEPLOY_DIR/config/$APP_ENV/haproxy/* $DEPLOY_DIR/haproxy/

    cp $DEPLOY_DIR/config/$APP_ENV/.env $DEPLOY_DIR/releases/$CURRENT/app/.env
}

function deleteConfig {
    sudo rm -rf $DEPLOY_DIR/config
}

function buildApp {
    cd $DEPLOY_DIR

    sudo docker compose up -d --build $CURRENT

    sudo docker exec --user root $CURRENT composer install --no-interaction --optimize-autoloader
}

function changeOwnership {
    sudo chown 1000:1000 -R $DEPLOY_DIR/releases/$CURRENT
}

function startCurrentRelease {
    cd $DEPLOY_DIR

    sudo docker compose up -d ${CURRENT}_nginx

    sleep 10

    sudo docker exec --user root gateway sh -c "echo \"set server blue_green/${CURRENT} state ready\" | socat stdio unix-connect:/sock/admin.sock"
}

function stopPrevRelease {
    cd $DEPLOY_DIR

    sudo docker exec --user root gateway sh -c "echo \"set server blue_green/${PREV} state maint\" | socat stdio unix-connect:/sock/admin.sock"

    sleep 10
    sudo docker compose stop $PREV ${PREV}_nginx
}

downloadNewCode
downloadConfig
prepareConfig
deleteConfig
buildApp
changeOwnership
startCurrentRelease
stopPrevRelease
