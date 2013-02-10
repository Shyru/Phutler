<?php
/**
 * This file is part of Phutler.
 * Please check the file LICENSE for information about the license.
 *
 * @copyright Daniel Haas 2013
 * @author Daniel Haas <daniel@file-factory.de>
 */

namespace Phutler\DataSources;


/**
 * Implements  pings to hosts.
 *
 */
class Ping extends DataSource implements Interfaces\Ping
{

	/**
	 * Checks if a given \c $_host is reachable by a ping.
	 * Unfortunatly currently there is no way to create a stream_socket_client for ICMP so
	 * we cannot use the socket library of react. :-(
	 *
	 * So this a blocking data-source.
	 *
	 * @param string $_host The host that should be pinged.
	 * @return boolean True if the host is pingable, false otherwise.
	 */
	function isPingable($_host)
	{
		/* ICMP ping packet with a pre-calculated checksum */
		$package = "\x08\x00\x7d\x4b\x00\x00\x00\x00PingHost";
		$socket  = socket_create(AF_INET, SOCK_RAW, 1);
		socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array('sec' => 1, 'usec' => 0));
		socket_connect($socket, $_host, null);

		//$ts = microtime(true);
		try {
		socket_send($socket, $package, strLen($package), 0);
		if (socket_read($socket, 255))
			//$result = microtime(true) - $ts;
			return true;
		else    $result = false;
		socket_close($socket);
		}
		catch (\Exception $e)
		{
			$result=false;
		}

		return $result;
	}


}