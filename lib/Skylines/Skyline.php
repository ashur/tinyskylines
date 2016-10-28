<?php

/*
 * This file is part of Skylines
 */
namespace Skylines;

use Huxtable\Core\File;
use Huxtable\Pixel;

class Skyline
{
	const GRADIENT_BAND_ROWS = 6;

	/**
	 * @var	string
	 */
	public $backgroundColor;

	/**
	 * @var	array
	 */
	public $backgroundElements = [];

	/**
	 * @var	string
	 */
	public $foregroundColor;

	/**
	 * @var	array
	 */
	public $foregroundElements = [];

	/**
	 * @var	string
	 */
	public $gradientColor;

	/**
	 * @var	Huxtable\Core\File\File
	 */
	protected $imageFile;

	/**
	 * @var	array
	 */
	public $stars = [];

	/**
	 * @param	Skylines\Palette	$palette
	 * @return	void
	 */
	public function __construct( Palette $palette = null )
	{
		if( !is_null( $palette ) )
		{
			$this->backgroundColor = $palette->getBackgroundColor();
			$this->foregroundColor = $palette->getForegroundColor();
			$this->gradientColor = $palette->getGradientColor();
		}
	}

	/**
	 * @param	Skylines\Building	$element
	 * @param	boolean				$preScaled
	 */
	public function addBackgroundElement( Element $element, $preScaled = false )
	{
		/* Scale element up */
		if( !$preScaled )
		{
			$elementWidth = $element->getWidth();// + rand( 5, 8 );
			$elementHeight = $element->getHeight();// + rand( 5, 8 );

			$element->setWidth( $elementWidth );
			$element->setHeight( $elementHeight );
		}

		/* Remove windows */
		$element->setWindowsCount( 0 );

		/* Left margin */
		if( count( $this->backgroundElements ) == 0 )
		{
			$element->setLeftMargin( 0 );
		}

		$this->backgroundElements[] = $element;
	}

	/**
	 * @param	Skylines\Element	$element
	 */
	public function addForegroundElement( Element $element )
	{
		$this->foregroundElements[] = $element;
	}

	/**
	 * @param	Skylines\Star	$star
	 */
	public function addStar( Star $star )
	{
		$this->stars[] = $star;
	}

	/**
	 * @param	array	$data
	 * @return	self
	 */
	public function getInstanceFromData( array $data )
	{
		$skyline = new self();
		$skylineData = $data['skyline'];

		/* Colors */
		$skyline->setBackgroundColor( $skylineData['backgroundColor'] );
		$skyline->setForegroundColor( $skylineData['foregroundColor'] );
		$skyline->setGradientColor( $skylineData['gradientColor'] );

		/* Stars */
		foreach( $skylineData['stars'] as $starData )
		{
			$star = new Star();
			$star->setOpacity( $starData['opacity'] );
			$star->setRadius( $starData['radius'] );
			$star->setOffsetPercentage( $starData['xOffsetPercentage'], $starData['yOffsetPercentage'] );

			$skyline->addStar( $star );
		}

		/* Elements */
		foreach( $skylineData['backgroundElements'] as $backgroundElementData )
		{
			switch( $backgroundElementData['type'] )
			{
				case 'building';
					$backgroundElement = Building::getInstanceFromData( $backgroundElementData );
					break;
			}

			$skyline->addBackgroundElement( $backgroundElement, true );
		}

		foreach( $skylineData['foregroundElements'] as $foregroundElementData )
		{
			switch( $foregroundElementData['type'] )
			{
				case 'building';
					$foregroundElement = Building::getInstanceFromData( $foregroundElementData );
					break;
			}

			$skyline->addForegroundElement( $foregroundElement, true );
		}

		return $skyline;
	}

