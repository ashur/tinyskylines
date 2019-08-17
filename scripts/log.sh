loggg()
{
	LOGS=$2
	LOGFILE="`date '+%Y-%m-%d'`.txt"
	MESSAGE=$1

	mkdir -p $LOGS
	echo "`date`  ${MESSAGE}" >> "${LOGS}/${LOGFILE}"
}
