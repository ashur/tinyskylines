<?php

/*
 * This file is part of Skylines
 */
namespace Skylines;

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

	/* Define the target image file */
	$dirTemp = $bot->getTempDirectory();
	$fileImage = $dirTemp->child( 'tweet.png' );

	/* Generate the image */
	$bot->generateImage( $fileImage );

	/* Build the tweet */
	$tweet = new Twitter\Tweet();
	$tweet->attachMedia( $fileImage );

	/* Post it */
	try
	{
		$bot->postTweetToTwitter( $tweet );
	}
	catch( \Exception $e )
	{
		throw new Command\CommandInvokedException( $e->getMessage(), 1 );
	}
});

return $commandTweet;
