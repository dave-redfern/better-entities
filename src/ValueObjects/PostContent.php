<?php

namespace AppBundle\ValueObjects;

use Assert\Assert;

/**
 * Class PostContent
 *
 * @package    AppBundle\ValueObjects
 * @subpackage AppBundle\ValueObjects\PostContent
 */
class PostContent extends AbstractValueObject
{

    private $content;

    /**
     * Constructor.
     *
     * @param string $content
     */
    public function __construct($content)
    {
        Assert::that($content, null, 'content')->notEmpty()->minLength(10)->maxLength(65000);

        $this->content = $content;
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return (string)$this->content;
    }

    /**
     * @return string
     */
    public function html()
    {
        return $this->content;
    }

    /**
     * @return string
     */
    public function text()
    {
        return preg_replace(
            '/\s{2,}/',
            '',
            trim(
                strip_tags(
                    str_replace(['<br>', '<br />', '<p>', '</p>'], "\n", $this->html())
                )
            )
        );
    }

    /**
     * @param int $length
     *
     * @return string
     */
    public function summary($length = 40)
    {
        return implode(' ', array_slice(explode(' ', str_replace("\n", ' ', $this->text())), 0, $length)) . '...';
    }
}
