<?php
/**
 * This file is part of Phutler.
 * Please check the file LICENSE for information about the license.
 *
 * @copyright Daniel Haas 2013
 * @author Daniel Haas <daniel@file-factory.de>
 */

namespace Phutler;


use Exception;
use Icecave\Isolator\Isolator;
use Monolog\Logger;
use React\EventLoop\LoopInterface;
use stdClass;

/**
 * Base-class for all dependencies of tasks.
 * This implements basic functionality that is needed for both Actions and DataSources.
 *
 * It provides the following protected members for all Actions and DataSources:
 * - $loop The react loop that can be used wherever needed.
 * - $log The Monolog logger instance that can be used for logging
 * - $config A Config instance that holds the configuration of the dependency.
 * - $isolator An isolator instance that should be used when calls to built-in php functions are being made. (This allows to unit-test dependecies)
 */
class Dependency
{
	protected $loop;
	protected $log;
	protected $config;
	protected $isolator;


	/**
	 * Construct a new dependency.
	 *
	 * @param LoopInterface $_loop The loop to use
	 * @param Logger $_log The log to use
	 * @param null $_config The config loaded from phutler.json
	 * @param Isolator $_isolator The isolator instance
	 *
	 * @throws Exception if configuration is incomplete
	 *
	 */
	final function __construct(LoopInterface $_loop, Logger $_log, $_config=null, Isolator $_isolator=null)
	{
		$this->loop=$_loop;
		$this->log=$_log;
		$config=new stdClass();
		if ($_config!==null) $config=$_config;
		$this->config=new Config($config,$this->defaultConfig());
		if (!$this->checkConfig())
		{
			throw new Exception("Configuration of dependency '".get_class($this)."' incomplete!");
		}
		$this->isolator=Isolator::get($_isolator);
	}

	/**
	 * This method should return the default-configuration for this action.
	 * If you action has any default-configuration override this method and return a configuration-object
	 * containing the needed default configuration.
	 *
	 * @return stdClass The default configuration for the action.
	 */
	function defaultConfig()
	{
		return new stdClass();
	}

	/**
	 * Checks if all required config values where set for the action to work.
	 * This should be reimplemented by actions.
	 * If a config value is missing, output some error message in the log and return false.
	 */
	function checkConfig()
	{
		return true;
	}

	/**
	 * Assert that the \c $_requiredConfigValues are present.
	 *
	 * @param array $_requiredConfigValues An associative array of config values that are required. The key is the name of the config value, the value in the array
	 * 				the error message that should be logged if the value is not present.
	 * @return bool True if all config values are present in $this->config, false otherwise
	 */
	protected function requireConfigValues($_requiredConfigValues)
	{
		$allConfigPresent=true;
		foreach ($_requiredConfigValues as $requiredConfigValue => $errorMessage)
		{
			if (!isset($this->config->data->{$requiredConfigValue}))
			{
				$this->log->error($errorMessage);
				$allConfigPresent=false;
			}
		}
		return $allConfigPresent;
	}
}