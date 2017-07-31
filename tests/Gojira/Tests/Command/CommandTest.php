<?php
/**
 * Copyright © 2017 Toan Nguyen. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gojira\Tests\Command;

use Gojira\Command;
use Gojira\Application;

class CommandMock extends \Gojira\Provider\Console\Command {}

/**
 * Command\Command test cases.
 *
 * @author Toan Nguyen <me@nntoan.com>
 */
class CommandTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Gojira\Command\Command */
    protected $fixture = null;

    /**
     * Sets up the test fixture.
     */
    public function setUp()
    {
        $this->fixture = new CommandMock('version');
    }

    /**
     * Tests the getContainer method.
     */
    public function testContainer()
    {
        $app = new Application('Test');
        $app->command($this->fixture);

        $this->assertSame($app, $this->fixture->getContainer());
    }

    /**
     * Tests whether the getService method correctly retrieves an element from
     * the container.
     */
    public function testGetService()
    {
        $app = new Application('Test');
        $app->command($this->fixture);

        $this->assertInstanceOf('Symfony\Component\Console\Application', $this->fixture->getService('console'));
    }
}
