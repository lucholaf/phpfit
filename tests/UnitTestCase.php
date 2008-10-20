<?php
require_once 'PHPUnit/Framework.php';

/**
 * Small adapter providing SimpleTest API
 */
class UnitTestCase extends PHPUnit_Framework_TestCase
{
    protected function assertIsA($object, $class, $message = '')
    {
        $this->assertType($class, $object, $message);
    }

	protected function assertEqual($a, $b, $message = '')
	{
	    $this->assertEquals($a, $b, $message);
	}

	protected function pass($message = '')
	{
	    // This is not really the same, but generates the same assertion count.
	    $this->assertTrue(true, $message);
	}
}