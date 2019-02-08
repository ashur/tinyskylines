<?php

/*
 * This file is part of Skylines
 */
namespace Skylines;

use Huxtable\Core\File;
use Huxtable\Core\Utils;
use Imagick;
use ImagickDraw;

class Bot
{
	/**
	 * @var	array
	 */
	protected $palettes=[];

	/**
	 * @return	void
	 */
	public function __construct()
	{
		/* Directories */
		$pathProject = dirname( dirname( __DIR__ ) );
		$this->dirProject = new File\Directory( $pathProject );
	}

	/**
	 * @param	Skylines\Palette	$palette
	 */
	public function addPalette( Palette $palette )
	{
		$this->palettes[] = $palette;
	}

	/**
	 * @return	void
	 */
	public function getPalettes()
	{
		$palettesURL = "https://free-brick.glitch.me/api/v1/palettes/random.txt";
		$apiToken = getenv( 'TINYSKYLINES_APIKEY' );

		// Create a stream
		$opts = [
			"http" => [
				"method" => "GET",
				"header" => "API-Token: {$apiToken}"
			]
		];

		$context = stream_context_create( $opts );
		$palettesContents = file( $palettesURL, false, $context );

		foreach( $palettesContents as $line )
		{
			$line = trim( $line );

			if( $line != '' && substr( $line, 0, 1 ) != '#' )
			{
				$colors = explode( ',', $line );
				$palette = new Palette();

				foreach( $colors as $color )
				{
					$palette->addColor( $color );
				}

				$this->addPalette( $palette );
			}
		}
	}

	/**
	 * @return	Skyline\Element\Element
	 */
	public function getRandomElement()
	{
		$randType = rand( 1, 100 );

		// 60%
		if( in_array( $randType, range( 1, 60 ) ) )
		{
			$element = new Element\BuildingFlat();
		}
		// 20%
		if( in_array( $randType, range( 61, 80 ) ) )
		{
			$element = new Element\BuildingFloating();
		}
		// 10%
		if( in_array( $randType, range( 81, 90 ) ) )
		{
			$element = new Element\BuildingShed();
		}
		// 4%
		if( in_array( $randType, range( 91, 95 ) ) )
		{
			$element = new Element\BuildingGable();
		}
		// 1%
		if( in_array( $randType, range( 96, 100 ) ) )
		{
			$element = new Element\BuildingWindows();
		}

		return $element;
	}

	/**
	 * @return	Skylines\Palette
	 */
	public function getRandomPalette()
	{
		if( count( $this->palettes ) == 0 )
		{
			$this->getPalettes();
		}

		$attempts = 0;
		$didFindMatch = false;

		return Utils::randomElement( $this->palettes );
	}

	/**
	 * @param	Skylines\Palette	$palette
	 * @return	Skylines\Skyline
	 */
	public function getSkyline( Palette $palette )
	{
		$skyline = new Skyline( $palette );

		$starsCount = rand( 2, 4 );
		for( $s = 0; $s < $starsCount; $s++ )
		{
			$star = new Star();
			$skyline->addStar( $star );
		}

		$foregroundElementsCount = rand( 3, 7 );
		if( $foregroundElementsCount % 2 == 0 )
		{
			$foregroundElementsCount--;
		}

		for( $fg = 0; $fg < $foregroundElementsCount; $fg++ )
		{
			$element = $this->getRandomElement();
			$skyline->addForegroundElement( $element );
		}

		$backgroundElementsCount = $foregroundElementsCount + rand( 2, 4 );
		for( $bg = 0; $bg < $backgroundElementsCount; $bg++ )
		{
			$element = $this->getRandomElement();
			$skyline->addBackgroundElement( $element );
		}

		return $skyline;
	}
}
