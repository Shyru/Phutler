<?php
/**
 * This file is part of Phutler.
 * Please check the file LICENSE for information about the license.
 *
 * @copyright Daniel Haas 2013
 * @author Daniel Haas <daniel@file-factory.de>
 */

namespace Phutler;

use React\EventLoop\LoopInterface;
use React\Http\Request;
use React\Http\Response;

/**
 * The Webinterface provides a simple webinterface to view the status of Phutler.
 */
class WebInterface
{
	private $loop;
	private $config;


	/**
	 * Constructs a new WebInterface for Phutler.
	 *
	 * @param \Phutler\Config $_config The configuration as read from phutler.json
	 * @param \React\EventLoop\LoopInterface $_loop The react loop to use for the webserver.
	 */
	function __construct(Config $_config, LoopInterface $_loop)
	{
		$this->config=$_config;
		$this->loop=$_loop;

		$socket = new \React\Socket\Server($_loop);
		$http = new \React\Http\Server($socket);


		$http->on('request',array($this,"handleRequest"));
		echo "WebInterface is listening on ".$this->config->data->WebInterface->port."...\n";
		$socket->listen($this->config->data->WebInterface->port);
	}

	/**
	 * Handles an incoming request.
	 *
	 * @param \React\Http\Request $_request The request that came
	 * @param \React\Http\Response $_response The response that will be sent to the client.
	 */
	function handleRequest(Request $_request, Response $_response)
	{
		if ($_request->getPath()=="/terminate")
		{
			$_response->writeHead(200,array("Content-Type"=>"text/plain"));
			$_response->end("Being stopped...");
			echo "Being stopped from webinterface. Shutting down in 0.5 sec...";
			$this->loop->addTimer(0.5,array($this->loop,"stop"));
		}
		else
		{
			$_response->writeHead(200,array("Content-Type"=>"text/html"));
			$_response->write("<html><head><title>".$this->config->data->name." WebInterface</title></head>");
			$_response->write("<body><h1>".$this->config->data->name." WebInterface</h1>");
			$_response->write($this->config->data->name." is running fine!<br/>");
			$_response->write("<a href='/terminate'>Kill me</a>");
			$_response->end("</body></html>");
		}
	}


}