<?php

namespace AppBundle\Tests\ValueObjects;

use AppBundle\ValueObjects\PostContent;
use PHPUnit\Framework\TestCase;

/**
 * Class PostContentTest
 *
 * @package    AppBundle\Tests\ValueObjects
 * @subpackage AppBundle\Tests\ValueObjects\PostContentTest
 */
class PostContentTest extends TestCase
{

    /**
     * @group value-objects
     * @group value-objects-post-content
     */
    public function testCreate()
    {
        $vo = new PostContent('<h2>First Post</h2><p>This is your first post, you should do something with it</p>');

        $this->assertEquals('<h2>First Post</h2><p>This is your first post, you should do something with it</p>', $vo->html());
        $this->assertEquals("First Post\nThis is your first post, you should do something with it", $vo->text());
    }

    /**
     * @group value-objects
     * @group value-objects-post-content
     */
    public function testCanCastToString()
    {
        $vo = new PostContent('<h2>First Post</h2><p>This is your first post, you should do something with it</p>');

        $this->assertEquals('<h2>First Post</h2><p>This is your first post, you should do something with it</p>', (string)$vo);
    }

    /**
     * @group value-objects
     * @group value-objects-post-content
     */
    public function testCanGetASummary()
    {
        $vo = new PostContent('<h2>First Post</h2><p>This is your first post, you should do something with it</p>');

        $this->assertEquals('First Post This is your...', $vo->summary(5));
    }

    /**
     * @group value-objects
     * @group value-objects-post-content
     */
    public function testCanCompareInstances()
    {
        $vo1 = new PostContent('<h2>First Post</h2><p>This is your first post, you should do something with it</p>');
        $vo2 = new PostContent('<h2>Second Post</h2><p>Another post</p>');
        $vo3 = new PostContent('<h2>First Post</h2><p>This is your first post, you should do something with it</p>');

        $this->assertFalse($vo1->equals($vo2));
        $this->assertTrue($vo1->equals($vo3));
        $this->assertTrue($vo1->equals($vo1));
    }

    /**
     * @group value-objects
     * @group value-objects-post-content
     */
    public function testCantSetArbitraryProperties()
    {
        $vo = new PostContent('<h2>First Post</h2><p>This is your first post, you should do something with it</p>');
        $vo->foo = 'bar';

        $this->assertObjectNotHasAttribute('foo', $vo);
    }
}
