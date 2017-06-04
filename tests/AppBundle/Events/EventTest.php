<?php

namespace AppBundle\Tests\Events;

use AppBundle\Events\AbstractEvent;
use AppBundle\Events\PostCreated;
use AppBundle\Exceptions\InvalidPropertyException;
use AppBundle\ValueObjects\Aggregate;
use PHPUnit\Framework\TestCase;
use Somnambulist\Collection\Immutable;

/**
 * Class EventTest
 *
 * @package    AppBundle\Tests\Events
 * @subpackage AppBundle\Tests\Events\EventTest
 */
class EventTest extends TestCase
{

    /**
     * @group events
     * @group domain
     */
    public function testCreateEvent()
    {
        $event = new PostCreated(['foo' => 'bar'], ['bar' => 'baz'], 5);

        $this->assertInstanceOf(Immutable::class, $event->properties());
        $this->assertInstanceOf(Immutable::class, $event->context());
        $this->assertArrayHasKey('foo', $event->properties());
        $this->assertArrayHasKey('bar', $event->context());
        $this->assertInternalType('float', $event->time());
        $this->assertEquals(5, $event->version());
        $this->assertEquals('PostCreated', $event->name());
        $this->assertEquals('bar', $event->property('foo'));
    }

    /**
     * @group events
     * @group domain
     */
    public function testCanSetAggregate()
    {
        $event = new PostCreated(['foo' => 'bar'], ['bar' => 'baz'], 5);
        $event->setAggregate($agg = new Aggregate(__CLASS__, __FUNCTION__));

        $this->assertEquals($agg, $event->aggregate());
    }

    /**
     * @group events
     * @group domain
     */
    public function testUnknownPropertiesRaiseException()
    {
        $event = new PostCreated(['foo' => 'bar'], ['bar' => 'baz'], 5);

        $this->expectException(InvalidPropertyException::class);
        $event->property('baz');
    }
}
