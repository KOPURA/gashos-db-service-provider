#!/bin/bash
set -e

PERSISTENCE_LOCATION=${HOME}/data

if [[ ! -d  "$PERSISTENCE_LOCATION" ]]; then
    mkdir -p "$PERSISTENCE_LOCATION"
fi;

pushd Docker > /dev/null

docker-compose up -d

popd > /dev/null