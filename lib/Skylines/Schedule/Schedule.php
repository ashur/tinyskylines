<?php

/*
 * This file is part of Skylines
 */
namespace Skylines\Schedule;

class Schedule implements \JsonSerializable
{
	/**
	 * @var	array
	 */
	protected $events=[];

	/**
	 * @param	Skylines\Schedule\Event	$event
	 * @return	void
	 */
	public function addEvent( Event $event )
	{
		$this->events[] = $event;
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

		/* Required properties */
		if( !isset( $unserialized['events'] ) || !is_array( $unserialized['events'] ) )
		{
			throw new \InvalidArgumentException( "Missing required object: 'events'" );
		}

		$schedule = new self();

		foreach( $unserialized['events'] as $unserializedEvent )
		{
			$event = Event::createFromUnserializedData( $unserializedEvent );
			$schedule->addEvent( $event );
		}

		return $schedule;
	}

	/**
	 * @return	array
	 */
	public function getEvents()
	{
		return $this->events;
	}

	/**
	 * @param	int		$startTime	Unix timestamp representing start of range
	 * @param	int		$endTime	Unix timestamp representing end of range
	 * @return	array
	 */
	public function getEventsInRange( $startTime, $endTime )
	{
		$matchingEvents = [];

		foreach( $this->events as $event )
		{
			$eventTime = $event->getTime();

			if( $eventTime >= $startTime && $eventTime <= $endTime )
			{
				$matchingEvents[] = $event;
			}
		}

		return $matchingEvents;
	}

	/**
	 * @return	array
	 */
	public function jsonSerialize()
	{
		$serialized = [];

		$serialized['events'] = $this->events;

		return $serialized;
	}
}
