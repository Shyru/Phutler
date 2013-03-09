<?php
/**
 * This file is part of Phutler.
 * Please check the file LICENSE for information about the license.
 *
 * @copyright Daniel Haas 2013
 * @author Daniel Haas <daniel@file-factory.de>
 */

namespace Phutler\WebInterface;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;


/**
 * Please add documentation for WebSocketServer!
 */
class WebSocketServer implements MessageComponentInterface
{
	private $log;
	private $clients;

	function __construct(\Monolog\Logger $_log)
	{
		$this->log=$_log;
		$this->clients=new \SplObjectStorage();
	}

	/**
	 * When a new connection is opened it will be passed to this method
	 * @param \Ratchet\ConnectionInterface $conn The socket/connection that just connected to your application
	 * @throws \Exception
	 */
	function onOpen(ConnectionInterface $conn)
	{
		$this->clients->attach($conn);
		$this->log->debug("Got incoming connection!");
		$conn->send("Welcome!");
		$conn->send("Flushing last log entries:");
	}

	/**
	 * This is called before or after a socket is closed (depends on how it's closed).  SendMessage to $conn will not result in an error if it has already been closed.
	 * @param \Ratchet\ConnectionInterface $conn The socket/connection that is closing/closed
	 * @throws \Exception
	 */
	function onClose(ConnectionInterface $conn)
	{
		$this->clients->detach($conn);
	}

	/**
	 * If there is an error with one of the sockets, or somewhere in the application where an Exception is thrown,
	 * the Exception is sent back down the stack, handled by the Server and bubbled back up the application through this method
	 * @param \Ratchet\ConnectionInterface $conn
	 * @param \Exception
	 * @throws \Exception
	 */
	function onError(ConnectionInterface $conn, \Exception $e)
	{
		// TODO: Implement onError() method.
	}

	/**
	 * Triggered when a client sends data through the socket
	 * @param \Ratchet\ConnectionInterface $from The socket/connection that sent the message to your application
	 * @param string $msg The message received
	 * @throws \Exception
	 */
	function onMessage(ConnectionInterface $from, $msg)
	{
		// TODO: Implement onMessage() method.
	}
}