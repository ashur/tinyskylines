<?php

/*
 * This file is part of Skylines
 */
namespace Skylines\Element;

use Huxtable\Core\Utils;
use Huxtable\Pixel;

class BuildingGable extends Building
{
	/**
	 * @var	int
	 */
	public $gableOffset;

	/**
	 * @var	string
	 */
	public $type = 'gable';

	/**
	 * Randomly generate building dimensions and margin
	 */
	public function __construct()
	{
		parent::__construct();

		if( $this->width %2 == 1 )
		{
			$this->width = $this->width - 1;
		}

		$minOffset = 1;
		$maxOffset = ($this->width / 2) - 2;

		$this->gableOffset = rand( $minOffset, $maxOffset );
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

		$gableHeight = floor( $this->width / 2 ) - 1;

		for( $col = 1; $col <= $gableHeight; $col++ )
		{
			for( $row = ($gableHeight - $col); $row < $gableHeight; $row++ )
			{
				$buildingCanvas->drawWithReflectionAt( $col, $row, $color );
			}
		}

		$buildingCanvas->fillRectangle( 0, $gableHeight - $this->gableOffset, $this->width, $this->height, $color );

		$canvas->compositeCanvas( $buildingCanvas, $offsetCol, $offsetRow );
	}

	/**
	 * @param	array	$data
	 * @return	Skylines\Building
	 */
	static public function getInstanceFromData( array $data )
	{
		$building = new self();

		$building->gableOffset = $data['gableOffset'];

		$building->height = $data['height'];
		$building->leftMargin = $data['leftMargin'];
		$building->width = $data['width'];

		return $building;
	}
}
