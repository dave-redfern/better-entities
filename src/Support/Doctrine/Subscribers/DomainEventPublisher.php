<?php

namespace AppBundle\Support\Doctrine\Subscribers;

use AppBundle\Events\AbstractEvent;
use AppBundle\Support\Contracts\RaisesDomainEvents;
use AppBundle\ValueObjects\Aggregate;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Events;
use Somnambulist\Collection\Collection;

/**
 * Class DomainEventPublisher
 *
 * @package    AppBundle\Support\Doctrine\Subscribers
 * @subpackage AppBundle\Support\Doctrine\Subscribers\DomainEventPublisher
 */
class DomainEventPublisher implements EventSubscriber
{

    /**
     * @var Collection|RaisesDomainEvents[]
     */
    private $entities;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->entities = new Collection();
    }

    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [Events::prePersist, Events::preFlush, Events::postFlush];
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function prePersist(LifecycleEventArgs $event)
    {
        $entity = $event->getEntity();

        if ($entity instanceof RaisesDomainEvents) {
            $this->entities->add($entity);
        }
    }

    /**
     * @param PreFlushEventArgs $event
     */
    public function preFlush(PreFlushEventArgs $event)
    {
        $uow = $event->getEntityManager()->getUnitOfWork();

        foreach ($uow->getIdentityMap() as $class => $entities) {
            if (!in_array(RaisesDomainEvents::class, class_implements($class))) {
                continue; // @codeCoverageIgnore
            }

            foreach ($entities as $entity) {
                $this->entities->add($entity);
            }
        }
    }

    /**
     * @param PostFlushEventArgs $event
     */
    public function postFlush(PostFlushEventArgs $event)
    {
        $em     = $event->getEntityManager();
        $evm    = $em->getEventManager();
        $events = new Collection();

        /*
         * Capture all domain events in this UoW and re-order for dispatch
         */
        foreach ($this->entities as $entity) {
            $class = $em->getClassMetadata(get_class($entity));

            foreach ($entity->releaseAndResetEvents() as $domainEvent) {
                /** @var AbstractEvent $domainEvent */
                $domainEvent->setAggregate(new Aggregate($class->name, $class->getSingleIdReflectionProperty()->getValue($entity)));

                $events->add($domainEvent);
            }
        }

        $events->sortUsing(function ($a, $b) {
            /** @var AbstractEvent $a */
            /** @var AbstractEvent $b */
            return bccomp($a->time(), $b->time(), 6);
        });

        /*
         * Events should now be in created order so they can be dispatched / published
         */
        $events->call(function ($event) use ($em, $evm) {
            /** @var AbstractEvent $event */
            $evm->dispatchEvent('on' . $event->name(), $event);
        });

        $this->entities->reset();
    }
}
