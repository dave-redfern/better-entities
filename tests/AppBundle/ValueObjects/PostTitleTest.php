<?php

namespace AppBundle\Tests\ValueObjects;

use AppBundle\ValueObjects\PostTitle;
use PHPUnit\Framework\TestCase;

/**
 * Class PostTitleTest
 *
 * @package    AppBundle\Tests\ValueObjects
 * @subpackage AppBundle\Tests\ValueObjects\PostTitleTest
 */
class PostTitleTest extends TestCase
{


    /**
     * @group value-objects
     * @group value-objects-post-title
     */
    public function testCreate()
    {
        $vo = new PostTitle('This is my Title');

        $this->assertEquals('This is my Title', $vo->title());
        $this->assertEquals('this-is-my-title', $vo->slug());
    }

    /**
     * @group value-objects
     * @group value-objects-post-title
     */
    public function testCanCastToString()
    {
        $vo = new PostTitle('This is my Title');

        $this->assertEquals('This is my Title', (string)$vo);
    }

    /**
     * @group value-objects
     * @group value-objects-post-title
     */
    public function testCanCompareInstances()
    {
        $vo1 = new PostTitle('This is my Title');
        $vo2 = new PostTitle('This is not my Title');
        $vo3 = new PostTitle('This is my Title');

        $this->assertFalse($vo1->equals($vo2));
        $this->assertTrue($vo1->equals($vo3));
        $this->assertTrue($vo1->equals($vo1));
    }

    /**
     * @group value-objects
     * @group value-objects-post-title
     */
    public function testCantSetArbitraryProperties()
    {
        $vo = new PostTitle('This is my Title');
        $vo->foo = 'bar';

        $this->assertObjectNotHasAttribute('foo', $vo);
    }
}
