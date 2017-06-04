<?php

namespace AppBundle\ValueObjects;

use AppBundle\Support\Slugger;
use Assert\Assert;

/**
 * Class PostTitle
 *
 * @package    AppBundle\ValueObjects
 * @subpackage AppBundle\ValueObjects\PostTitle
 */
class PostTitle extends AbstractValueObject
{

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $slug;

    /**
     * Constructor.
     *
     * @param string $title
     */
    public function __construct(string $title)
    {
        Assert::that($title, null, 'title')->notEmpty()->maxLength(100);

        $this->title = $title;
        $this->slug = Slugger::generateSlugFrom($title);
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return (string)$this->title;
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function slug(): string
    {
        return $this->slug;
    }
}
