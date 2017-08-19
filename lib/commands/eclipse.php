<?php

/*
 * This file is part of Skylines
 */
namespace Skylines;

use Huxtable\Bot\Twitter;
use Huxtable\CLI\Command;
use Huxtable\Core\File;

/**
 * @command		eclipse
 * @desc		A special eclipse skyline combo
 * @usage		eclipse [--dark] [--no-tweet]
 */
$command = new Command( 'eclipse', 'Eclipse', function()
{
	GLOBAL $bot;

	/*
	 * Files
	 */
	$dirTemp = $bot->getTempDirectory();

	/* Data files */
	if( $this->getOptionValue( 'dark' ) === true )
	{
		$jsonFile = 'https://cabrera-bots.s3.amazonaws.com/tinyskylines/skylines/eclipse-dark.json';
	}
	else
	{
		$jsonFile = 'https://cabrera-bots.s3.amazonaws.com/tinyskylines/skylines/eclipse-bright.json';
	}

	$jsonData = json_decode( file_get_contents( $jsonFile ), true );
	if( json_last_error() !== 0 )
	{
		throw new Command\CommandInvokedException( "Invalid data: '" . json_last_error_msg() . "'.", 1 );
	}

	$imageFile = $dirTemp->child( 'tweet.png' );

	/*
	 * Skyline
	 */
	$skyline = Skyline::getInstanceFromData( $jsonData );

	/*
	 * Render
	 */
	$skyline->render( $imageFile, 150, 50, 5 );

	/*
	 * Tweet
	 */
	if( $this->getOptionValue( 'no-tweet' ) )
 	{
 		return;
 	}

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

$command->registerOption( 'dark' );
$command->registerOption( 'no-tweet' );

return $command;
