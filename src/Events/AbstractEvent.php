<?php

namespace AppBundle\Events;

use AppBundle\Exceptions\InvalidPropertyException;
use AppBundle\ValueObjects\Aggregate;
use Doctrine\Common\EventArgs;
use Somnambulist\Collection\Immutable;

/**
 * Class AbstractEvent
 *
 * EventArgs is a concession to Doctrine the Doctrine Event Dispatcher can be used.
 *
 * @package    AppBundle\Events
 * @subpackage AppBundle\Events\AbstractEvent
 */
abstract class AbstractEvent extends EventArgs
{

    /**
     * @var string
     */
    private $name;

    /**
     * @var Immutable
     */
    private $properties;

    /**
     * @var Immutable
     */
    private $context;

    /**
     * @var Aggregate
     */
    private $aggregate;

    /**
     * @var int
     */
    private $version;

    /**
     * @var float
     */
    private $time;



    /**
     * Constructor.
     *
     * @param array $payload Array of specific state change data
     * @param array $context Array of additional data providing context e.g. user, ip etc
     * @param int   $version A version identifier for the payload format
     */
    public function __construct(array $payload = [], array $context = [], $version = 1)
    {
        $this->properties = new Immutable($payload);
        $this->context    = new Immutable($context);
        $this->time       = microtime(true);
        $this->version    = $version;
    }

    /**
     * @return float
     */
    public function time(): float
    {
        return $this->time;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        if (is_null($this->name)) {
            $this->name = $this->parseName();
        }

        return $this->name;
    }

    /**
     * @return string
     */
    private function parseName(): string
    {
        $class = get_class($this);

        if (substr($class, -5) === "Event") {
            $class = substr($class, 0, -5);
        }
        if (strpos($class, "\\") === false) {
            return $class;
        }

        $parts = explode("\\", $class);

        return end($parts);
    }

    /**
     * @return Immutable
     */
    public function properties(): Immutable
    {
        return $this->properties;
    }

    /**
     * @return Immutable
     */
    public function context(): Immutable
    {
        return $this->context;
    }

    /**
     * @return int
     */
    public function version(): int
    {
        return $this->version;
    }

    /**
     * @return Aggregate
     */
    public function aggregate(): Aggregate
    {
        return $this->aggregate;
    }

    /**
     * @param string $name
     *
     * @return null|mixed
     */
    public function property($name)
    {
        if (!$this->properties->has($name)) {
            throw InvalidPropertyException::propertyDoesNotExist($name);
        }

        return $this->properties->get($name);
    }

    /**
     * @param Aggregate $aggregate
     */
    public function setAggregate(Aggregate $aggregate)
    {
        if (is_null($this->aggregate)) {
            $this->aggregate = $aggregate;
        }
    }
}
