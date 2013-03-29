<?php
/**
 * This file is part of Phutler.
 * Please check the file LICENSE for information about the license.
 *
 * @copyright Daniel Haas 2013
 * @author Daniel Haas <daniel@file-factory.de>
 */

use Phutler\Dependency;

class DependencyTest extends PHPUnit_Framework_TestCase
{
	function testRequireConfigValues()
	{
		$loop = \React\EventLoop\Factory::create();
		$log=$this->getMock('Monolog\\Logger',array("error"),array("name"));
		$log->expects($this->at(0))->method("error")->with("Required config value not passed");
		$log->expects($this->at(1))->method("error")->with("Required config value #2 not passed");
		$log->expects($this->exactly(2))->method("error");

		$testDependency=new TestDependency($loop,$log,(object)array("present"=>"value"));
		$testDependency->testRequireConfigValues($this);

	}
}

class TestDependency extends Dependency
{
	function testRequireConfigValues(PHPUnit_Framework_TestCase $_test)
	{
		$result=$this->requireConfigValues(array("myvalue"=>"Required config value not passed",
												 "myvalue2"=>"Required config value #2 not passed",
												 "present"=>"This value is present and no log should be output."));
		$_test->assertFalse($result);
	}
}
