<?php
/**
 * This file is part of Phutler.
 * Please check the file LICENSE for information about the license.
 *
 * @copyright Daniel Haas 2013
 * @author Daniel Haas <daniel@file-factory.de>
 */


use Monolog\Logger;

class LogHandlerTest extends \PHPUnit_Framework_TestCase
{

	function testRegisterCallback()
	{
		$logHandler=new \Phutler\WebInterface\LogHandler();
		$data=new stdClass();
		$data->records=array();
		$logHandler->registerCallback(function($_message) use ($data)
		{
			$data->records[]=$_message;
		});

		$now=new DateTime();
		$logHandler->handle(array("level"=>Logger::DEBUG,"level_name"=>"DEBUG","channel"=>"blub","datetime"=>$now,"extra"=>array(),"message"=>"Message","context"=>array()));
		$this->assertEquals(1,count($data->records));
		$this->assertEquals("[".$now->format("Y-m-d H:i:s")."] blub.DEBUG: Message [] []\n",$data->records[0]);
	}

	function testGetBuffer()
	{
		//set a buffer size of 3
		$logHandler=new \Phutler\WebInterface\LogHandler(3);

		$now=new DateTime();
		$logHandler->handle(array("level"=>Logger::DEBUG,"level_name"=>"DEBUG","channel"=>"blub","datetime"=>$now,"extra"=>array(),"message"=>"Message1","context"=>array()));
		$logHandler->handle(array("level"=>Logger::DEBUG,"level_name"=>"DEBUG","channel"=>"blub","datetime"=>$now,"extra"=>array(),"message"=>"Message2","context"=>array()));
		$logHandler->handle(array("level"=>Logger::DEBUG,"level_name"=>"DEBUG","channel"=>"blub","datetime"=>$now,"extra"=>array(),"message"=>"Message3","context"=>array()));
		$logHandler->handle(array("level"=>Logger::DEBUG,"level_name"=>"DEBUG","channel"=>"blub","datetime"=>$now,"extra"=>array(),"message"=>"Message4","context"=>array()));

		$buffer=$logHandler->getBuffer();

		$this->assertEquals(3,count($buffer)); //since our buffer size is 3 this must be 3
		//the last 3 messages should be in the buffer
		$this->assertEquals("[".$now->format("Y-m-d H:i:s")."] blub.DEBUG: Message2 [] []\n",$buffer[0]);
		$this->assertEquals("[".$now->format("Y-m-d H:i:s")."] blub.DEBUG: Message3 [] []\n",$buffer[1]);
		$this->assertEquals("[".$now->format("Y-m-d H:i:s")."] blub.DEBUG: Message4 [] []\n",$buffer[2]);
	}

}
