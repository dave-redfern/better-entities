<?php

namespace AppBundle\Tests\Support\Traits;

use AppBundle\Events\PostCreated;
use AppBundle\Support\Traits\RaisesDomainEvents;
use PHPUnit\Framework\TestCase;

class RaisesDomainEventsTest extends TestCase
{

    /**
     * @group traits
     * @group traits-raises-domain-events
     * @group events
     */
    public function testCanRaiseEvents()
    {
        $mock = $this->getMockForTrait(RaisesDomainEvents::class);
        $mock->raise(new PostCreated([]));

        $events = $mock->releaseAndResetEvents();

        $this->assertCount(1, $events);

        $events = $mock->releaseAndResetEvents();

        $this->assertCount(0, $events);
    }
}
