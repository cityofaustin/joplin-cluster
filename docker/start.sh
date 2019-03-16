#!/usr/bin/env bash

set -o errexit

DOCKER_COMPOSE_FOLDER="./docker"
DOCKER_COMPOSE_FILE=""


#
# First we need to find out which service configuration we are going to run.
#
if [ "$JOPLIN_MODE" != "" ]; then
    DOCKER_COMPOSE_FILE_PATH="${DOCKER_COMPOSE_FOLDER}/joplin-cluster-${JOPLIN_MODE}.yml"
else
    DOCKER_COMPOSE_FILE_PATH="${DOCKER_COMPOSE_FOLDER}/joplin-cluster.yml"
fi


#
# Then we spin it up, rebuild if flag is present ...
#
echo "Starting Joplin Cluster based on: '${DOCKER_COMPOSE_FILE_PATH}'"

if [ "$REBUILD" == "on" ]; then
    docker-compose -f $DOCKER_COMPOSE_FILE_PATH up --build
else
    docker-compose -f $DOCKER_COMPOSE_FILE_PATH up
fi