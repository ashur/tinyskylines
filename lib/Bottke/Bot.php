<?php

/*
 * This file is part of Bottke
 */
namespace Bottke;

use Huxtable\Bot\History;
use Huxtable\Core\Config;
use Huxtable\Core\File;
use Huxtable\Core\Utils;

class Bot extends \Huxtable\Bot\Bot
{
	const HISTORY_HASH_DOMAIN = 'hashes';

	/**
	 * @var	array
	 */
	protected $boardTiles=[];

	/**
	 * @var	array
	 */
	protected $colors=[];

	/**
	 * @var	array
	 */
	protected $tiles=[];

	/**
	 * @param	string					$name			Bot name
	 * @param	Huxtable\Bot\History	$history
	 * @param	Huxtable\Core\Config	$config
	 * @return	void
	 */
	public function __construct( $name, File\Directory $dirData )
	{
		parent::__construct( $name, $dirData );

		$this->colors['a'] = '19d4ff';
		$this->colors['b'] = '53dfff';
		$this->colors['c'] = '8ce9ff';
		$this->colors['d'] = 'c5f4ff';
		$this->colors['e'] = 'ffffff';

		$this->tiles =
		[
			0	=> 'a',
			1	=> 'a',
			2	=> 'a',
			3	=> 'a',
			4	=> 'a',

			5	=> 'a',
			6	=> 'b',
			7	=> 'b',
			8	=> 'b',
			9	=> 'b',

			10	=> 'a',
			11	=> 'b',
			12	=> 'c',
			13	=> 'c',
			14	=> 'c',

			15	=> 'a',
			16	=> 'b',
			17	=> 'c',
			18	=> 'd',
			19	=> 'd',

			20	=> 'a',
			21	=> 'b',
			22	=> 'c',
			23	=> 'd',
			24	=> 'e',
		];
	}

	/**
	 * @return	string
	 */
	protected function getHash()
	{
		$colorIds = array_values( $this->boardTiles );
		$hashString = implode( '', $colorIds );

		return $hashString;
	}

	/**
	 * @return	void
	 */
	public function generateBoard()
	{
		do
		{
			$this->boardTiles = $this->tiles;
			shuffle( $this->boardTiles );

			$hash = $this->getHash();
		}
		while( $this->history->domainEntryExists( self::HISTORY_HASH_DOMAIN, $hash ) );

		$this->history->addDomainEntry( self::HISTORY_HASH_DOMAIN, $hash );
	}

	/**
	 * Randomize tiles and return an array of colors
	 *
	 * @return	void
	 */
	public function generateBoardImage( File\File $fileImage )
	{
		$this->generateBoard();

		/*
		 * Setup
		 */
		$image = new \Imagick();
		$image->newImage( 750, 750, '#ffffff' );
		$image->setImageFormat( 'png' );

		$draw = new \ImagickDraw();

		/*
		 * Draw each tile
		 */
		$boardColors = $this->getBoardColors();

		$tileHeight = 150;
		$tileWidth = $tileHeight;

		$col = 0;
		$row = 0;

		for( $i = 0; $i < count( $boardColors ); $i++ )
		{
			$fillColor = $boardColors[$i];

			$x = $col * $tileWidth;
			$y = $row * $tileHeight;

			$draw->setFillColor( "#{$fillColor}" );
			$draw->rectangle( $x, $y, $x + $tileWidth, $y + $tileHeight );

			$col++;

			if( $i % 5 == 4 )
			{
				$col = 0;
				$row++;
			}
		}

		/* Draw tiles */
		$image->drawImage( $draw );

		/*
		 * Set transparent pixel to fight Twitter compression
		 */
		$iterator = $image->getPixelIterator();
		$iterator->setIteratorRow( 0 );

		/* Get pixels in row */
		$row = $iterator->getCurrentIteratorRow();

		$pixel = $row[0];
		$pixel->setColor( '#ffffff00');

		/* Sync data back to image */
		$iterator->syncIterator();

		/*
		 * Done
		 */
		$fileImage->putContents( $image->getImageBlob() );
	}

	/**
	 * @return	array
	 */
	public function getBoardColors()
	{
		$boardColors =[];

		foreach( $this->boardTiles as $boardTile )
		{
			$boardColors[] = $this->colors[$boardTile];
		}

		return $boardColors;
	}
}
