<?php
/**
 * This file is part of Phutler.
 * Please check the file LICENSE for information about the license.
 *
 * @copyright Daniel Haas 2013
 * @author Daniel Haas <daniel@file-factory.de>
 */



class ProxyTest extends \PHPUnit_Framework_TestCase
{
	function testSet()
	{
		$persistorMock=$this->getMock("Phutler\\Persistence\\Persistor");
		$persistorMock->expects($this->once())
						->method('set')
						->with("blub:key","val");
		$proxy=new \Phutler\Persistence\Proxy($persistorMock,"blub:");
		$proxy->set("key","val");
	}

	function testGet()
	{
		$persistorMock=$this->getMock("Phutler\\Persistence\\Persistor");
		$persistorMock->expects($this->once())
			->method('get')
			->with("blub:key");
		$proxy=new \Phutler\Persistence\Proxy($persistorMock,"blub:");
		$proxy->get("key");
	}

	function testRemove()
	{
		$persistorMock=$this->getMock("Phutler\\Persistence\\Persistor");
		$persistorMock->expects($this->once())
			->method('remove')
			->with("blub:key");
		$proxy=new \Phutler\Persistence\Proxy($persistorMock,"blub:");
		$proxy->remove("key");
	}
}
