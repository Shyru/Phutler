<?php
/**
 * This file is part of Phutler.
 * Please check the file LICENSE for information about the license.
 *
 * @copyright Daniel Haas 2013
 * @author Daniel Haas <daniel@file-factory.de>
 */


use Monolog\Logger;

class PingTest extends PHPUnit_Framework_TestCase
{
	function testIsPingable()
	{
		global $argv;
		//unfortunatly the ping-data source needs root/admin-rights so we cannot execute it in a travis environment or from PhpStorm
		if (getenv("TRAVIS")==true || substr_count(implode($argv," "),"ide-phpunit.php")>0) $this->markTestSkipped("Pingtest needs admin/root rights!");

		$loop = \React\EventLoop\Factory::create();
		$log=new Logger("test");
		$pingDataSource=new \Phutler\DataSources\Ping($loop,$log);


		$this->assertTrue($pingDataSource->isPingable("127.0.0.1"));
		$this->assertFalse($pingDataSource->isPingable("192.168.100.150"));
	}
}
