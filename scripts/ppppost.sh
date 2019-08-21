#!/usr/bin/env bash

# Generate skyline and post captioned image to Mastodon and Twitter

SCRIPTS=$(dirname "$0")
SRC=$SCRIPTS/../

source $SRC/.env
source $SCRIPTS/log.sh

TEMP=$TINYSKYLINES_TEMPDIR
DATA=$TINYSKYLINES_DATADIR

# Palette
PALETTE=$DATA/palette.json
if [ ! -f $PALETTE ]; then
	loggg "$PALETTE not found" "$TINYSKYLINES_LOGSDIR"
	exit 1
fi

TITLE=`cat $PALETTE | jq .title`
TITLE=${TITLE//\"/}

AUTHOR=`cat $PALETTE | jq .author`
AUTHOR=${AUTHOR//\"/}

CAPTION="“${TITLE//,/\,}” by $AUTHOR"

COLORS=`cat $PALETTE | jq .colors | jq 'join(",")'`
COLORS=${COLORS//\"/}

# Generate
GENERATED=$(/usr/bin/env php ${SRC}/generate.php ${COLORS})

if [ $? -eq 0 ]; then
	RESULT=$(/usr/bin/env ppppost to $TINYSKYLINES_BOTNAME --images "${TEMP}/skyline.png" --captions "${CAPTION}")

	# Error While Posting
	if [ $? -ne 0 ]; then
		loggg "$RESULT" "$TINYSKYLINES_LOGSDIR"
		exit 1
	fi

# Error While Generating
else
	loggg "$GENERATED" "$TINYSKYLINES_LOGSDIR"
	exit 1
fi
