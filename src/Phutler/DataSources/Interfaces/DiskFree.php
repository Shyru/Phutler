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
 * Describes a data-source that provides data about the free disk space of disks.
 *
 */
interface DiskFree
{
	/**
	 * Returns the number of free bytes for the given drive/path.
	 *
	 * @param string $_path The path to retrieve the free disk-space for
	 * @return int The number of bytes
	 */
	function getFreeDiskBytes($_path);

	/**
	 * Returns the amount of free disk-space for the given drive/path in percent.
	 *
	 * @param string $_path The path to retrieve the free disk-space for
	 * @return float The percentage of free disk space for the given path.
	 */
	function getFreeDiskPercent($_path);
}
