<?php

/*
 * This file is part of Skylines
 */
namespace Skylines;

use Huxtable\Core\Utils;

class Palette
{
	/**
	 * @var	type
	 */
	protected $colors=[];

	/**
	 * @var	string
	 */
	protected $horizonColor;

	/**
	 * @var	string
	 */
	protected $skyColor;

	/**
	 * @param	string	$color
	 * @return	void
	 */
	public function addColor( $color )
	{
		$this->colors[] = $color;
	}

	/**
	 * @return	string
	 */
	public function getBuildingColor()
	{
		shuffle( $this->colors );
		$this->buildingColor = '#' . array_pop( $this->colors );
		return $this->buildingColor;
	}

	/**
	 * @return	string
	 */
	public function getHorizonColor()
	{
		if( !is_null( $this->horizonColor ) )
		{
			return $this->horizonColor;
		}

		shuffle( $this->colors );
		$this->horizonColor = '#' . array_pop( $this->colors );
		return $this->horizonColor;
	}

	/**
	 * @return	string
	 */
	public function getSkyColor()
	{
		if( !is_null( $this->skyColor ) )
		{
			return $this->skyColor;
		}

		shuffle( $this->colors );
		$this->skyColor = '#' . array_pop( $this->colors );
		return $this->skyColor;
	}
}
