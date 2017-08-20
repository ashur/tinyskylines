<?php

/*
 * This file is part of Skylines
 */
namespace Skylines\Schedule;

use PHPUnit\Framework\TestCase;

class ScheduleTest extends TestCase
{
	public function testAddEvent()
	{
		$schedule = new Schedule();

		$eventName = 'name-' . microtime( true );
		$eventTime = time() + 10;
		$eventURL  = "https://cabrera-bots.s3.amazonaws.com/tinyskylines/skylines/{$eventName}.json";

		$event = new Event( $eventName, $eventTime, $eventURL );

		$schedule->addEvent( $event );
		$events = $schedule->getEvents();

		$this->assertArrayHasKey( 0, $events );
		$this->assertSame( $event, $events[0] );
	}

	/**
	 * @expectedException		InvalidArgumentException
	 * @expectedExceptionCode	JSON_ERROR_SYNTAX
	 */
	public function testCreateFromInvalidJSONThrowsException()
	{
		$invalidJSON = '{[}';
		$schedule = Schedule::createFromJSON( $invalidJSON );
	}

	/**
	 * @expectedException	InvalidArgumentException
	 */
	public function testCreateFromJSONMissingRequiredPropertyThrowsException()
	{
		$json = '{}';
		$schedule = Schedule::createFromJSON( $json );
	}

	/**
	 * @expectedException	InvalidArgumentException
	 */
	public function testCreateFromJSONMissingRequiredPropertyTypeThrowsException()
	{
		$json = '{ "events": "string" }';
		$schedule = Schedule::createFromJSON( $json );
	}

	public function testCreateFromJSON()
	{
		$schedule = new Schedule();

		$event = new Event( 'name', time() - 60, 'https://cabrera-bots.s3.amazonaws.com/tinyskylines/skylines/name.json' );
		$schedule->addEvent( $event );

		$serializedSchedule = json_encode( $schedule );

		$factorySchedule = Schedule::createFromJSON( $serializedSchedule );

		$this->assertEquals( $schedule, $factorySchedule );
	}

	public function testGetEventsInRange()
	{
		$schedule = new Schedule();

		$currentTime = time();

		/* Event 1: 10 minutes from "now" */
		$eventName1 = 'name-' . microtime( true );
		$eventTime1 = $currentTime + (60 * 10);
		$eventURL1  = "https://cabrera-bots.s3.amazonaws.com/tinyskylines/skylines/{$eventName1}.json";

		$event1 = new Event( $eventName1, $eventTime1, $eventURL1 );
		$schedule->addEvent( $event1 );

		/* Event 2: 2 days from "now" */
		$eventName2 = 'name-' . microtime( true );
		$eventTime2 = $currentTime + (60 * 60 * 24 * 2);
		$eventURL2  = "https://cabrera-bots.s3.amazonaws.com/tinyskylines/skylines/{$eventName2}.json";

		$event2 = new Event( $eventName2, $eventTime2, $eventURL2 );
		$schedule->addEvent( $event2 );

		$matchingEvents = $schedule->getEventsInRange( $currentTime - (60 * 20), $currentTime + (60 * 20) );

		$this->assertTrue( is_array( $matchingEvents ) );
		$this->assertEquals( 1, count( $matchingEvents ) );
		$this->assertArrayHasKey( 0, $matchingEvents );
		$this->assertSame( $event1, $matchingEvents[0] );
	}

	public function testGetEventsInRangeFromEmptyScheduleReturnsEmptyArray()
	{
		$schedule = new Schedule();

		$currentTime = time();
		$events = $schedule->getEventsInRange( $currentTime - 60, $currentTime + 60 );

		$this->assertTrue( is_array( $events ) );
		$this->assertEquals( 0, count( $events ) );
	}

	public function testGetEventsFromEmptyScheduleReturnsEmptyArray()
	{
		$schedule = new Schedule();
		$events = $schedule->getEvents();

		$this->assertTrue( is_array( $events ) );
		$this->assertEquals( 0, count( $events ) );
	}

	public function testSerializeEmptyScheduleToJSON()
	{
		$schedule = new Schedule();

		$serializedSchedule = json_encode( $schedule );
		$unserializedSchedule = json_decode( $serializedSchedule, true );

		$this->assertTrue( is_array( $unserializedSchedule ) );
		$this->assertArrayHasKey( 'events', $unserializedSchedule );
		$this->assertEquals( 0, count( $unserializedSchedule['events'] ) );
	}

	public function testSerializeNonEmptyScheduleToJSON()
	{
		$schedule = new Schedule();

		$event = new Event( 'name', time() - 60, 'https://cabrera-bots.s3.amazonaws.com/tinyskylines/skylines/name.json' );
		$schedule->addEvent( $event );

		$serializedSchedule = json_encode( $schedule );
		$unserializedSchedule = json_decode( $serializedSchedule, true );

		$this->assertTrue( is_array( $unserializedSchedule ) );
		$this->assertArrayHasKey( 'events', $unserializedSchedule );
		$this->assertEquals( 1, count( $unserializedSchedule['events'] ) );
	}
}
