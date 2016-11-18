<?php

/*
 * This file is part of Skylines
 */
namespace Skylines;

use Huxtable\Bot\Twitter;
use Huxtable\CLI\Command;
use Huxtable\Core\File;

/**
 * @command		b19
 * @desc		Building 19
 * @usage		b19 [--no-tweet]
 */
$commandDev = new Command( 'b19', 'Building 19', function()
{
	GLOBAL $bot;

	/*
	 * Files
	 */
	$dirTemp = $bot->getTempDirectory();
	$imageFile = $dirTemp->child( 'tweet.png' );
	$jsonFile = $dirTemp->child( 'tweet.json' );

	/*
	 * Render
	 */
	$palette = new Palette();

	$palette->setBackgroundColor( '1f8a70' );
	$palette->setForegroundColor( '004358' );
	$palette->setGradientColor( 'bedb39' );

	$skyline = $bot->getSkyline( $palette );

	$elementLeft = new Element\Building19( 8, 18, 2 );
	$elementRight = new Element\Building19( 15, 24, 0 );

	$elementLeftOffsetCols = 1;
	$elementLeftOffsetRows = -1;

	$elementRightOffsetCols = -3;
	$elementRightOffsetRows = 2;

	$lightedWindows[] = '1,2';
	$lightedWindows[] = '2,2';
	$lightedWindows[] = '4,2';
	$lightedWindows[] = '5,2';
	$lightedWindows[] = '0,3';
	$lightedWindows[] = '1,3';
	$lightedWindows[] = '2,3';
	$lightedWindows[] = '3,3';
	$lightedWindows[] = '4,3';
	$lightedWindows[] = '5,3';
	$lightedWindows[] = '6,3';
	$lightedWindows[] = '0,4';
	$lightedWindows[] = '1,4';
	$lightedWindows[] = '2,4';
	$lightedWindows[] = '3,4';
	$lightedWindows[] = '4,4';
	$lightedWindows[] = '5,4';
	$lightedWindows[] = '6,4';
	$lightedWindows[] = '1,5';
	$lightedWindows[] = '2,5';
	$lightedWindows[] = '3,5';
	$lightedWindows[] = '4,5';
	$lightedWindows[] = '5,5';
	$lightedWindows[] = '2,6';
	$lightedWindows[] = '3,6';
	$lightedWindows[] = '4,6';
	$lightedWindows[] = '3,7';

	foreach( $lightedWindows as $windowCoordinateString )
	{
		$windowCoordinates = explode( ',', $windowCoordinateString );
		$elementLeft->turnOnWindowLight( $windowCoordinates[0] + $elementLeftOffsetCols, $windowCoordinates[1] + $elementLeftOffsetRows, '#bedb39' );
		$elementRight->turnOnWindowLight( $windowCoordinates[0] + $elementRightOffsetCols, $windowCoordinates[1] + $elementRightOffsetRows, '#bedb39' );
	}

	$skyline->insertForegroundElement( $elementLeft, -1 );
	$skyline->insertForegroundElement( $elementRight, 0 );

	$elementLast = new Element\BuildingFloating();
	$elementLast->setLeftMargin( 2 );
	$skyline->insertForegroundElement( $elementLast, 1 );

	$skyline->render( $imageFile, 150, 50, 5 );

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

$commandDev->registerOption( 'no-tweet' );

return $commandDev;
