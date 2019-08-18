<?php

use Huxtable\Core\File;

$pathBase	= __DIR__;
$pathLib	= $pathBase . '/lib';
$pathApp	= $pathLib  . '/Skylines';
$pathVendor	= $pathBase . '/vendor';
$pathTemp	= getenv( 'TINYSKYLINES_TEMPDIR' );

/* Colors */
if( !isset( $argv[1] ) )
{
    echo 'usage: generate.php <color>,<color>,<color>...' . PHP_EOL;
    exit( 1 );
}
$colors = explode( ',', $argv[1] );
if( count( $colors ) < 3 )
{
    echo "Requires three or more colors: {$argv[1]}" . PHP_EOL;
    exit( 1 );
}

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
$palette = new Skylines\Palette();
foreach( $colors as $color )
{
    $palette->addColor( $color );
}

$skyline = $bot->getSkyline( $palette );
$skyline->render( $imageFile, 150, 50, 5 );
