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


class CronMethodTest extends \PHPUnit_Framework_TestCase
{
	private $callbackWasExecuted=false;

	function setUp()
	{
		$this->callbackWasExecuted=false;
	}

	function callbackMethod()
	{
		$this->callbackWasExecuted=true;
	}


	function testExecuteEveryMinute()
	{
		$cronExpression=\Cron\CronExpression::factory("* * * * * *");
		$cronMethod=new \Phutler\CronMethod($cronExpression,array($this,"callbackMethod"));
		TimeMachine::setNow(time()+61);
		$cronMethod->executeIfDue();
		$this->assertTrue($this->callbackWasExecuted);

		//now check if calling it again will leave it at false
		$this->callbackWasExecuted=false;
		$cronMethod->executeIfDue();
		$this->assertFalse($this->callbackWasExecuted);

		//now check if calling it again will set it to true
		$this->callbackWasExecuted=false;
		TimeMachine::fastForward(61); //fast forward the time so that it must be executed again
		$cronMethod->executeIfDue();
		$this->assertTrue($this->callbackWasExecuted);


	}

	function testExecuteEveryHour()
	{
		$cronExpression=\Cron\CronExpression::factory("1 * * * * *");
		$cronMethod=new \Phutler\CronMethod($cronExpression,array($this,"callbackMethod"));
		TimeMachine::setNow("2013-02-27 21:01:00");
		$cronMethod->executeIfDue();

		$this->assertTrue($this->callbackWasExecuted);

		//now check if calling it again will leave it at false
		$this->callbackWasExecuted=false;
		$cronMethod->executeIfDue();
		$this->assertFalse($this->callbackWasExecuted);
	}



}
