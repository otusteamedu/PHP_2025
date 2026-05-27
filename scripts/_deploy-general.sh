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

function downloadConfigs {
    ENV_NAME=${ENV_NAME:-prod}
    CONFIG_REPO_DIR=$DEPLOY_DIR/my-config-repo
    CONFIG_REPO_URL="http://$MY_CONFIG_REPO_DEPLOY_USERNAME:$MY_CONFIG_REPO_DEPLOY_TOKEN@$MY_CONFIG_REPO_URL"

    sudo rm -rf "$CONFIG_REPO_DIR"
    git clone --depth 1 "$CONFIG_REPO_URL" "$CONFIG_REPO_DIR"

    if [ ! -f "$CONFIG_REPO_DIR/$ENV_NAME/.env" ]; then
        echo "Config file not found: $CONFIG_REPO_DIR/$ENV_NAME/.env"
        exit 1
    fi

    cp "$CONFIG_REPO_DIR/$ENV_NAME/.env" "$DEPLOY_DIR/releases/$CURRENT/.env"
}

function buildApp {
    cd $DEPLOY_DIR

    sudo docker compose up -d $CURRENT

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
downloadConfigs
buildApp
changeOwnership
startCurrentRelease
stopPrevRelease

