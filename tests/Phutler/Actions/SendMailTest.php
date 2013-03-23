<?php
/**
 * This file is part of Phutler.
 * Please check the file LICENSE for information about the license.
 *
 * @copyright Daniel Haas 2013
 * @author Daniel Haas <daniel@file-factory.de>
 */



class SendMailTest extends \PHPUnit_Framework_TestCase
{
	function testSendMail()
	{
		$loop = \React\EventLoop\Factory::create();
		$isolator=$this->getMock("\\Icecave\\Isolator\\Isolator",array("mail"));
		$isolator->expects($this->once())
				->method("mail")
				->with($this->equalTo("test@example.com"),$this->equalTo("Testmail"),$this->equalTo("content"),$this->equalTo(null));
		$mailSender=new \Phutler\Actions\SendMail($loop,$isolator);
		$mailSender->send("test@example.com","Testmail","content");
	}
}
