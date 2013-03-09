<?php
/**
 * This file is part of Phutler.
 * Please check the file LICENSE for information about the license.
 *
 * @copyright Daniel Haas 2013
 * @author Daniel Haas <daniel@file-factory.de>
 */

namespace Phutler\WebInterface;


/**
 * Please add documentation for PhluidApp!
 */
class PhluidApp extends \Phluid\App
{

	function __construct($_loop)
	{
		$this->loop=$_loop;
		parent::__construct(array("view_path"=>__DIR__."/views/"));
	}

	public function createServer( \React\Http\ServerInterface $http = null ){
		if ( $http === null ) {
			$this->socket = $socket = new \React\Socket\Server( $this->loop );
			$this->http = $http = new \React\Http\Server( $socket, $this->loop );
		}
		$http->on( 'request', function( $http_request, $http_response ){
			$app = $this;
			$request = new \Phluid\Request( $http_request );
			$response = new \Phluid\Response( $http_response, $request );
			$app( $request, $response );

		});
		return $this;
	}

	public function listen( $port, $host = '127.0.0.1' ){
		if ( !$this->http ) {
			$this->createServer();
		}
		$this->socket->listen( $port, $host );
		return $this;
	}
}