<?php

/*
 * This file is part of Skylines
 */
namespace Skylines;

use Huxtable\Bot\Slack;
use Huxtable\Bot\Twitter;
use Huxtable\CLI\Command;
use Huxtable\Core\File;

/**
 * @command		tweet
 * @desc		Generate a logo and tweet it
 * @usage		tweet
 */
$commandTweet = new Command( 'tweet', 'Generate a logo and tweet it', function()
{
	GLOBAL $bot;

	/*
	 * Files
	 */
	$dirTemp = $bot->getTempDirectory();
	$imageFile = $dirTemp->child( 'tweet.png' );
	$jsonFile = $dirTemp->child( 'tweet.json' );

	/*
	 * Scheduled Events
	 */
	$scheduleURL  = "https://cabrera-bots.s3.amazonaws.com/tinyskylines/schedule.json";
	$scheduleJSON = file_get_contents( $scheduleURL );
	$schedule = Schedule\Schedule::createFromJSON( $scheduleJSON );

	$currentTime = time();
	$startTime   = $currentTime - 5 * 60;	// 5-minute window on either side
	$endTime     = $currentTime + 5 * 60;

	$scheduledEvents = $schedule->getEventsInRange( $startTime, $endTime );

	/* Generate scheduled skyline */
	if( count( $scheduledEvents ) > 0 )
	{
		$event = $scheduledEvents[0];

		$skylineJSON = file_get_contents( $event->getURL() );
		$skylineData = json_decode( $skylineJSON, true );

		$skyline = Skyline::getInstanceFromData( $skylineData );
	}

	/* Generate random skyline */
	else
	{
		$palette = $bot->getRandomPalette();
		$skyline = $bot->getSkyline( $palette );
	}

	/*
	 * Render
	 */
	$skyline->render( $imageFile, 150, 50, 5 );

	/*
	 * Save Skyline definition to disk
	 */
	$tweetData['skyline'] = $skyline;

	$json = json_encode( $tweetData, JSON_PRETTY_PRINT );
	$jsonFile->putContents( $json );

	if( $this->getOptionValue( 'no-tweet' ) )
	{
		return;
	}

	/*
	 * Tweet
	 */
	$tweet = new Twitter\Tweet();
	$tweet->attachMedia( $imageFile );

	/* Post it */
	try
	{
		$bot->postTweetToTwitter( $tweet );
	}
	catch( \Exception $e )
	{
		/* Slack */
		$message = new Slack\Message();
		$attachment = new Slack\Attachment( "Post to Twitter" );

		$attachment->setColor( 'danger' );
		$attachment->addField( 'Status', 'Failed', true );
		$attachment->addField( 'Message', $e->getMessage(), true );

		$message->addAttachment( $attachment );
		$bot->postMessageToSlack( $message );

		throw new Command\CommandInvokedException( $e->getMessage(), 1 );
	}
});

$commandTweet->registerOption( 'no-tweet' );

return $commandTweet;
