<?php

/*
 * This file is part of Skylines
 */
namespace Skylines;

use Huxtable\CLI\Command;

/**
 * @command		schedule
 * @desc		Preview the schedule
 * @usage		schedule
 */
$command = new Command( 'schedule', 'Preview the schedule', function()
{
	$scheduleURL  = "https://cabrera-bots.s3.amazonaws.com/tinyskylines/schedule.json";
	$scheduleJSON = file_get_contents( $scheduleURL );
	$schedule = Schedule\Schedule::createFromJSON( $scheduleJSON );

	$currentTime = time();

	$pattern = '%s %-18s %s';
	$events = $schedule->getEvents();

	foreach( $events as $event )
	{
		$eventTime = $event->getTime();
		$timeDifference = $eventTime - $currentTime;

		if( $timeDifference < 0 )
		{
			echo sprintf( $pattern, '✓', $event->getName(), '' ) . PHP_EOL;
			continue;
		}

		$totalHoursUntilEvent = $timeDifference / 60 / 60;
		$hoursUntilEvent = floor( $totalHoursUntilEvent );
		$minutesUntilEvent = ceil( ($totalHoursUntilEvent - $hoursUntilEvent) * 60 );

		$timeUntilEvent = sprintf( '%02s:%02s remaining', $hoursUntilEvent, $minutesUntilEvent );

		echo sprintf( $pattern, ' ', $event->getName(), $timeUntilEvent ) . PHP_EOL;
	}
});

return $command;
