<?php

/*
 * This file is part of Skylines
 */
namespace Skylines\Schedule;

class Event implements \JsonSerializable
{
	/**
	 * @var	string
	 */
	protected $name;

	/**
	 * @var	int
	 */
	protected $time;

	/**
	 * @var	string
	 */
	protected $url;

	/**
	 * @param	string	$name
	 * @param	int		$time
	 * @param	string	$url
	 * @return	void
	 */
	public function __construct( $name, $time, $url )
	{
		$this->name = $name;
		$this->time = $time;
		$this->url = $url;
	}

	/**
	 * @param	string	$json
	 * @return	self
	 */
	static public function createFromJSON( $json )
	{
		$unserialized = json_decode( $json, true );

		if( json_last_error() !== 0 )
		{
			$exceptionMessage = sprintf( "Invalid JSON: '%s'", json_last_error_msg() );
			throw new \InvalidArgumentException( $exceptionMessage, json_last_error() );
		}

		return self::createFromUnserializedData( $unserialized );
	}

	/**
	 * @param	array	$unserialized
	 * @return	self
	 */
	static public function createFromUnserializedData( array $unserialized )
	{
		/* Required properties */
		$requiredProperties = ['name', 'time', 'url'];
		foreach( $requiredProperties as $requiredProperty )
		{
			if( !isset( $unserialized[$requiredProperty] ) )
			{
				throw new \InvalidArgumentException( "Missing required object: '$requiredProperty'" );
			}
		}

		$event = new self( $unserialized['name'], $unserialized['time'], $unserialized['url'] );
		return $event;
	}

	/**
	 * @return	string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return	int
	 */
	public function getTime()
	{
		return $this->time;
	}

	/**
	 * @return	string
	 */
	public function getURL()
	{
		return $this->url;
	}

	/**
	 * @return	array
	 */
	public function jsonSerialize()
	{
		$serialized = [];

		$serialized['name'] = $this->name;
		$serialized['time'] = $this->time;
		$serialized['url']  = $this->url;

		return $serialized;
	}
}
