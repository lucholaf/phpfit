<?php
interface PHPFIT_FixtureListener
{
	/**
	 * @param PHPFIT_Parse $table
	 * @return void
	 */
	public function tableFinished($table);

	/**
	 * @param PHPFIT_Counts $count
	 * @return void
	 */
	public function tablesFinished($count);
}
