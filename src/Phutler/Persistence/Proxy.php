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
 * Proxies a Persistor with a prefix.
 */
class Proxy implements Persistor
{
	private $persistor;
	private $prefix;

	/**
	 * Construct a new Proxy.
	 * @param Persistor $_persistor The persistor that should be proxied
	 * @param string $_prefix The prefix which should be prepended to all keys
	 */
	function __construct(Persistor $_persistor, $_prefix)
	{
		$this->persistor=$_persistor;
		$this->prefix=$_prefix;
	}

	/**
	 * @param $_key
	 * @return \React\Promise\PromiseInterface
	 */
	public function get($_key)
	{
		return $this->persistor->get($this->prefix.$_key);
	}

	public function set($_key, $_value)
	{
		return $this->persistor->set($this->prefix.$_key,$_value);
	}

	public function remove($_key)
	{
		return $this->persistor->remove($this->prefix.$_key);
	}
}