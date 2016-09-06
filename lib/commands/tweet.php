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

	/* 3: Colorful */
	$palette = new Palette();

	$palette->addColor( '30bbb4' );
	$palette->addColor( 'f4b278' );
	$palette->addColor( 'd54b43' );
	$palette->addColor( 'ee736e' );

	$bot->addPalette( $palette );

	/* Monument Valley */
	$palette = new Palette();

	$palette->addColor( '6c2f56' );
	$palette->addColor( '48c29c' );
	$palette->addColor( 'b9dab9' );
	$palette->addColor( 'bbbf0e' );
	$palette->addColor( 'f48802' );

	$bot->addPalette( $palette );

	/* https://color.adobe.com/Vitamin-C-color-theme-492199 */
	$palette = new Palette();

	$palette->addColor( '004358' );
	$palette->addColor( '1f8a70' );
	$palette->addColor( 'bedb39' );
	$palette->addColor( 'ffe11a' );
	$palette->addColor( 'fd7400' );

	$bot->addPalette( $palette );

	/* https://color.adobe.com/Pear-Lemon-Fizz-color-theme-1833017 */
	$palette = new Palette();

	$palette->addColor( '04bfbf' );
	$palette->addColor( 'cafcd8' );
	$palette->addColor( 'f7e967' );
	$palette->addColor( 'a9cf54' );
	$palette->addColor( '588f27' );

	$bot->addPalette( $palette );

	/* https://color.adobe.com/Salmon-on-Ice-color-theme-2291686 */
	$palette = new Palette();

	$palette->addColor( '3e454c' );
	$palette->addColor( '2185c5' );
	$palette->addColor( '7ecefd' );
	$palette->addColor( 'fff6e5' );
	$palette->addColor( 'ff7f66' );

	$bot->addPalette( $palette );

	/* https://color.adobe.com/KnotJustNautical-color-theme-2565165 */
	$palette = new Palette();

	$palette->addColor( '2c3e50' );
	$palette->addColor( 'fc4349' );
	$palette->addColor( 'd7dadb' );
	$palette->addColor( '6dbcdb' );
	$palette->addColor( 'ffffff' );

	$bot->addPalette( $palette );

	/* https://color.adobe.com/Ventana-Azul-color-theme-2159606 */
	$palette = new Palette();

	$palette->addColor( 'f2385a' );
	$palette->addColor( 'f5a503' );
	$palette->addColor( 'e9f1df' );
	$palette->addColor( '4ad9d9' );
	$palette->addColor( '36b1bf' );

	$bot->addPalette( $palette );

	/* https://color.adobe.com/Woman-in-purple-dress-color-theme-32850 */
	$palette = new Palette();

	$palette->addColor( 'f9e4ad' );
	$palette->addColor( 'e6b098' );
	$palette->addColor( 'cc4452' );
	$palette->addColor( '723147' );
	$palette->addColor( '31152b' );

	$bot->addPalette( $palette );

	/* https://color.adobe.com/Honey-Pot-color-theme-1490158 */
	$palette = new Palette();

	$palette->addColor( '105b63' );
	$palette->addColor( 'fffad5' );
	$palette->addColor( 'ffd34e' );
	$palette->addColor( 'db9e36' );
	$palette->addColor( 'bd4932' );

	$bot->addPalette( $palette );

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
