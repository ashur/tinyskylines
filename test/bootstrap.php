<?php
/*
 * This file is part of Skylines
 */

$projectDir = dirname( __DIR__ );
$pathApp    = $projectDir . '/lib/Skylines';
$pathVendor	= $projectDir . '/vendor';

include_once( $pathApp . '/Autoloader.php' );
Skylines\Autoloader::register();

include_once( $pathVendor . '/huxtable/bot/autoload.php' );
include_once( $pathVendor . '/huxtable/cli/autoload.php' );
include_once( $pathVendor . '/huxtable/core/autoload.php' );
include_once( $pathVendor . '/huxtable/pixel/autoload.php' );
