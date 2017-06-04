<?php

namespace AppBundle\ValueObjects;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class AbstractValueObject
 *
 * @package    AppBundle\ValueObjects
 * @subpackage AppBundle\ValueObjects\AbstractValueObject
 */
abstract class AbstractValueObject
{

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        // prevent arbitrary properties
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->toString();
    }

    /**
     * @return string
     */
    abstract function toString(): string;

    /**
     * @param AbstractValueObject $object
     *
     * @return bool
     */
    public function equals($object): bool
    {
        if (get_class($this) === get_class($object)) {
            $props = new ArrayCollection((new \ReflectionObject($this))->getProperties());

            return $props->forAll(function ($key, $prop) use ($object) {
                /** @var \ReflectionProperty $prop */
                $prop->setAccessible(true);

                return (string)$prop->getValue($object) === (string)$prop->getValue($this);
            });
        }

        return false;
    }
}
