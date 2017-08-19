<?php

/*
 * This file is part of Skylines
 */
namespace Skylines\Element;

use Huxtable\Core\Utils;
use Huxtable\Pixel;

class BuildingFlat extends Building
{
	/**
	 * @var	int
	 */
	public $antennaHeight = 0;

	/**
	 * @var	string
	 */
	public $type = 'flat';

	/**
	 * @var	int
	 */
	public $windowsCount = 0;

	/**
	 * Randomly generate building dimensions and margin
	 */
	public function __construct()
	{
		parent::__construct();

		/*
		 * Antenna
		 */
		if( $this->width % 2 == 1 )
		{
			if( rand( 1, 3 ) == 3 )
			{
				$this->antennaHeight = floor( $this->width / 2 );
			}
		}

		/* Windows */
		$windowCounts = [0, 1, 1, 2, 2, 2];
		$this->windowsCount = Utils::randomElement( $windowCounts );
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

		$buildingCanvas->fillRectangle( 0, $this->antennaHeight, $this->width, 1, $color );
		$buildingCanvas->fillRectangle( 0, $this->antennaHeight + $this->windowsCount + 1, $this->width, $this->height, $color );

		/* Windows */
		for( $w = 1; $w <= $this->windowsCount; $w++ )
		{
			for( $col = 0; $col < $this->width; $col++ )
			{
				if( $col != $w )
				{
					$buildingCanvas->drawAt( $col, $this->antennaHeight + $w, $color );
				}
			}
		}

		/* Antenna */
		$centerCol = floor( $this->width / 2 );

		for( $a = 0; $a < $this->antennaHeight; $a++ )
		{
			if( $a == 1 )
			{
				continue;
			}

			$buildingCanvas->drawAt( $centerCol, $a, $color );
		}

		$canvas->compositeCanvas( $buildingCanvas, $offsetCol, $offsetRow );
	}

	/**
	 * @return	int
	 */
	public function getHeight()
	{
		return $this->height + $this->antennaHeight;
	}

	/**
	 * @param	array	$data
	 * @return	Skylines\Building
	 */
	static public function getInstanceFromData( array $data )
	{
		$building = new self();

		$building->antennaHeight = $data['antennaHeight'];
		$building->windowsCount = $data['windowsCount'];

		$building->height = $data['height'];
		$building->leftMargin = $data['leftMargin'];
		$building->width = $data['width'];

		return $building;
	}
}
