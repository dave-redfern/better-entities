<?php

namespace AppBundle\ValueObjects;

use Assert\Assert;

/**
 * Class EmailAddress
 *
 * @package    AppBundle\ValueObjects
 * @subpackage AppBundle\ValueObjects\EmailAddress
 */
class EmailAddress extends AbstractValueObject
{

    /**
     * @var string
     */
    private $email;

    /**
     * Constructor.
     *
     * @param string $email
     */
    public function __construct(string $email)
    {
        Assert::that($email, null, 'email')->notEmpty()->email()->maxLength(60);

        $this->email = $email;
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return (string)$this->email;
    }
}
