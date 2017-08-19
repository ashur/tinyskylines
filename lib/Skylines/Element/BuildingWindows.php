<?php

/*
 * This file is part of Skylines
 */
namespace Skylines\Element;

use Huxtable\Core\Utils;
use Huxtable\Pixel;

class BuildingWindows extends Building
{
	/**
	 * @var	string
	 */
	public $type = 'windows';

	/**
	 *
	 */
	public function __construct()
	{
		parent::__construct();

		if( $this->width % 2 == 0 )
		{
			$this->width++;
		}
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

		/* Windows */
		for( $row = 0; $row < $this->height; $row++ )
		{
			if( $row % 2 == 0 )
			{
				$buildingCanvas->fillRectangle( 0, $row, $this->width, 1, $color );
			}
			else
			{
				for( $col = 0; $col < $this->width; $col = $col + 2 )
				{
					$buildingCanvas->drawAt( $col, $row, $color );
				}
			}
		}

		$canvas->compositeCanvas( $buildingCanvas, $offsetCol, $offsetRow );
	}

	/**
	 * @param	array	$data
	 * @return	Skylines\Building
	 */
	static public function getInstanceFromData( array $data )
	{
		$building = new self();

		$building->height = $data['height'];
		$building->leftMargin = $data['leftMargin'];
		$building->width = $data['width'];

		return $building;
	}
}
