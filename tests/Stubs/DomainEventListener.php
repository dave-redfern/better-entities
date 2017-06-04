<?php

namespace AppBundle\Tests\Stubs;

use AppBundle\Events\AbstractEvent;
use Doctrine\Common\EventSubscriber;

/**
 * Class DomainEventListener
 *
 * @package    Stubs
 * @subpackage Stubs\DomainEventListener
 */
class DomainEventListener implements EventSubscriber
{

    public function getSubscribedEvents()
    {
        return ['onPostCreated', 'onPostPublished', 'onPostTitleChanged'];
    }

    public function onPostCreated(AbstractEvent $event)
    {
        printf(
            "Post titled %s was created and assigned id %s\n",
            $event->property('title'),
            $event->aggregate()->id()
        );
    }

    public function onPostPublished(AbstractEvent $event)
    {
        printf(
            "Post titled %s was published with slug %s\n",
            $event->property('title'),
            $event->property('title')->slug()
        );
    }

    public function onPostTitleChanged(AbstractEvent $event)
    {
        printf(
            "Post title was changed to %s for post %s\n",
            $event->property('title'),
            $event->aggregate()->id()
        );
    }
}