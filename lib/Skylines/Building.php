<?php

/*
 * This file is part of Skylines
 */
namespace Skylines;

use Imagick;

class Building
{
	/**
	 * @var	string
	 */
	protected $color;

	/**
	 * @var	int
	 */
	protected $height;

	/**
	 * @var	int
	 */
	protected $width;

	/**
	 * @param
	 * @return	void
	 */
	public function __construct( $color, $coefficient )
	{
		$this->color = $color;

		$this->height = (rand( 5, 18 ) * Bot::PIXEL_SIZE) * $coefficient;
		$this->width = (rand( 4, 9 ) * Bot::PIXEL_SIZE) * $coefficient;
	}

	/**
	 * @param	string	$color
	 * @return	Imagick
	 */
	public function draw()
	{
		$this->image = new Imagick();
		$this->image->newImage( $this->width, $this->height, '#ffffff00' );

		$pixelRows = floor( $this->height / Bot::PIXEL_SIZE );
		$pixelCols = floor( $this->width / Bot::PIXEL_SIZE );

		if( $pixelCols < 3 )
		{
			return $this->image;
		}

		/* Fill */
		for( $pixelRow = 0; $pixelRow < $pixelRows; $pixelRow++ )
		{
			for( $pixelCol = 0; $pixelCol < $pixelCols; $pixelCol++ )
			{
				$this->drawAt( $pixelCol, $pixelRow, $this->color );
			}
		}

		$pixelWidth = $this->getPixelWidth();
		$pixelHeight = $this->getPixelHeight();

		if( $pixelWidth % 2 == 1 && $pixelWidth <= 9 && $pixelHeight >= 8 )
		{
			/* Antenna */
			if( $pixelHeight / $pixelWidth >= 2  && rand( 1, 3 ) == 1 )
			{
				/* Median Column */
				$colMedian = floor( $pixelWidth / 2 );
				$antennaFloors = floor( $pixelHeight / rand( 3, 4 ) );

				for( $row = 0; $row < $antennaFloors; $row++ )
				{
					for( $col = 0; $col < $pixelWidth; $col++ )
					{
						if( $col != $colMedian )
						{
							$this->eraseAt( $col, $row );
						}
					}
				}

				$this->eraseAt( $colMedian, 1 );
			}
		}

		if( $pixelWidth % 2 == 1 && $pixelWidth >= 7 )
		{
			$colMedian = floor( $pixelWidth / 2 );
			$delta = rand( 1, 2 );

			/* KOIN tower */
			if( rand( 1, 3 ) == 1 )
			{
				$koinFloors = floor( $pixelHeight / 3 );

				$width = 0;
				for( $row = 0; $row < $koinFloors; $row++ )
				{
					for( $col = 0; $col < $pixelWidth; $col++ )
					{
						if( $col <= ($colMedian - $width) || $col >= ($colMedian + $width) )
						{
							$this->eraseAt( $col, $row );
						}
					}

					$width = $width + $delta;
				}
			}
		}

		/* Erase Windows */
		if( $pixelWidth > 4 )
		{
			if( rand( 1, 2 ) == 1 )
			{
				$this->eraseAt( 1, 1 );

				if( rand( 1, 2 ) == 1 )
				{
					$this->eraseAt( 2, 2 );
				}
			}
		}

		return $this->image;
	}

	/**
	 * @param	int		$x
	 * @param	int		$y
	 * @param	string	$color
	 * @return	void
	 */
	public function drawAt( $x, $y, $color )
	{
		$x1 = $x * Bot::PIXEL_SIZE;
		$x2 = $x1 + Bot::PIXEL_SIZE;

		$y1 = $y * Bot::PIXEL_SIZE;
		$y2 = $y1 + Bot::PIXEL_SIZE;

		$pixelIterator = $this->image->getPixelIterator();

		for( $row = $y1; $row < $y2; $row++ )
		{
			$pixelIterator->setIteratorRow( $row );
			$pixels = $pixelIterator->getCurrentIteratorRow();

			for( $col = $x1; $col < $x2; $col++ )
			{
				if( isset( $pixels[$col] ) )
				{
					$pixel = $pixels[$col];
					$pixel->setColor( $color );

					$pixelIterator->syncIterator();
				}
			}
		}
	}

	/**
	 * @param
	 * @return	void
	 */
	public function eraseAt( $x, $y )
	{
		$this->drawAt( $x, $y, '#ffffff00' );
	}

	/**
	 * @param	string	$color
	 */
	public function getColor()
	{
		return $this->color;
	}

	/**
	 * @return	int
	 */
	public function getHeight()
	{
		return $this->height;
	}

	/**
	 * @return	int
	 */
	public function getPixelHeight()
	{
		return $this->height / Bot::PIXEL_SIZE;
	}

	/**
	 * @return	int
	 */
	public function getPixelWidth()
	{
		return $this->width / Bot::PIXEL_SIZE;
	}

	/**
	 * @return	int
	 */
	public function getWidth()
	{
		return $this->width;
	}

	/**
	 * @param	int		$width
	 */
	public function setWidth( $width )
	{
		$this->width = $width;
	}
}
