#!/usr/bin/env bash

# Select first palette element and save to disk

SCRIPTS=$(dirname "$0")
SRC=$SCRIPTS/../

source $SRC/.env
source $SCRIPTS/log.sh

mkdir -p $TINYSKYLINES_DATADIR
PALETTE=$TINYSKYLINES_DATADIR/palette.json

PALETTES=`curl -s https://paletas.ashur.cab/api/palettes.json`
LENGTH=`echo $PALETTES | jq length 2>&1`
if [ $? -eq 0 ]; then
	INDEX=$((1 + RANDOM % ($LENGTH - 1)))
	echo $PALETTES | jq ".[$INDEX]" > $PALETTE
else
	loggg "$LENGTH" "$TINYSKYLINES_LOGSDIR"
fi