	/**
	 * @param	Huxtable\Core\File\File		$imageFile
	 * @param	int		$cols
	 * @param	int		$rows
	 * @param	int		$pixelSize
	 */
	public function render( File\File $imageFile, $cols, $rows, $pixelSize )
	{
		$skylineCanvas = new Pixel\Canvas( $cols, $rows, $pixelSize );

		/* Horizon */
		$horizonRowEstimate = floor( $rows * 0.8 );
		$gradientBandCount = floor( $horizonRowEstimate / self::GRADIENT_BAND_ROWS );
		$horizonRow = $gradientBandCount * self::GRADIENT_BAND_ROWS;

		$skylineCanvas->fillRectangle( 0, $horizonRow, $cols, $rows, $this->foregroundColor );

		/* Sky */
		$skylineCanvas->setBackgroundColor( $this->backgroundColor );

		/* Stars */
		foreach( $this->stars as $star )
		{
			$star->draw( $this->gradientColor, $skylineCanvas, $horizonRow );
		}

		/* Gradient */
		$gradientOpacityMax = 80;
		$gradientOpacityMin = 20;
		$gradientOpacity = $gradientOpacityMax;

		for( $g = 0; $g < $gradientBandCount; $g++ )
		{
			$gradientRow = $g * self::GRADIENT_BAND_ROWS;
			$gradientOpacityHex = dechex( $gradientOpacity );
			$gradientColor = "{$this->gradientColor}{$gradientOpacityHex}";

			$skylineCanvas->fillRectangle( 0, $gradientRow, $cols, self::GRADIENT_BAND_ROWS, $gradientColor );

			$gradientOpacity = floor( $gradientOpacity - (($gradientOpacityMax - $gradientOpacityMin) / $gradientBandCount) );
		}

		/*
		 * Background Elements
		 */
		$backgroundCols = 0;
		$backgroundRows = 0;

		/* Calculate canvas size */
		foreach( $this->backgroundElements as $backgroundElement )
		{
			$backgroundCols = $backgroundCols + $backgroundElement->getWidth() + $backgroundElement->getLeftMargin();
			if( $backgroundElement->getHeight() > $backgroundRows )
			{
				$backgroundRows = $backgroundElement->getHeight();
			}
		}

		$backgroundCanvas = new Pixel\Canvas( $backgroundCols, $backgroundRows, $pixelSize );

		$backgroundOffsetCol = 0;
		foreach( $this->backgroundElements as $backgroundElement )
		{
			$backgroundOffsetCol = $backgroundOffsetCol + $backgroundElement->getLeftMargin();
			$backgroundElement->draw( $this->backgroundColor, $backgroundOffsetCol, $backgroundCanvas );

			$backgroundOffsetCol = $backgroundOffsetCol + $backgroundElement->getWidth();
		}

		$backgroundCanvasOffsetCol = floor( ($cols - $backgroundCols) / 2 );
		$backgroundCanvasOffsetRow = ($horizonRow - $backgroundRows);

		$skylineCanvas->compositeCanvas( $backgroundCanvas, $backgroundCanvasOffsetCol, $backgroundCanvasOffsetRow );

		/*
		 * Foreground Elements
		 */
		$foregroundCols = 0;
		$foregroundRows = 0;

		/* Calculate canvas size */
		foreach( $this->foregroundElements as $foregroundElement )
		{
			$foregroundCols = $foregroundCols + $foregroundElement->getWidth() + $foregroundElement->getLeftMargin();
			if( $foregroundElement->getHeight() > $foregroundRows )
			{
				$foregroundRows = $foregroundElement->getHeight();
			}
		}

		$foregroundCanvas = new Pixel\Canvas( $foregroundCols, $foregroundRows, $pixelSize );

		$foregroundOffsetCol = 0;
		foreach( $this->foregroundElements as $foregroundElement )
		{
			$foregroundOffsetCol = $foregroundOffsetCol + $foregroundElement->getLeftMargin();
			$foregroundElement->draw( $this->foregroundColor, $foregroundOffsetCol, $foregroundCanvas );

			$foregroundOffsetCol = $foregroundOffsetCol + $foregroundElement->getWidth();
		}

		$foregroundCanvasOffsetCol = floor( ($cols - $foregroundCols) / 2 );
		$foregroundCanvasOffsetRow = ($horizonRow - $foregroundRows);

		$skylineCanvas->compositeCanvas( $foregroundCanvas, $foregroundCanvasOffsetCol, $foregroundCanvasOffsetRow );
		$skylineCanvas->render( $imageFile );
	}

	/**
	 * @param	string	$backgroundColor
	 */
	public function setBackgroundColor( $backgroundColor )
	{
		$this->backgroundColor = $backgroundColor;
	}

	/**
	 * @param	string	$foregroundColor
	 */
	public function setForegroundColor( $foregroundColor )
	{
		$this->foregroundColor = $foregroundColor;
	}

	/**
	 * @param	string	$gradientColor
	 */
	public function setGradientColor( $gradientColor )
	{
		$this->gradientColor = $gradientColor;
	}
}