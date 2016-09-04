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
	const IMAGE_HEIGHT = 250;
	const IMAGE_WIDTH  = 750;
	const PIXEL_SIZE   = 5;

	/**
	 * @var	array
	 */
	protected $palettes=[];

	/**
	 * @param	string							$name			Bot name
	 * @param	Huxtable\Core\File\Directory	$dirData
	 * @param	array							$palettes
	 * @return	void
	 */
	public function __construct( $name, File\Directory $dirData )
	{
		parent::__construct( $name, $dirData );

		$this->image = new Imagick();
		$this->draw = new ImagickDraw();
	}

	/**
	 * @param	Skylines\Palette	$palette
	 */
	public function addPalette( Palette $palette )
	{
		$this->palettes[] = $palette;
	}

	/**
	 * @param	int					$xOffset
	 * @param	Skylines\Building	$building
	 */
	public function drawBuilding( $xOffset, Building $building )
	{
		$imageBuilding = $building->draw();

		$horizonY = 180;
		$buildingY = $horizonY - $imageBuilding->getImageHeight();

		$this->image->compositeImage( $imageBuilding, Imagick::COMPOSITE_DEFAULT, $xOffset, $buildingY );
	}

	/**
	 * @param	string	$color
	 */
	public function drawHorizon( $color )
	{
		$horizonY = 180;
		$this->draw->setFillColor( $color );
		$this->draw->rectangle( 0, $horizonY, self::IMAGE_WIDTH, self::IMAGE_HEIGHT );
	}

	/**
	 * @param	string	$color
	 */
	public function drawSkyGradient( $color )
	{
		$this->draw->setFillColor( "{$color}80" );
		$this->draw->rectangle( 0, 0, self::IMAGE_WIDTH, 30 );

		$this->draw->setFillColor( "{$color}70" );
		$this->draw->rectangle( 0, 31, self::IMAGE_WIDTH, 60 );

		$this->draw->setFillColor( "{$color}60" );
		$this->draw->rectangle( 0, 61, self::IMAGE_WIDTH, 90 );

		$this->draw->setFillColor( "{$color}50" );
		$this->draw->rectangle( 0, 91, self::IMAGE_WIDTH, 120 );

		$this->draw->setFillColor( "{$color}40" );
		$this->draw->rectangle( 0, 121, self::IMAGE_WIDTH, 150 );

		$this->draw->setFillColor( "{$color}30" );
		$this->draw->rectangle( 0, 151, self::IMAGE_WIDTH, 180 );
	}

	/**
	 * @param	Huxtable\Core\File\File		$fileImage
	 */
	public function generateImage( File\File $fileImage )
	{
		$palette = Utils::randomElement( $this->palettes );

		$skyColor = $palette->getSkyColor();
		$this->image->newImage( self::IMAGE_WIDTH, self::IMAGE_HEIGHT, $skyColor );
		$this->image->setImageFormat( 'png' );

		/* Sky */
		$skyGradientColor = $palette->getBuildingColor();
		$this->drawSkyGradient( $skyGradientColor );

		/* Horizon */
		$horizonColor = $palette->getHorizonColor();
		$this->drawHorizon( $horizonColor );
		$this->image->drawImage( $this->draw );

		$previousXOffset = floor( self::IMAGE_WIDTH * 0.25 );
		do
		{
			/* Buildings */
			$building = new Building( $skyColor, 1 );
			$this->drawBuilding( $previousXOffset, $building );

			$previousXOffset = $building->getWidth() + $previousXOffset;
		}
		while( $previousXOffset < floor( self::IMAGE_WIDTH * 0.75 ) );

		$previousXOffset = floor( self::IMAGE_WIDTH * 0.33 );
		do
		{
			/* Buildings */
			$building = new Building( $horizonColor, 1 );
			$this->drawBuilding( $previousXOffset, $building );

			$gapWidth = rand( 0, 1 ) * self::PIXEL_SIZE;
			$previousXOffset = $building->getWidth() + $previousXOffset + $gapWidth;
		}
		while( $previousXOffset < floor( self::IMAGE_WIDTH * 0.67 ) );

		$fileImage->putContents( $this->image->getImageBlob() );
	}
}
