<?php
/**
 * This file is part of Phutler.
 * Please check the file LICENSE for information about the license.
 *
 * @copyright Daniel Haas 2013
 * @author Daniel Haas <daniel@file-factory.de>
 */

namespace Phutler;

use Cron\CronExpression;

/**
 * A simple helper class that executes a given callback anytime the given CronExpression is due.
 *
 */
class CronMethod
{
	/** @var CronExpression */
	private $cronExpression;
	private $callback;
	private $lastCallTimestamp;

	/**
	 * Constructs a new CronMethod.
	 * @param CronExpression $_cronExpression The CronExpression that is used to decide when to execute the callback
	 * @param callback $_callback The callback that should be executed if the \c $_cronExpression is due.
	 */
	function __construct(CronExpression $_cronExpression, $_callback)
	{
		$this->cronExpression=$_cronExpression;
		$this->callback=$_callback;
		$this->lastCallTimestamp=0;
	}

	/**
	 * Executes the callback if the CronExpression is due.
	 * This should be called in regular intervals every minute.
	 */
	public function executeIfDue()
	{
		if ($this->cronExpression->isDue() && time()>$this->lastCallTimestamp+60)
		{
			$this->lastCallTimestamp=time();
			call_user_func($this->callback);
		}
	}
}