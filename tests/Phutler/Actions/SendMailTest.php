<?php
/**
 * This file is part of Phutler.
 * Please check the file LICENSE for information about the license.
 *
 * @copyright Daniel Haas 2013
 * @author Daniel Haas <daniel@file-factory.de>
 */


use Monolog\Logger;
use Phutler\Phutler;

class SendMailTest extends \PHPUnit_Framework_TestCase
{
	function testSendMail()
	{
		$loop = \React\EventLoop\Factory::create();
		$log=new Logger("test");
		$isolator=$this->getMock("\\Icecave\\Isolator\\Isolator",array("mail"));
		$isolator->expects($this->once())
				->method("mail")
				->with(	$this->equalTo("test@example.com"),
						$this->equalTo("Testmail"),
						$this->equalTo("content"),
						$this->equalTo("From: phutler@example.com\r\nX-Mailer: Phutler/".Phutler::VERSION));
		$mailSender=new \Phutler\Actions\SendMail($loop,$log,(object)array("from"=>"phutler@example.com"),$isolator);
		$mailSender->send("test@example.com","Testmail","content");
	}
}
