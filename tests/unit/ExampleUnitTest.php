<?php
/**
 * Eventsky plugin for Craft CMS 3.x
 *
 * Craft plugin for event management and attendee registration
 *
 * @link      https://fredmansky.at
 * @copyright Copyright (c) 2021 Fredmansky
 */

namespace fredmansky\eventskytests\unit;

use Codeception\Test\Unit;
use UnitTester;
use Craft;
use fredmansky\eventsky\Eventsky;

/**
 * ExampleUnitTest
 *
 *
 * @author    Fredmansky
 * @package   Eventsky
 * @since     1.0.0
 */
class ExampleUnitTest extends Unit
{
    // Properties
    // =========================================================================

    /**
     * @var UnitTester
     */
    protected $tester;

    // Public methods
    // =========================================================================

    // Tests
    // =========================================================================

    /**
     *
     */
    public function testPluginInstance()
    {
        $this->assertInstanceOf(
            Eventsky::class,
            Eventsky::$plugin
        );
    }

    /**
     *
     */
    public function testCraftEdition()
    {
        Craft::$app->setEdition(Craft::Pro);

        $this->assertSame(
            Craft::Pro,
            Craft::$app->getEdition()
        );
    }
}
