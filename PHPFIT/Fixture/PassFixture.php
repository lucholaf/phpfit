<?php
/**
 * Fixture that simply passes
 * 
 * Used for FitServerTest.java from the Fitnesse package
 * @see https://fitnesse.svn.sourceforge.net/svnroot/fitnesse/trunk/srcFitServerTests
 */
class PHPFIT_Fixture_PassFixture extends PHPFIT_Fixture
{
	public function doTable($parse)
	{
		$this->right($parse);
	}
}
