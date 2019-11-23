#!/usr/bin/env bash

# Select first palette element and save to disk

if [ "$1" == "" ]; then
	echo "usage: ./palette.sh <url>"
	exit 1
fi

PALETTES_URL=$1

SCRIPTS=$(dirname "$0")
SRC=$SCRIPTS/../

source $SRC/.env
source $SCRIPTS/log.sh

mkdir -p $TINYSKYLINES_DATADIR
PALETTE=$TINYSKYLINES_DATADIR/palette.json

PALETTES=`curl -s $PALETTES_URL`
RESULT=`echo $PALETTES | jq ".[0]" 2>&1 > $PALETTE`

if [ $? -ne 0 ]; then
	loggg "$RESULT" "$TINYSKYLINES_LOGSDIR"
fi
