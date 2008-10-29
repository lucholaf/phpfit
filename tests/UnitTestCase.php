<?php
require_once 'PHPUnit/Framework.php';

/**
 * Small adapter providing SimpleTest API
 */
class UnitTestCase extends PHPUnit_Framework_TestCase
{
    public static function assertEquals($expected, $actual, $message = '', $delta = 0, $maxDepth = 10, $canonicalizeEol = FALSE)
	{
	    throw new Exception('assertEquals() called! For SimpleTest compliance use assertEqual().');
	}

    public static function assertType($expected, $actual, $message = '')
	{
	    throw new Exception('assertType() called! For SimpleTest compliance use assertIsA().');
	}

    protected function assertIsA($object, $class, $message = '')
    {
        parent::assertType($class, $object, $message);
    }

	protected function assertEqual($a, $b, $message = '')
	{
	    parent::assertEquals($a, $b, $message);
	}

	protected function pass($message = '')
	{
	    // This is not really the same, but generates the same assertion count.
	    $this->assertTrue(true, $message);
	}
}