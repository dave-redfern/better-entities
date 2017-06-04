<?php

namespace AppBundle\Support\Contracts;

use AppBundle\Events\AbstractEvent;

/**
 * Interface RaisesDomainEvents
 *
 * @package    AppBundle\Support\Contracts
 * @subpackage AppBundle\Support\Contracts\RaisesDomainEvents
 */
interface RaisesDomainEvents
{

    /**
     * @param AbstractEvent $event
     */
    public function raise(AbstractEvent $event);

    /**
     * @return array|AbstractEvent[]
     */
    public function releaseAndResetEvents(): array;
}