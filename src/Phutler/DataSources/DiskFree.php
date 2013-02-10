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
 * Default implementation of the DiskFree Interface.
 */
class DiskFree extends DataSource implements Interfaces\DiskFree
{

	/**
	 * Returns the number of free bytes for the given drive/path.
	 *
	 * @param string $_path The path to retrieve the free disk-space for
	 * @return int The number of bytes
	 */
	function getFreeDiskBytes($_path)
	{
		return disk_free_space($_path);
	}

	/**
	 * Returns the amount of free disk-space for the given drive/path in percent.
	 *
	 * @param string $_path The path to retrieve the free disk-space for
	 * @return float The percentage of free disk space for the given path.
	 */
	function getFreeDiskPercent($_path)
	{
		return disk_free_space($_path)/disk_total_space($_path)*100;
	}
}
