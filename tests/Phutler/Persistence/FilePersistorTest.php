<?php
/**
 * This file is part of Phutler.
 * Please check the file LICENSE for information about the license.
 *
 * @copyright Daniel Haas 2013
 * @author Daniel Haas <daniel@file-factory.de>
 */


use Phutler\Config;
use Phutler\Persistence\FilePersistor;

class FilePersistorTest extends \PHPUnit_Framework_TestCase
{
	function testReadNonExisting()
	{
		//make sure we start fresh:
		@unlink("testfile.state");

		$loop=\React\EventLoop\Factory::create();
		$persistor=new FilePersistor((object)array("filePath"=>"testfile.state"),$loop);

		$promise=$persistor->get("myvalue");
		$this->assertInstanceOf("\\React\\Promise\\PromiseInterface",$promise);
		$resolveResult=(object)array("result"=>null);
		$promise->then(function() use ($resolveResult){
			$resolveResult->result=true;
		},function() use ($resolveResult){
			$resolveResult->result=false;
		});

		$loop->tick();
		$loop->tick();
		$loop->tick();
		$loop->tick();


		$this->assertSame(false,$resolveResult->result);
	}

	function testSet()
	{
		$loop=\React\EventLoop\Factory::create();
		$persistor=new FilePersistor((object)array("filePath"=>"testfile.state"),$loop);

		//let the persistor load its data
		$loop->tick();
		$loop->tick();

		$persistor->set("persistor","rocks!");
		$loop->tick();
		$this->assertEquals(true,file_exists("testfile.state"));
		$this->assertGreaterThan(0,filesize("testfile.state"));
	}

	function testWrittenData()
	{
		$loop=\React\EventLoop\Factory::create();
		$persistor=new FilePersistor((object)array("filePath"=>"testfile.state"),$loop);

		$promise=$persistor->get("persistor");
		$this->assertInstanceOf("\\React\\Promise\\PromiseInterface",$promise);
		$resolveResult=(object)array("result"=>null);
		$promise->then(function($value) use ($resolveResult){
			$resolveResult->result=$value;
		},function() use ($resolveResult){
			$resolveResult->result=false;
		});

		$loop->tick();
		$loop->tick();
		$loop->tick();
		$loop->tick();
		$this->assertEquals("rocks!",$resolveResult->result);

	}

	function testRemove()
	{
		$loop=\React\EventLoop\Factory::create();
		$persistor=new FilePersistor((object)array("filePath"=>"testfile.state"),$loop);

		$promise=$persistor->remove("persistor");
		$this->assertInstanceOf("\\React\\Promise\\PromiseInterface",$promise);
		$resolveResult=(object)array("result"=>null);
		$promise->then(function($value) use ($resolveResult){
			$resolveResult->result=$value;
		},function() use ($resolveResult){
			$resolveResult->result=false;
		});

		$loop->tick();
		$loop->tick();
		$loop->tick();
		$loop->tick();
		$this->assertEquals(true,$resolveResult->result);
	}

	function testRemoved()
	{
		$loop=\React\EventLoop\Factory::create();
		$persistor=new FilePersistor((object)array("filePath"=>"testfile.state"),$loop);

		$promise=$persistor->get("persistor");
		$this->assertInstanceOf("\\React\\Promise\\PromiseInterface",$promise);
		$resolveResult=(object)array("result"=>null);
		$promise->then(function() use ($resolveResult){
			$resolveResult->result=true;
		},function() use ($resolveResult){
			$resolveResult->result=false;
		});

		$loop->tick();
		$loop->tick();
		$loop->tick();
		$loop->tick();


		$this->assertSame(false,$resolveResult->result);

		unlink("testfile.state");
	}

}
