<?php
/**
 * This file is part of Phutler.
 * Please check the file LICENSE for information about the license.
 *
 * @copyright Daniel Haas 2013
 * @author Daniel Haas <daniel@file-factory.de>
 */

class DiskFreeTest extends PHPUnit_Framework_TestCase
{
	function testGetFreeDiskBytes()
	{
		$loop = \React\EventLoop\Factory::create();
		$diskFreeDataSource=new \Phutler\DataSources\DiskFree($loop);
		if (is_dir("C:\\"))
		{ //we are on windows
			$this->assertEquals(disk_free_space("C:\\"),$diskFreeDataSource->getFreeDiskBytes("C:\\"));
		}
		else
		{ //assume unix style os
			$this->assertEquals(disk_free_space("/"),$diskFreeDataSource->getFreeDiskBytes("/"));
		}
	}

	function testGetFreeDiskPercent()
	{
		$loop = \React\EventLoop\Factory::create();
		$diskFreeDataSource=new \Phutler\DataSources\DiskFree($loop);
		if (is_dir("C:\\"))
		{ //we are on windows
			$path="C:\\";
		}
		else
		{ //assume unix style os
			$path="/";
		}
		$diskFreePercent=disk_free_space($path)/disk_total_space($path)*100;
		$this->assertEquals($diskFreePercent,$diskFreeDataSource->getFreeDiskPercent($path));
	}
}
