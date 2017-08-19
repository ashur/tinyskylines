<?php

/*
 * This file is part of Skylines
 */
namespace Skylines\Element;

use Huxtable\Core\Utils;
use Huxtable\Pixel;

class BuildingShed extends Building
{
	/**
	 * @var	int
	 */
	public $roofSlope;

	/**
	 * @var	string
	 */
	public $roofSlopeDirection;

	/**
	 * @var	string
	 */
	public $type = 'shed';

	/**
	 * Randomly generate building dimensions and margin
	 */
	public function __construct()
	{
		parent::__construct();

		if( $this->height < ($this->width * 2) )
		{
			$this->height = $this->width * 2;
		}

		$this->roofSlope = rand( 2, 5 );
		$this->roofSlopeDirection = rand( 0, 1 ) == 1 ? 'down' : 'up';
	}

	/**
	 * @param	string					$color
	 * @param	int						$offsetCol
	 * @param	Huxtable\Pixel\Canvas	$canvas
	 */
	public function draw( $color, $offsetCol, Pixel\Canvas &$canvas )
	{
		$offsetRow = $canvas->getRows() - $this->getHeight();

		$buildingCanvas = new Pixel\Canvas( $this->width, $this->getHeight(), $canvas->getPixelSize() );

		$roofRows = ($this->width / 2) + 1;
		$roofLineWidth = 1;

		for( $row = 0; $row < $roofRows; $row++ )
		{
			if( $this->roofSlopeDirection == 'down' )
			{
				$buildingCanvas->fillRectangle( 0, $row, $roofLineWidth, 1, $color );
			}
			if( $this->roofSlopeDirection == 'up' )
			{
				$buildingCanvas->fillRectangle( $this->width - $roofLineWidth, $row, $roofLineWidth, 1, $color );
			}

			$roofLineWidth = $roofLineWidth + $this->roofSlope;
		}

		$buildingCanvas->fillRectangle( 0, $roofRows, $this->width, $this->height, $color );

		$canvas->compositeCanvas( $buildingCanvas, $offsetCol, $offsetRow );
	}

	/**
	 * @param	array	$data
	 * @return	Skylines\Building
	 */
	static public function getInstanceFromData( array $data )
	{
		$building = new self();

		$building->roofSlope = $data['roofSlope'];
		$building->roofSlopeDirection = $data['roofSlopeDirection'];

		$building->height = $data['height'];
		$building->leftMargin = $data['leftMargin'];
		$building->width = $data['width'];

		return $building;
	}
}
