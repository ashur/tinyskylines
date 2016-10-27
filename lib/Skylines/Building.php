<?php

/*
 * This file is part of Skylines
 */
namespace Skylines;

use Huxtable\Core\Utils;
use Huxtable\Pixel;

class Building extends Element
{
	/**
	 * @var	int
	 */
	public $antennaHeight = 0;

	/**
	 * @var	int
	 */
	public $gableOffset;

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
	public $roofType;

	/**
	 * @var	string
	 */
	public $type = 'building';

	/**
	 * @var	int
	 */
	public $windowsCount = 0;

	/**
	 *
	 */
	public function __construct()
	{
		// $this->height = rand( 8, 23 );
		// $this->width = rand( 7, 15 );
		$this->height = rand( 8, 21 );
		$this->width = rand( 7, 13 );

		/* Left Margin */
		$leftMargins = [0, 0, 0, 1, 2 ];
		$this->leftMargin = Utils::randomElement( $leftMargins );

		/*
		 * Roof
		 */
		$randRoof = rand( 1, 20 );

		if( in_array( $randRoof, range( 1, 15 ) ) )
		{
			$this->roofType = 'flat';
		}
		if( in_array( $randRoof, range( 16, 17 ) ) )
		{
			$this->roofType = 'floating';
		}
		if( in_array( $randRoof, range( 18, 18 ) ) )
		{
			$this->roofType = 'gable';
		}
		if( in_array( $randRoof, range( 19, 20 ) ) )
		{
			$this->roofType = 'shed';
		}

		if( $this->roofType == 'gable' )
		{
			if( $this->width %2 == 1 )
			{
				$this->width = $this->width - 1;
			}

			$this->gableOffset = rand( 0, 3 );
		}

		if( $this->roofType == 'shed' )
		{
			if( $this->height < ($this->width * 2) )
			{
				$this->height = $this->width * 2;
			}

			$this->roofSlope = rand( 2, 5 );
			$this->roofSlopeDirection = rand( 0, 1 ) == 1 ? 'down' : 'up';
		}

		/*
		 * Antenna
		 */
		if( $this->width % 2 == 1 && $this->roofType == 'flat' )
		{
			if( rand( 1, 3 ) == 3 )
			{
				$this->antennaHeight = floor( $this->width / 2 );
			}
		}

		/* Windows */
		$windowsRoofTypes = ['flat', 'shed'];
		if( in_array( $this->roofType, $windowsRoofTypes ) )
		{
			$windowCounts = [0, 1, 1, 2, 2];
			$this->windowsCount = Utils::randomElement( $windowCounts );
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

		/* Flat */
		if( $this->roofType == 'flat' )
		{
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
		}

		/* Floating */
		if( $this->roofType == 'floating' )
		{
			$buildingCanvas->fillRectangle( 0, 0, $this->width, 2, $color );
			$buildingCanvas->fillRectangle( 0, 3, $this->width, $this->getHeight(), $color );
		}

		/* Antenna */
		if( $this->roofType == 'flat')
		{
			$centerCol = floor( $this->width / 2 );

			for( $a = 0; $a < $this->antennaHeight; $a++ )
			{
				if( $a == 1 )
				{
					continue;
				}

				$buildingCanvas->drawAt( $centerCol, $a, $color );
			}
		}

		/* Gable */
		if( $this->roofType == 'gable' )
		{
			$gableHeight = floor( $this->width / 2 ) - 1;

			for( $col = 1; $col <= $gableHeight; $col++ )
			{
				for( $row = ($gableHeight - $col); $row < $gableHeight; $row++ )
				{
					$buildingCanvas->drawWithReflectionAt( $col, $row, $color );
				}
			}

			$buildingCanvas->fillRectangle( 0, $gableHeight - $this->gableOffset, $this->width, $this->height, $color );
		}

		/* Shed */
		if( $this->roofType == 'shed' )
		{
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
	public function getInstanceFromData( array $data )
	{
		$building = new self();

		$building->antennaHeight = $data['antennaHeight'];
		$building->gableOffset = $data['gableOffset'];
		$building->roofSlope = $data['roofSlope'];
		$building->roofSlopeDirection = $data['roofSlopeDirection'];
		$building->roofType = $data['roofType'];
		$building->windowsCount = $data['windowsCount'];

		$building->height = $data['height'];
		$building->leftMargin = $data['leftMargin'];
		$building->width = $data['width'];

		return $building;
	}

	/**
	 * @param	int	$windowsCount
	 */
	public function setWindowsCount( $windowsCount )
	{
		$this->windowsCount = $windowsCount;
	}
}
