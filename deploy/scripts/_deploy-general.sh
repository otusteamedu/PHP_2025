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

function buildApp {
    cd $DEPLOY_DIR

    sudo docker compose up -d $CURRENT
    sudo docker exec --user root "$CURRENT" sh -c "
        cd /app/app &&
        composer install --no-interaction --optimize-autoloader
    "
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
buildApp
changeOwnership
startCurrentRelease
stopPrevRelease
