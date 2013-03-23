<?php
/**
 * This file is part of Phutler.
 * Please check the file LICENSE for information about the license.
 *
 * @copyright Daniel Haas 2013
 * @author Daniel Haas <daniel@file-factory.de>
 */

use Monolog\Logger;

class DiskFreeTest extends PHPUnit_Framework_TestCase
{
	function testGetFreeDiskBytes()
	{
		$loop = \React\EventLoop\Factory::create();
		$log=new Logger("test");
		$isolator=$this->getMock("\\Icecave\\Isolator\\Isolator",array("disk_free_space"));
		$isolator->expects($this->once())
			->method("disk_free_space")
			->will($this->returnValue(768));


		$diskFreeDataSource=new \Phutler\DataSources\DiskFree($loop,$log,null,$isolator);
		if (is_dir("C:\\"))
		{ //we are on windows
			$this->assertEquals(768,$diskFreeDataSource->getFreeDiskBytes("C:\\"));
		}
		else
		{ //assume unix style os
			$this->assertEquals(768,$diskFreeDataSource->getFreeDiskBytes("/"));
		}
	}

	function testGetFreeDiskPercent()
	{
		$loop = \React\EventLoop\Factory::create();
		$log=new Logger("test");

		$isolator=$this->getMock("\\Icecave\\Isolator\\Isolator",array("disk_free_space","disk_total_space"));
		$isolator->expects($this->once())
			->method("disk_free_space")
			->will($this->returnValue(500));
		$isolator->expects($this->once())
			->method("disk_total_space")
			->will($this->returnValue(1000));

		$diskFreeDataSource=new \Phutler\DataSources\DiskFree($loop,$log,null,$isolator);
		if (is_dir("C:\\"))
		{ //we are on windows
			$path="C:\\";
		}
		else
		{ //assume unix style os
			$path="/";
		}
		$this->assertEquals(50,$diskFreeDataSource->getFreeDiskPercent($path));
	}
}
