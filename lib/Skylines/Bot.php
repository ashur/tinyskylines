<?php

/*
 * This file is part of Skylines
 */
namespace Skylines;

use Huxtable\Bot\History;
use Huxtable\Core\Config;
use Huxtable\Core\File;
use Huxtable\Core\Utils;
use Imagick;
use ImagickDraw;

class Bot extends \Huxtable\Bot\Bot
{
	/**
	 * @var	array
	 */
	protected $palettes=[];

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
		// $palettesURL = 'https://www.dropbox.com/s/26zjp6fgwn9w8fv/palettes.txt?dl=1';
		$palettesURL = 'https://discreet-grouse.glitch.me/api/palettes.txt';
		$palettesContents = file( $palettesURL );

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

		do
		{
			$palette = Utils::randomElement( $this->palettes );
			if( !$this->history->domainEntryExists( 'palette', "{$palette}" ) )
			{
				$didFindMatch = true;
				break;
			}

			$attempts++;
		}
		while( $attempts < 10 );

		if( !$didFindMatch )
		{
			$this->history->resetDomain( 'palette' );
			$palette = Utils::randomElement( $this->palettes );
		}

		$this->history->addDomainEntry( 'palette', "{$palette}" );

		return $palette;
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
