<?php
/**
 * This file is part of Phutler.
 * Please check the file LICENSE for information about the license.
 *
 * @copyright Daniel Haas 2013
 * @author Daniel Haas <daniel@file-factory.de>
 */

namespace Phutler\DataSources\Interfaces;

/**
 * Describes a data-source that provides data about the reachability of a host.
 */
interface Ping
{
	/**
	 * Checks if a given \c $_host is reachable by a ping.
	 *
	 * @param string $_host The host that should be pinged.
	 * @return boolean True if the host is pingable, false otherwise.
	 */
	function isPingable($_host);
}
