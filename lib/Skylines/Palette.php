<?php

/*
 * This file is part of Skylines
 */
namespace Skylines;

use Huxtable\Core\Utils;

class Palette
{
	/**
	 * @var	string
	 */
	protected $backgroundColor;

	/**
	 * @var	type
	 */
	protected $colors=[];

	/**
	 * @var	string
	 */
	protected $foregroundColor;

	/**
	 * @var	string
	 */
	protected $gradientColor;

	/**
	 * @return	string
	 */
	public function __toString()
	{
		return implode( $this->colors );
	}

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
	public function getBackgroundColor()
	{
		if( !is_null( $this->backgroundColor ) )
		{
			return $this->backgroundColor;
		}

		shuffle( $this->colors );
		$this->backgroundColor = '#' . array_pop( $this->colors );
		return $this->backgroundColor;
	}

	/**
	 * @return	string
	 */
	public function getForegroundColor()
	{
		if( !is_null( $this->foregroundColor ) )
		{
			return $this->foregroundColor;
		}

		shuffle( $this->colors );
		$this->foregroundColor = '#' . array_pop( $this->colors );
		return $this->foregroundColor;
	}

	/**
	 * @return	string
	 */
	public function getGradientColor()
	{
		if( !is_null( $this->gradientColor ) )
		{
			return $this->gradientColor;
		}

		shuffle( $this->colors );
		$this->gradientColor = '#' . array_pop( $this->colors );
		return $this->gradientColor;
	}

	/**
	 * @param	string	$backgroundColor
	 */
	public function setBackgroundColor( $backgroundColor )
	{
		$this->backgroundColor = "#{$backgroundColor}";
	}

	/**
	 * @param	string	$foregroundColor
	 */
	public function setForegroundColor( $foregroundColor )
	{
		$this->foregroundColor = "#{$foregroundColor}";
	}

	/**
	 * @param	string	$gradientColor
	 */
	public function setGradientColor( $gradientColor )
	{
		$this->gradientColor = "#{$gradientColor}";
	}
}
