<?php
/**
 * This file is part of Phutler.
 * Please check the file LICENSE for information about the license.
 *
 * @copyright Daniel Haas 2013
 * @author Daniel Haas <daniel@file-factory.de>
 */

namespace Phutler\WebInterface;

use Monolog\Logger;
use React\EventLoop\LoopInterface;
use React\Http\Request;
use React\Http\Response;

/**
 * The Webinterface provides a simple webinterface to view the status of Phutler.
 * It uses ratchet for a live view on the log.
 */
class Server
{
	private $loop;
	private $config;
	private $log;


	/**
	 * Constructs a new WebInterface for Phutler.
	 *
	 * @param \Phutler\Config $_config The configuration as read from phutler.json
	 * @param \React\EventLoop\LoopInterface $_loop The react loop to use for the webserver.
	 */
	function __construct(Config $_config, LoopInterface $_loop, Logger $_log)
	{
		$this->config=$_config;
		$this->loop=$_loop;
		$this->log=$_log;

		$socket = new \React\Socket\Server($_loop);
		$http = new \React\Http\Server($socket);


		//$http->on('request',array($this,"handleRequest"));
		$this->log->info("WebInterface is listening on ".$this->config->data->WebInterface->port."...");
		$this->app=new \React\Espresso\Application();
		$this->app->get('/favicon.ico', function ($request, $response) {
			$response->writeHead(204);
			$response->end();
		});

		$this->app->get('/',array($this,"index"));
		$this->app->get('/terminate',array($this,"terminate"));

		$stack=new \React\Espresso\Stack($this->app);
		$stack['http'] = $http;
		$stack->listen($this->config->data->WebInterface->port);
	}


	function index(Request $_request, Response $_response)
	{
		$_response->writeHead(200,array("Content-Type"=>"text/html"));
		$_response->write("<html><head><title>".$this->config->data->name." WebInterface</title></head>");
		$_response->write("<body><h1>".$this->config->data->name." WebInterface</h1>");
		$_response->write($this->config->data->name." is running fine!<br/>");
		$_response->write("<a href='/terminate'>Kill me</a>");
		$_response->end("</body></html>");
	}

	function terminate(Request $_request, Response $_response)
	{
		$_response->writeHead(200,array("Content-Type"=>"text/plain"));
		$_response->end("Being stopped...");
		$this->log->info("Being stopped from webinterface. Shutting down in 0.5 sec...");
		$this->loop->addTimer(0.5,array($this->loop,"stop"));
	}



}