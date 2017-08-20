<?php

/*
 * This file is part of Skylines
 */
namespace Skylines\Schedule;

use PHPUnit\Framework\TestCase;

class EventTest extends TestCase
{
	public function missingPropertyJSONProvider()
	{
		return [
			[ '{ "name": "event-name", "time": 12345678 }' ],					// missing url
			[ '{ "time": 12345678, "url": "https://cabreramade.co" }' ],		// missing name
			[ '{ "name": "event-name", "url": "https://cabreramade.co" }' ],	// missing time
		];
	}

	public function test__constructSetsProperties()
	{
		$eventName = 'name-' . microtime( true );
		$eventTime = time() + 10;
		$eventURL  = "https://cabrera-bots.s3.amazonaws.com/tinyskylines/skylines/{$eventName}.json";

		$event = new Event( $eventName, $eventTime, $eventURL );

		$this->assertEquals( $eventName, $event->getName() );
		$this->assertEquals( $eventTime, $event->getTime() );
		$this->assertEquals( $eventURL, $event->getURL() );
	}

	/**
	 * @expectedException		InvalidArgumentException
	 * @expectedExceptionCode	JSON_ERROR_SYNTAX
	 */
	public function testCreateFromInvalidJSONThrowsException()
	{
		$invalidJSON = '{[}';
		$event = Event::createFromJSON( $invalidJSON );
	}

	/**
	 * @dataProvider		missingPropertyJSONProvider
	 * @expectedException	InvalidArgumentException
	 */
	public function testCreateFromJSONMissingRequiredPropertyThrowsException( $json )
	{
		$event = Event::createFromJSON( $json );
	}

	/**
	 * @dataProvider		missingPropertyJSONProvider
	 * @expectedException	InvalidArgumentException
	 */
	public function testCreateFromUnserializedDataMissingRequiredPropertyThrowsException( $json )
	{
		$unserialized = json_decode( $json, true );
		$factoryEvent = Event::createFromUnserializedData( $unserialized );
	}

	public function testCreateFromJSON()
	{
		$eventName = 'name-' . microtime( true );
		$eventTime = time() + 10;
		$eventURL  = "https://cabrera-bots.s3.amazonaws.com/tinyskylines/skylines/{$eventName}.json";

		$event = new Event( $eventName, $eventTime, $eventURL );
		$serializedEvent = json_encode( $event );

		$factoryEvent = Event::createFromJSON( $serializedEvent );

		$this->assertEquals( $event, $factoryEvent );
	}

	public function testSerializeToJSON()
	{
		$eventName = 'name-' . microtime( true );
		$eventTime = time() + 10;
		$eventURL  = "https://cabrera-bots.s3.amazonaws.com/tinyskylines/skylines/{$eventName}.json";

		$event = new Event( $eventName, $eventTime, $eventURL );

		$serializedEvent = json_encode( $event );
		$unserializedEvent = json_decode( $serializedEvent, true );

		$this->assertTrue( is_array( $unserializedEvent ) );

		$this->assertArrayHasKey( 'name', $unserializedEvent );
		$this->assertEquals( $eventName, $unserializedEvent['name'] );

		$this->assertArrayHasKey( 'time', $unserializedEvent );
		$this->assertEquals( $eventTime, $unserializedEvent['time'] );

		$this->assertArrayHasKey( 'url', $unserializedEvent );
		$this->assertEquals( $eventURL, $unserializedEvent['url'] );
	}
}
