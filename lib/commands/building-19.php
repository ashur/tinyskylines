<?php

/*
 * This file is part of Skylines
 */
namespace Skylines;

use Huxtable\Bot\Twitter;
use Huxtable\CLI\Command;
use Huxtable\Core\File;

/**
 * @command		building-19
 * @desc			Building 19
 * @usage			building-19 [--no-tweet]
 */
$commandDev = new Command( 'building-19', 'Building 19', function()
{
	GLOBAL $bot;

	/*
	 * Files
	 */
	$dirTemp = $bot->getTempDirectory();
	$imageFile = $dirTemp->child( 'tweet.gif' );
	$jsonFile = $dirTemp->child( 'tweet.json' );

	/*
	 * Render
	 */
	$palette = new Palette();

	$palette->setBackgroundColor( '800699' );
	$palette->setForegroundColor( '240d83' );
	$palette->setGradientColor( '2db4ed' );

	$skyline = $bot->getSkyline( $palette );

	$element = new Element\Building19();

	$lightedWindows[] = '3,2';
	$lightedWindows[] = '3,3';
	$lightedWindows[] = '3,5';
	$lightedWindows[] = '3,6';
	$lightedWindows[] = '4,1';
	$lightedWindows[] = '4,2';
	$lightedWindows[] = '4,3';
	$lightedWindows[] = '4,4';
	$lightedWindows[] = '4,5';
	$lightedWindows[] = '4,6';
	$lightedWindows[] = '4,7';
	$lightedWindows[] = '5,1';
	$lightedWindows[] = '5,2';
	$lightedWindows[] = '5,3';
	$lightedWindows[] = '5,4';
	$lightedWindows[] = '5,5';
	$lightedWindows[] = '5,6';
	$lightedWindows[] = '5,7';
	$lightedWindows[] = '6,2';
	$lightedWindows[] = '6,3';
	$lightedWindows[] = '6,4';
	$lightedWindows[] = '6,5';
	$lightedWindows[] = '6,6';
	$lightedWindows[] = '7,3';
	$lightedWindows[] = '7,4';
	$lightedWindows[] = '7,5';
	$lightedWindows[] = '8,4';

	/*
	 * Gif
	 */
	$gifFile = $dirTemp->child( 'tweet.gif' );
	$gifImage = new \Imagick();
	$gifImage->setFormat( 'gif' );

	$frameDelay = 200;
	while( count( $lightedWindows ) >= 0 )
	{
		$skyline->insertForegroundElement( $element );
		$skyline->render( $imageFile, 150, 50, 5 );

		$gifFrameImage = new \Imagick();
		$gifFrameImage->readImage( $imageFile );

		$gifFrameImage->setImageDelay( $frameDelay );
		$gifImage->addImage( $gifFrameImage );

		if( count( $lightedWindows ) == 0 )
		{
			break;
		}

		/* Windows */
		shuffle( $lightedWindows );

		$windowsToTurnOn = count( $lightedWindows ) <= 2 ? 1 : rand( 1, 2 );
		for( $w = 0; $w < $windowsToTurnOn; $w++ )
		{
			$windowCoordinateString = array_pop( $lightedWindows );
			$windowCoordinates = explode( ',', $windowCoordinateString );

			$element->turnOnWindowLight( $windowCoordinates[0], $windowCoordinates[1] );
		}

		$frameDelay = count( $lightedWindows ) > 0 ? rand( 10, 120 ) : 600;
	}

	$gifFile->putContents( $gifImage->getImagesBlob() );

	if( $this->getOptionValue( 'no-tweet' ) )
	{
		return;
	}

	/*
	 * Tweet
	 */
	$tweet = new Twitter\Tweet();
	$tweet->attachMedia( $gifFile );

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
