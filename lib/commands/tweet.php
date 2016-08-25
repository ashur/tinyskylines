<?php

use Huxtable\CLI\Command;

/**
 * @command		tweet
 * @desc		Generate a logo and tweet it
 * @usage		tweet
 */
$commandTweet = new Command( 'tweet', 'Generate a logo and tweet it', function()
{
	GLOBAL $bot;

	try
	{
		$logo = $bot->generateLogo();
		$bot->postImageToTwitter( $logo );
	}
	catch( \Exception $e )
	{
		throw new Command\CommandInvokedException( $e->getMessage(), 1 );
	}
});

return $commandTweet;
