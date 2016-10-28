<?php

/*
 * This file is part of Skylines
 */
namespace Skylines\Element;

use Huxtable\Core\Utils;
use Huxtable\Pixel;

class BuildingFloating extends Building
{
	/**
	 * @var	string
	 */
	public $type = 'floating';

	/**
	 * @param	string					$color
	 * @param	int						$offsetCol
	 * @param	Huxtable\Pixel\Canvas	$canvas
	 */
	public function draw( $color, $offsetCol, Pixel\Canvas &$canvas )
	{
		$offsetRow = $canvas->getRows() - $this->getHeight();

		$buildingCanvas = new Pixel\Canvas( $this->width, $this->getHeight(), $canvas->getPixelSize() );

		$buildingCanvas->fillRectangle( 0, 0, $this->width, 2, $color );
		$buildingCanvas->fillRectangle( 0, 3, $this->width, $this->getHeight(), $color );

		$canvas->compositeCanvas( $buildingCanvas, $offsetCol, $offsetRow );
	}

	/**
	 * @param	array	$data
	 * @return	Skylines\Element\BuildingFloating
	 */
	public function getInstanceFromData( array $data )
	{
		$building = new self();

		$building->height = $data['height'];
		$building->leftMargin = $data['leftMargin'];
		$building->width = $data['width'];

		return $building;
	}
}
