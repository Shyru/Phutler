<?php
/**
 * This file is part of Phutler.
 * Please check the file LICENSE for information about the license.
 *
 * @copyright Daniel Haas 2013
 * @author Daniel Haas <daniel@file-factory.de>
 */



class PingTest extends PHPUnit_Framework_TestCase
{
	function testIsPingable()
	{
		$loop = \React\EventLoop\Factory::create();
		$pingDataSource=new \Phutler\DataSources\Ping($loop);


		$this->assertTrue($pingDataSource->isPingable("127.0.0.1"));
		$this->assertFalse($pingDataSource->isPingable("192.168.100.150"));
	}
}
