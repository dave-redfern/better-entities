<?php

namespace AppBundle\ValueObjects;

/**
 * Class Aggregate
 *
 * @package    AppBundle\ValueObjects
 * @subpackage AppBundle\ValueObjects\Aggregate
 */
class Aggregate extends AbstractValueObject
{

    /**
     * @var string
     */
    private $class;

    /**
     * @var string
     */
    private $id;

    /**
     * Constructor.
     *
     * @param string $class
     * @param string $id
     */
    public function __construct(string $class, $id)
    {
        $this->class = $class;
        $this->id    = $id;
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return sprintf('%s:%s', $this->class, $this->id);
    }

    /**
     * @return string
     */
    public function class(): string
    {
        return $this->class;
    }

    /**
     * @return string
     */
    public function id(): string
    {
        return (string)$this->id;
    }
}
