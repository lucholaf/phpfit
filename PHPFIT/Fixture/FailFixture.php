<?php
/**
 * Fixture that simply fails
 * 
 * Used for FitServerTest.java from the Fitnesse package
 * @see https://fitnesse.svn.sourceforge.net/svnroot/fitnesse/trunk/srcFitServerTests
 */
class PHPFIT_Fixture_FailFixture extends PHPFIT_Fixture
{
	public function doTable($parse)
	{
		$this->wrong($parse);
	}
}
