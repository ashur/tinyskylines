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
	protected $backgroundColor;

	/**
	 * @var	string
	 */
	protected $foregroundColor;

	/**
	 * @var	string
	 */
	protected $gradientColor;

	/**
	 * @var	Huxtable\Core\File\File
	 */
	protected $imageFile;

	/**
	 * @var	Skyline\Palette
	 */
	protected $palette;

	/**
	 * @var	array
	 */
	protected $stars = [];

	/**
	 * @param	Skylines\Palette	$palette
	 * @return	void
	 */
	public function __construct( Palette $palette )
	{
		$this->palette = $palette;

		$this->backgroundColor = $palette->getBackgroundColor();
		$this->foregroundColor = $palette->getForegroundColor();
		$this->gradientColor = $palette->getGradientColor();
	}

	/**
	 * @param	Skylines\Star	$star
	 */
	public function addStar( Star $star )
	{
		$this->stars[] = $star;
	}

	/**
	 * @param	Huxtable\Core\File\File		$imageFile
	 * @param	int		$cols
	 * @param	int		$rows
	 * @param	int		$pixelSize
	 */
	public function render( File\File $imageFile, $cols, $rows, $pixelSize )
	{
		$canvas = new Pixel\Canvas( $cols, $rows, $pixelSize );

		/* Horizon */
		$horizonRowEstimate = floor( $rows * 0.72);
		$gradientBandCount = floor( $horizonRowEstimate / self::GRADIENT_BAND_ROWS );
		$horizonRow = $gradientBandCount * self::GRADIENT_BAND_ROWS;

		$canvas->fillRectangle( 0, $horizonRow, $cols, $rows, $this->foregroundColor );

		/* Sky */
		$canvas->setBackgroundColor( $this->backgroundColor );

		/* Stars */
		foreach( $this->stars as $star )
		{
			$star->draw( $this->gradientColor, $canvas, $horizonRow );
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

			$canvas->fillRectangle( 0, $gradientRow, $cols, self::GRADIENT_BAND_ROWS, $gradientColor );

			$gradientOpacity = floor( $gradientOpacity - (($gradientOpacityMax - $gradientOpacityMin) / $gradientBandCount) );
		}

		$canvas->render( $imageFile );
	}
}
