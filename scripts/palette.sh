#!/usr/bin/env bash

# Select first palette element and save to disk

SCRIPTS=$(dirname "$0")
SRC=$SCRIPTS/../

source $SRC/.env
source $SCRIPTS/log.sh

mkdir -p $TINYSKYLINES_DATADIR
PALETTE=$TINYSKYLINES_DATADIR/palette.json

PALETTES=`curl -s https://paletas.ashur.cab/api/history.json`
RESULT=`echo $PALETTES | jq ".[0]" 2>&1 > $PALETTE`

if [ $? -ne 0 ]; then
	loggg "$RESULT" "$TINYSKYLINES_LOGSDIR"
fi
