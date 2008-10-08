<?php
require_once 'PHPFIT/FixtureListener.php';

class PHPFIT_NullFixtureListener implements PHPFIT_FixtureListener
{
	/**
	 * @param PHPFIT_Parse $table
	 * @return void
	 */
	public function tableFinished($table)
	{
	}

	/**
	 * @param PHPFIT_Counts $count
	 * @return void
	 */
	public function tablesFinished($count)
	{
	}
}

