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
	 * @param	int		$x
	 * @param	int		$y
	 * @param	string	$color
	 */
	public function drawAt( $x, $y, $color )
	{
		$x1 = $x * Bot::PIXEL_SIZE;
		$x2 = $x1 + Bot::PIXEL_SIZE;

		$y1 = $y * Bot::PIXEL_SIZE;
		$y2 = $y1 + Bot::PIXEL_SIZE;

		$pixelIterator = $this->image->getPixelIterator();

		for( $row = $y1; $row < $y2; $row++ )
		{
			$pixelIterator->setIteratorRow( $row );
			$pixels = $pixelIterator->getCurrentIteratorRow();

			for( $col = $x1; $col < $x2; $col++ )
			{
				$pixel = $pixels[$col];
				$pixel->setColor( $color );

				$pixelIterator->syncIterator();
			}
		}
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
	 * Set transparent pixel to fight Twitter compression
	 *
	 * @return	void
	 */
	public function drawTransparentPixel()
	{
		$iterator = $this->image->getPixelIterator();
		$iterator->setIteratorRow( 0 );

		/* Get pixels in row */
		$row = $iterator->getCurrentIteratorRow();

		$pixel = $row[0];
		$pixel->setColor( '#ffffff00');

		/* Sync data back to image */
		$iterator->syncIterator();
	}

	/**
	 * @param	string	$color
	 * @return	void
	 */
	public function drawStar( $color )
	{
		$x = rand( 2, $this->getPixelWidth() - 2 );
		$y = rand( 2, $this->getPixelHeight() - 2 );

		$opacity = sprintf( '%02s', rand( 5, 10 ) );
		$starColor = $color . $opacity;

		$starSize = rand( 0, 1 );

		$this->drawAt( $x - $starSize, $y, $starColor );
		$this->drawAt( $x + $starSize, $y, $starColor );
		$this->drawAt( $x, $y - $starSize, $starColor );
		$this->drawAt( $x, $y + $starSize, $starColor );
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

		/*
		 * Sky
		 */
		$skyGradientColor = $palette->getBuildingColor();

		/* Stars */
		if( rand( 1, 3 ) != 1 )
		{
			$starCount = rand( 1, 4 );
			for( $s = 0; $s < $starCount; $s++ )
			{
				$this->drawStar( $skyGradientColor );
			}
		}

		/* Gradient */
		$this->drawSkyGradient( $skyGradientColor );

		/* Horizon */
		$horizonColor = $palette->getHorizonColor();
		$this->drawHorizon( $horizonColor );
		$this->image->drawImage( $this->draw );

		/*
		 * Distant Buildings
		 */
		$distantMargin = rand( 1, 2 ) / 10;
		$distantStartX = floor( self::IMAGE_WIDTH * $distantMargin );
		$distantStopX = floor( self::IMAGE_WIDTH * (1 - $distantMargin) );

		$previousXOffset = $distantStartX;
		do
		{
			/* Buildings */
			$building = new Building( $skyColor, 1 );
			$this->drawBuilding( $previousXOffset, $building );

			$previousXOffset = $building->getWidth() + $previousXOffset;
		}
		while( $previousXOffset < $distantStopX );

		/*
		 * Near Buildings
		 */
		$splitGroups = rand( 1, 5 ) == 5;

		if( !$splitGroups )
		{
			$nearMargin = rand( 2, 4 ) / 10;
	 		$nearStartX = floor( self::IMAGE_WIDTH * $nearMargin );
	 		$nearStopX = floor( self::IMAGE_WIDTH * (1 - $nearMargin) );

			$previousXOffset = $nearStartX;
			do
			{
				/* Buildings */
				$building = new Building( $horizonColor, 1 );
				$this->drawBuilding( $previousXOffset, $building );

				$gapWidth = rand( 0, 2 ) * self::PIXEL_SIZE;
				$previousXOffset = $building->getWidth() + $previousXOffset + $gapWidth;
			}
			while( $previousXOffset < $nearStopX );
		}
		else
		{
			$nearMargin = 1 / 10;

			/* Group 1 */
			$nearStartX = floor( self::IMAGE_WIDTH * $nearMargin );
			$nearStopX = floor( self::IMAGE_WIDTH * 0.33 * (1 - $nearMargin) );

			$previousXOffset = $nearStartX;
			do
			{
				/* Buildings */
				$building = new Building( $horizonColor, 1 );
				$this->drawBuilding( $previousXOffset, $building );

				$gapWidth = rand( 0, 2 ) * self::PIXEL_SIZE;
				$previousXOffset = $building->getWidth() + $previousXOffset + $gapWidth;
			}
			while( $previousXOffset < $nearStopX );

			/* Group 2 */
			$nearStartX = floor( self::IMAGE_WIDTH * 0.67 );
			$nearStopX = floor( self::IMAGE_WIDTH * (1 - $nearMargin) );

			$previousXOffset = $nearStartX;
			do
			{
				/* Buildings */
				$building = new Building( $horizonColor, 1 );
				$this->drawBuilding( $previousXOffset, $building );

				$gapWidth = rand( 0, 2 ) * self::PIXEL_SIZE;
				$previousXOffset = $building->getWidth() + $previousXOffset + $gapWidth;
			}
			while( $previousXOffset < $nearStopX );
		}

		/* Transparent Pixel */
		$this->drawTransparentPixel();

		$fileImage->putContents( $this->image->getImageBlob() );
	}

	/**
	 * @return	int
	 */
	public function getPixelHeight()
	{
		return self::IMAGE_HEIGHT / self::PIXEL_SIZE;
	}

	/**
	 * @return	int
	 */
	public function getPixelWidth()
	{
		return self::IMAGE_WIDTH / self::PIXEL_SIZE;
	}
}
