<?php

/*
 * This file is part of Skylines
 */
namespace Skylines\Element;

use Huxtable\Core\Utils;
use Huxtable\Pixel;

class Building19 extends BuildingWindows
{
	/**
	 * @var	array
	 */
	public $lightedWindows = [];

	/**
	 * @var	string
	 */
	public $type = '19';

	/**
	 *
	 */
	public function __construct( $width, $height, $leftMargin )
	{
		parent::__construct();

		$this->height = $height;
		$this->width = $width;
		$this->leftMargin = $leftMargin;
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
				// Draw divider
				$buildingCanvas->fillRectangle( 0, $row, $this->width, 1, $color );
			}
			else
			{
				// Draw dividers
				for( $col = 0; $col < $this->width; $col = $col + 2 )
				{
					$buildingCanvas->drawAt( $col, $row, $color );
				}

				for( $col = 1; $col < $this->width; $col = $col + 2 )
				{
					if( isset( $this->lightedWindows[(($col - 1) / 2)][(($row - 1) / 2)] ) )
					{
						$buildingCanvas->drawAt( $col, $row, $this->windowColor );
					}
					else
					{
						$buildingCanvas->drawAt( $col, $row, "{$color}80" );
					}
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

	/**
	 * @param	int		$col
	 * @param	int		$row
	 * @param	string	$windowColor
	 */
	public function turnOnWindowLight( $col, $row, $windowColor )
	{
		$this->lightedWindows[$col][$row] = true;
		$this->windowColor = $windowColor;
	}
}
