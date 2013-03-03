<?php
/**
 * This file is part of Phutler.
 * Please check the file LICENSE for information about the license.
 *
 * @copyright Daniel Haas 2013
 * @author Daniel Haas <daniel@file-factory.de>
 */


namespace TimeDilation;

TimeMachine::infectNamespace("Phutler");
TimeMachine::infectNamespace("Cron");

class CronMethodExecutorTest extends \PHPUnit_Framework_TestCase
{
	function testExecutionEveryMinute()
	{
		$someClass=new SomeClass();
		$cronMethodExecutor=new \Phutler\CronMethodExecutor($someClass);

		$cronMethodExecutor->executeCronMethods();
		$this->assertTrue($someClass->everyMinuteCalled);
		$this->assertFalse($someClass->everyHourCalled);

	}

	function testExecutionEveryHour()
	{
		$someClass=new SomeClass();
		$cronMethodExecutor=new \Phutler\CronMethodExecutor($someClass);
		TimeMachine::setNow("2012-07-12 17:01:00");

		$cronMethodExecutor->executeCronMethods();
		$this->assertTrue($someClass->everyMinuteCalled);
		$this->assertTrue($someClass->everyHourCalled);

		//now test that the methods are not called again if we call executeCronMethods() again with the same time
		$someClass->everyHourCalled=false;
		$someClass->everyMinuteCalled=false;
		$cronMethodExecutor->executeCronMethods();
		$this->assertFalse($someClass->everyMinuteCalled);
		$this->assertFalse($someClass->everyHourCalled);

	}
}

/**
 * Short test class that uses the @cron notation to execute methods regularly.
 *
 */
class SomeClass
{
	public $everyMinuteCalled=false;
	public $everyHourCalled=false;


	/**
	 * @cron * * * * * *
	 */
	function everyMinute()
	{
		$this->everyMinuteCalled=true;
	}

	/**
	 * @cron 1 * * * * *
	 */
	function everyHourAtMinute1()
	{
		$this->everyHourCalled=true;
	}
}