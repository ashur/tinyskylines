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
	/**
	 * @var	array
	 */
	protected $board=[];

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

		$this->colors[0] = '19d4ff';
		$this->colors[1] = '53dfff';
		$this->colors[2] = '8ce9ff';
		$this->colors[3] = 'c5f4ff';
		$this->colors[4] = 'ffffff';

		$this->tiles =
		[
			0	=> 0,
			1	=> 0,
			2	=> 0,
			3	=> 0,
			4	=> 0,

			5	=> 0,
			6	=> 1,
			7	=> 1,
			8	=> 1,
			9	=> 1,

			10	=> 0,
			11	=> 1,
			12	=> 2,
			13	=> 2,
			14	=> 2,

			15	=> 0,
			16	=> 1,
			17	=> 2,
			18	=> 3,
			19	=> 3,

			20	=> 0,
			21	=> 1,
			22	=> 2,
			23	=> 3,
			24	=> 4
		];

		$this->board = $this->tiles;
		shuffle( $this->board );
	}

	/**
	 * @param
	 * @return	void
	 */
	public function getBoard()
	{
		$boardColors = [];
		foreach( $this->board as $tile )
		{
			$boardColors[] = $this->colors[$tile];
		}

		return $boardColors;
	}

	/**
	 * @return	string
	 */
	public function getBoardHash()
	{
		$colorIds = array_values( $this->board );
		$hashString = implode( '', $colorIds );
		echo $hashString . PHP_EOL;
	}
}
