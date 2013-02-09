<?php
/**
 * This file is part of Phutler.
 * Please check the file LICENSE for information about the license.
 *
 * @copyright Daniel Haas 2013
 * @author Daniel Haas <daniel@file-factory.de>
 */

namespace Phutler;


/**
 * Holds the configuration of Phutler.
 * For now this only holds the data in a public member and
 * provides some handy methods to merge default configuration
 * data into the config.
 *
 * In the future this may be refactored into an interface providing methods
 * to access certain configuration options and an implementation (or multiple) which
 * reads configuration from a file, database or whatever.
 *
 */
class Config
{
	public $data;

	/**
	 * Constructs a new Config object using configuration data \c $_config.
	 *
	 * @param \stdClass $_config The configuration data that was read from the phutler.json file
	 * @param \stdClass $_defaultConfig The default configuration object
	 */
	public function __construct($_config,$_defaultConfig)
	{
		$this->data=$this->mergeConfig($_defaultConfig,$_config);
	}

	/**
	 * Recursively merges \c $_config into \c $_defaultConfig.
	 *
	 * @param \stdClass $_defaultConfig The default configuration object
	 * @param \stdClass $_config The configuration object. This overwrites any values in $_defaultConfig
	 * @return \stdClass the merged config
	 */
	public function mergeConfig($_defaultConfig, $_config)
	{
		$config=$_defaultConfig;
		$keys = array_keys( (array)$_config );
		foreach ($keys as $key)
		{
			if (is_object($_defaultConfig->$key) && is_object($_config->$key))
			{
				$config->$key = $this->mergeConfig( $_defaultConfig->$key, $_config->$key );
			}
			else
			{
				$config->$key = $_config->$key;
			}
		}
		return $config;
	}


}