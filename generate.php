<?php

use Huxtable\Core\File;

$pathBase	= __DIR__;
$pathLib	= $pathBase . '/lib';
$pathApp	= $pathLib  . '/Skylines';
$pathVendor	= $pathBase . '/vendor';
$pathTemp	= getenv( 'TINYSKYLINES_TEMPDIR' );

/*
 * Initialize autoloading
 */
include_once( $pathApp . '/Autoloader.php' );
Skylines\Autoloader::register();

include_once( $pathVendor . '/huxtable/core/autoload.php' );
include_once( $pathVendor . '/huxtable/pixel/autoload.php' );

/*
 * Some basics
 */
$dirApp = new File\Directory( $pathBase );
$dirLib = $dirApp->childDir( 'lib' );

$pathTemp = str_replace( '~', getenv( 'HOME' ), $pathTemp );
if( $pathTemp == false )
{
	echo 'Missing required environment variable TINYSKYLINES_TEMPDIR' . PHP_EOL;
	exit( 1 );
}
$dirTemp = new File\Directory( $pathTemp );
if( !$dirTemp->exists() )
{
	$dirTemp->create();
}

/*
 * Bot configuration
 */
$bot = new Skylines\Bot();

/*
 * Files
 */
$imageFile = $dirTemp->child( 'skyline.png' );

/*
 * Generate the image
 */
$palette = $bot->getRandomPalette();
$skyline = $bot->getSkyline( $palette );
$skyline->render( $imageFile, 150, 50, 5 );
