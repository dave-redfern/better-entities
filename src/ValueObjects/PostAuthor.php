<?php

namespace AppBundle\ValueObjects;

use Assert\Assert;

/**
 * Class PostAuthor
 *
 * @package    AppBundle\ValueObjects
 * @subpackage AppBundle\ValueObjects\PostAuthor
 */
class PostAuthor extends AbstractValueObject
{

    /**
     * @var string
     */
    private $name;

    /**
     * @var EmailAddress
     */
    private $email;

    /**
     * Constructor.
     *
     * @param string       $name
     * @param EmailAddress $email
     */
    public function __construct(string $name, EmailAddress $email)
    {
        Assert::that($name, null, 'name')->notEmpty()->maxLength(50);

        $this->name  = $name;
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return (string)$this->name;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @return EmailAddress
     */
    public function email(): EmailAddress
    {
        return $this->email;
    }
}
