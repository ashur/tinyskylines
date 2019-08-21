#!/usr/bin/env bash

# Trigger build to add new palette to history

SCRIPTS=$(dirname "$0")
SRC=$SCRIPTS/../

source $SRC/.env
source $SCRIPTS/log.sh

curl -X POST -d {} $TINYSKYLINES_BUILD_HOOK
