<?php
/**
 * This file is part of Phutler.
 * Please check the file LICENSE for information about the license.
 *
 * @copyright Daniel Haas 2013
 * @author Daniel Haas <daniel@file-factory.de>
 */

namespace TimeDilation;

use Cron\CronExpression;
use Phutler\Tasks\Task;


TimeMachine::infectNamespace("Phutler");
TimeMachine::infectNamespace("React\\EventLoop\\Timer");
TimeMachine::infectNamespace("Cron");

class TaskTest extends \PHPUnit_Framework_TestCase
{

	function testCronMethods()
	{
		TimeMachine::setNow("2013-03-03 09:59:00",200);
		$loop=\React\EventLoop\Factory::create();
		$someTask=new SomeTask(new \stdClass(),$loop);
		TimeMachine::fastForward(60);
		$loop->tick();

		$this->assertTrue($someTask->doAtSunday10aClockExecuted);
	}

}


class SomeTask extends Task
{
	public $doAtSunday10aClockExecuted=false;

	/**
	 * @cron 0 10 * * SUN *
	 */
	function doAtSunday10aClock()
	{
		$this->doAtSunday10aClockExecuted=true;
	}

}