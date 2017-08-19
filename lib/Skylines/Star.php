<?php

/*
 * This file is part of Skylines
 */
namespace Skylines;

use Huxtable\Core\Utils;
use Huxtable\Pixel;

class Star
{
	/**
	 * @var	int
	 */
	public $opacity;

	/**
	 * @var	int
	 */
	public $radius;

	/**
	 * @var	array
	 */
	protected $supportedRadii;

	/**
	 * @var	float
	 */
	public $xOffsetPercentage;

	/**
	 * @var	float
	 */
	public $yOffsetPercentage;

	/**
	 * @return	void
	 */
	public function __construct()
	{
		$this->supportedRadii = [0,0,1];
		$this->radius = Utils::randomElement( $this->supportedRadii );

		$this->xOffsetPercentage = rand( 5, 95 ) / 100;
		$this->yOffsetPercentage = rand( 5, 95 ) / 100;

		$this->opacity = rand( 90, 100 );
	}

	/**
	 * @param	string					$color
	 * @param	Huxtable\Pixel\Canvas	$canvas
	 * @param	int						$maxRow
	 */
	public function draw( $color, Pixel\Canvas &$canvas, $maxRow )
	{
		$offsetCol = floor( $canvas->getCols() * $this->xOffsetPercentage );
		$offsetRow = floor( $maxRow * $this->yOffsetPercentage );

		$opacityHex = dechex( $this->opacity );
		$starColor = "{$color}{$opacityHex}";

		$canvas->drawAt( $offsetCol - $this->radius, $offsetRow, $starColor );
		$canvas->drawAt( $offsetCol + $this->radius, $offsetRow, $starColor );
		$canvas->drawAt( $offsetCol, $offsetRow - $this->radius, $starColor );
		$canvas->drawAt( $offsetCol, $offsetRow + $this->radius, $starColor );
	}

	/**
	 * @param	int	$xOffsetPercentage
	 * @param	int	$yOffsetPercentage
	 */
	public function setOffsetPercentage( $xOffsetPercentage, $yOffsetPercentage )
	{
		$this->xOffsetPercentage = $xOffsetPercentage;
		$this->yOffsetPercentage = $yOffsetPercentage;
	}

	/**
	 * @param	int	$opacity
	 */
	public function setOpacity( $opacity )
	{
		$this->opacity = $opacity;
	}

	/**
	 * @param	int	$radius
	 */
	public function setRadius( $radius )
	{
		if( in_array( $radius, $this->supportedRadii ) )
		{
			$this->radius = $radius;
		}
	}
}
