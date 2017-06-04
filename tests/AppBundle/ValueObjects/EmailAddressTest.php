<?php

namespace AppBundle\Tests\ValueObjects;

use AppBundle\ValueObjects\EmailAddress;
use PHPUnit\Framework\TestCase;

/**
 * Class EmailAddressTest
 *
 * @package    AppBundle\Tests\ValueObjects
 * @subpackage AppBundle\Tests\ValueObjects\EmailAddressTest
 */
class EmailAddressTest extends TestCase
{

    /**
     * @group value-objects
     * @group value-objects-email-address
     */
    public function testCreate()
    {
        $vo = new EmailAddress('foo@example.com');

        $this->assertEquals('foo@example.com', $vo->toString());
    }

    /**
     * @group value-objects
     * @group value-objects-email-address
     */
    public function testCanCastToString()
    {
        $vo = new EmailAddress('foo@example.com');

        $this->assertEquals('foo@example.com', (string)$vo);
    }

    /**
     * @group value-objects
     * @group value-objects-email-address
     */
    public function testCanCompareInstances()
    {
        $vo1 = new EmailAddress('foo@example.com');
        $vo2 = new EmailAddress('foo.bar@example.com');
        $vo3 = new EmailAddress('foo@example.com');


        $this->assertFalse($vo1->equals($vo2));
        $this->assertTrue($vo1->equals($vo3));
        $this->assertTrue($vo1->equals($vo1));
    }

    /**
     * @group value-objects
     * @group value-objects-email-address
     */
    public function testCantSetArbitraryProperties()
    {
        $vo = new EmailAddress('foo@example.com');
        $vo->foo = 'bar';

        $this->assertObjectNotHasAttribute('foo', $vo);
    }
}
