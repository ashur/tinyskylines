#!/usr/bin/env bash

SCRIPTS=$(dirname "$0")
SRC=$(dirname "$0")/../

source $SRC/.env
source $SCRIPTS/log.sh

PALETTE=$TINYSKYLINES_DATADIR/palette.json

PALETTES=`curl -s https://paletas.ashur.cab/api/palettes.json`
LENGTH=`echo $PALETTES | jq length 2>&1`
if [ $? -eq 0 ]; then
	INDEX=$((1 + RANDOM % ($LENGTH - 1)))
	echo $PALETTES | jq ".[$INDEX]" > $PALETTE
else
	loggg "$LENGTH" "$TINYSKYLINES_LOGSDIR"
fi
