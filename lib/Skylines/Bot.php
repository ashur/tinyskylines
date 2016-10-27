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
		$palettesURL = 'https://www.dropbox.com/s/26zjp6fgwn9w8fv/palettes.txt?dl=1';
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
	 * @return	Skylines\Palette
	 */
	public function getRandomPalette()
	{
		if( count( $this->palettes ) == 0 )
		{
			$this->getPalettes();
		}

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

		$foregroundElementsCount = rand( 5, 8 );
		for( $fg = 0; $fg < $foregroundElementsCount; $fg++ )
		{
			$building = new Building();
			$skyline->addForegroundElement( $building );
		}

		$backgroundElementsCount = $foregroundElementsCount + rand( 2, 4 );
		for( $bg = 0; $bg < 9; $bg++ )
		{
			$building = new Building();
			$skyline->addBackgroundElement( $building );
		}

		return $skyline;
	}
}
