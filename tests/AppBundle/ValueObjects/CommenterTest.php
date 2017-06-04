<?php

namespace AppBundle\Tests\ValueObjects;

use AppBundle\ValueObjects\Commenter;
use AppBundle\ValueObjects\EmailAddress;
use PHPUnit\Framework\TestCase;

/**
 * Class CommenterTest
 *
 * @package    AppBundle\Tests\ValueObjects
 * @subpackage AppBundle\Tests\ValueObjects\CommenterTest
 */
class CommenterTest extends TestCase
{

    /**
     * @group value-objects
     * @group value-objects-commenter
     */
    public function testCreate()
    {
        $vo = new Commenter('foo bar', new EmailAddress('foo@example.com'));

        $this->assertEquals('foo bar', $vo->name());
        $this->assertEquals('foo@example.com', (string)$vo->email());
    }

    /**
     * @group value-objects
     * @group value-objects-commenter
     */
    public function testCanCastToString()
    {
        $vo = new Commenter('foo bar', new EmailAddress('foo@example.com'));

        $this->assertEquals('foo bar', (string)$vo);
    }

    /**
     * @group value-objects
     * @group value-objects-commenter
     */
    public function testCanCompareInstances()
    {
        $vo1 = new Commenter('foo bar', new EmailAddress('foo@example.com'));
        $vo2 = new Commenter('bar baz', new EmailAddress('foo@example.com'));
        $vo3 = new Commenter('foo bar', new EmailAddress('foo@example.com'));


        $this->assertFalse($vo1->equals($vo2));
        $this->assertTrue($vo1->equals($vo3));
        $this->assertTrue($vo1->equals($vo1));
    }

    /**
     * @group value-objects
     * @group value-objects-commenter
     */
    public function testCantSetArbitraryProperties()
    {
        $vo = new Commenter('foo bar', new EmailAddress('foo@example.com'));
        $vo->foo = 'bar';

        $this->assertObjectNotHasAttribute('foo', $vo);
    }
}
