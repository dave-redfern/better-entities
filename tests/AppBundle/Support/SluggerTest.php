<?php

namespace AppBundle\Tests\Support;

use AppBundle\Support\Slugger;
use PHPUnit\Framework\TestCase;

/**
 * Class SluggerTest
 *
 * @package    AppBundle\Tests\Support
 * @subpackage AppBundle\Tests\Support\SluggerTest
 */
class SluggerTest extends TestCase
{

    /**
     * @group support
     * @group support-slugger
     */
    public function testSlug()
    {
        $this->assertEquals('this-is-a-title', Slugger::generateSlugFrom('This IS A Title'));
    }
}
