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
	 * Render
	 */
	$palette = $bot->getRandomPalette();
	$skyline = $bot->getSkyline( $palette );

	$skyline->render( $imageFile, 150, 50, 5 );

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

	/*
	 * Save Skyline definition to disk
	 */
	$tweetData['skyline'] = $skyline;

	$json = json_encode( $tweetData );
	$jsonFile->putContents( $json );
});

return $commandTweet;
