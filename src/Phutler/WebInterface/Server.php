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
use Phutler\Config;
use Ratchet\Server\IoServer;
use React\EventLoop\LoopInterface;
use Phluid\Request;
use Phluid\Response;

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
	function __construct(Config $_config, LoopInterface $_loop, Logger $_log, LogHandler $_logHandler)
	{
		$this->config=$_config;
		$this->loop=$_loop;
		$this->log=$_log;

		$socket = new \React\Socket\Server($_loop);
		$http = new \React\Http\Server($socket);


		$this->log->info("Listening on ".$this->config->data->WebInterface->port."...");
		$this->app=new PhluidApp($_loop);
		$this->app->inject( new \Phluid\Middleware\StaticFiles( __DIR__ . '/static' ) );
		$that=$this;
		$this->app->inject( function(Request $_request, Response $_response, $next ) use ($that) {
			$_response->setHeader('X-Powered-By', 'Phutler');
			$_request->phutlerName=$that->config->data->name;
			$next();
		} );
		$this->app->get('/favicon.ico', function (Request $_request, Response $_response) {
			$_response->writeHead(204);
			$_response->end();
		});


		$this->app->get('/',array($this,"index"));
		$this->app->get('/terminate',array($this,"terminate"));
		$this->app->get('/showLog',array($this,"showLog"));

		$this->app->listen($this->config->data->WebInterface->port);


		//initialize websocket server:
		$webSocket=new \React\Socket\Server($_loop);
		$this->websocketServer = new \Ratchet\WebSocket\WsServer(new WebSocketServer($_log,$_logHandler),$webSocket,$_loop);
		$this->websocketServer->disableVersion("Hixie76");
		$ioServer = new \Ratchet\Server\IoServer($this->websocketServer,$webSocket,$_loop);

		$webSocket->listen($this->config->data->WebInterface->port+1);
	}


	function index(Request $_request, Response $_response)
	{
		$_response->render("index",array("phutlerName"=>$this->config->data->name));
	}

	function terminate(Request $_request, Response $_response)
	{
		$_response->writeHead(200,array("Content-Type"=>"text/plain"));
		$_response->end("Being stopped...");
		$this->log->info("Being stopped from webinterface. Shutting down in 0.5 sec...");
		$this->loop->addTimer(0.5,array($this->loop,"stop"));
	}

	function showLog(Request $_request, Response $_response)
	{
		$_response->render("showLog",array("phutlerName"=>$this->config->data->name,"websocketPort"=>$this->config->data->WebInterface->port+1));
	}



}