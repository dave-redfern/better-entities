<?php

namespace AppBundle\Tests\ValueObjects;

use AppBundle\ValueObjects\Aggregate;
use AppBundle\ValueObjects\EmailAddress;
use PHPUnit\Framework\TestCase;

/**
 * Class AggregateTest
 *
 * @package    AppBundle\Tests\ValueObjects
 * @subpackage AppBundle\Tests\ValueObjects\AggregateTest
 */
class AggregateTest extends TestCase
{

    /**
     * @group value-objects
     * @group value-objects-aggregate
     */
    public function testCreate()
    {
        $vo = new Aggregate(__CLASS__, 'AggregateTest');

        $this->assertEquals(__CLASS__, $vo->class());
        $this->assertEquals('AggregateTest', $vo->id());
    }

    /**
     * @group value-objects
     * @group value-objects-aggregate
     */
    public function testCanCastToString()
    {
        $vo = new Aggregate(__CLASS__, 'AggregateTest');

        $this->assertEquals(__CLASS__ . ':AggregateTest', (string)$vo);
    }

    /**
     * @group value-objects
     * @group value-objects-aggregate
     */
    public function testCanCompareInstances()
    {
        $vo1 = new Aggregate(__CLASS__, 'AggregateTest');
        $vo2 = new Aggregate(__CLASS__, 'AnotherTest');
        $vo3 = new Aggregate(__CLASS__, 'AggregateTest');

        $this->assertFalse($vo1->equals($vo2));
        $this->assertTrue($vo1->equals($vo3));
        $this->assertTrue($vo1->equals($vo1));
    }

    /**
     * @group value-objects
     * @group value-objects-aggregate
     */
    public function testCanCompareOtherInstances()
    {
        $vo1 = new Aggregate(__CLASS__, 'AggregateTest');
        $vo2 = new EmailAddress('bob@example.com');

        $this->assertFalse($vo1->equals($vo2));
    }

    /**
     * @group value-objects
     * @group value-objects-aggregate
     */
    public function testCantSetArbitraryProperties()
    {
        $vo = new Aggregate(__CLASS__, 'AggregateTest');
        $vo->foo = 'bar';

        $this->assertObjectNotHasAttribute('foo', $vo);
    }
}
