<?php

/*
 * This file is part of Skylines
 */
namespace Skylines\Element;

use Huxtable\Core\Utils;

abstract class Building extends Element
{
	/**
	 * Randomly generate building dimensions and margin
	 */
	public function __construct()
	{
		$this->height = rand( 8, 21 );
		$this->width = rand( 7, 13 );

		/* Left Margin */
		$leftMargins = [0, 0, 0, 1, 2 ];
		$this->leftMargin = Utils::randomElement( $leftMargins );
	}

	/**
	 * @param	array	$data
	 * @return	Skylines\Building
	 */
	abstract public function getInstanceFromData( array $data );
}
