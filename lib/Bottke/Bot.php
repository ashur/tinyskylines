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
	 * @param	string					$name			Bot name
	 * @param	Huxtable\Bot\History	$history
	 * @param	Huxtable\Core\Config	$config
	 * @return	void
	 */
	public function __construct( $name, File\Directory $dirData )
	{
		parent::__construct( $name, $dirData );
	}
}
