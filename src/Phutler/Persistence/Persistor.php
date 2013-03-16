<?php
/**
 * This file is part of Phutler.
 * Please check the file LICENSE for information about the license.
 *
 * @copyright Daniel Haas 2013
 * @author Daniel Haas <daniel@file-factory.de>
 */

namespace Phutler\Persistence;


/**
 * The Persistor describes and interface that can be used
 * to persist data between phutler restarts.
 *
 * @package Phutler\Persistence
 */
interface Persistor
{

	/**
	 * @param $_key
	 * @return \React\Promise\PromiseInterface
	 */
	public function get($_key);

	/**
	 * @param string $_key The key where the value should be saved
	 * @param mixed $_value The value that should be saved.
	 * @return \React\Promise\PromiseInterface
	 */
	public function set($_key, $_value);

	/**
	 * @param $_key
	 * @return \React\Promise\PromiseInterface
	 */
	public function remove($_key);
}