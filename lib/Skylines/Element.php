<?php

/*
 * This file is part of Skylines
 */
namespace Skylines;

use Huxtable\Pixel;

abstract class Element
{
	/**
	 * Height of element in rows
	 *
	 * @var	int
	 */
	public $height;

	/**
	 * Left margin in cols
	 *
	 * @var	int
	 */
	public $leftMargin;

	/**
	 * Width of element in cols
	 *
	 * @var	int
	 */
	public $width;

	/**
	 * @param	string					$color
	 * @param	int						$offsetCol
	 * @param	Huxtable\Pixel\Canvas	$canvas
	 */
	abstract public function draw( $color, $offsetCol, Pixel\Canvas &$canvas );

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
	public function getLeftMargin()
	{
		return $this->leftMargin;
	}

	/**
	 * @return	int
	 */
	public function getWidth()
	{
		return $this->width;
	}

	/**
	 * @param	int	$height
	 */
	public function setHeight( $height )
	{
		$this->height = $height;
	}

	/**
	 * @param	int	$leftMargin
	 */
	public function setLeftMargin( $leftMargin )
	{
		$this->leftMargin = $leftMargin;
	}

	/**
	 * @param	int	$width
	 */
	public function setWidth( $width )
	{
		$this->width = $width;
	}
}
