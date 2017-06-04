<?php

namespace AppBundle\Support\Traits;

use AppBundle\Events\AbstractEvent;

/**
 * Trait RaisesDomainEvents
 *
 * @package    AppBundle\Support\Traits
 * @subpackage AppBundle\Support\Traits\RaisesDomainEvents
 */
trait RaisesDomainEvents
{

    /**
     * @var array|AbstractEvent[]
     */
    protected $events = [];

    /**
     * @param AbstractEvent $event
     */
    public function raise(AbstractEvent $event)
    {
        $this->events[] = $event;
    }

    /**
     * @return array
     */
    public function releaseAndResetEvents(): array
    {
        $events = $this->events;

        $this->events = [];

        return $events;
    }
}