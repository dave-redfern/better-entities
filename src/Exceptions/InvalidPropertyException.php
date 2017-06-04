<?php

namespace AppBundle\Exceptions;

/**
 * Class InvalidPropertyException
 *
 * @package    AppBundle\Exceptions
 * @subpackage AppBundle\Exceptions\InvalidPropertyException
 */
class InvalidPropertyException extends \InvalidArgumentException
{

    /**
     * @param string $name
     *
     * @return InvalidPropertyException
     */
    public static function propertyDoesNotExist($name)
    {
        return new static(sprintf('Property "%s" does not exist', $name));
    }
}
