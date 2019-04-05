BOTNAME="@tinyskylines"
SRC=$(dirname "$0")

source "${SRC}/.env"

LOGS=$TINYSKYLINES_LOGSDIR
TEMP=$TINYSKYLINES_TEMPDIR

log()
{
	LOGFILE="`date '+%Y-%m-%d'`.txt"
	MESSAGE=$1

	mkdir -p $LOGS
	echo "`date`  ${MESSAGE}" >> "${LOGS}/${LOGFILE}"
}

# Generate
GENERATED=$(/usr/bin/env php ${SRC}/generate.php)

if [ $? -eq 0 ]; then
	RESULT=$(/usr/bin/env ppppost to $BOTNAME --images "${TEMP}/skyline.png")

	# Error While Posting
	if [ $? -ne 0 ]; then
		log "${RESULT}"
		exit 1
	fi

# Error While Generating
else
	log "${GENERATED}"
	exit 1
fi
