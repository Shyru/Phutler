<?php
/**
 * This file is part of Phutler.
 * Please check the file LICENSE for information about the license.
 *
 * @copyright Daniel Haas 2013
 * @author Daniel Haas <daniel@file-factory.de>
 */

namespace Phutler\Tasks;


use Phutler\Config;

/**
 * Base-class for all Tasks.
 *
 * To implement your own Task inherit from this class and implement any of the
 * doEvery* methods to do your stuff.
 *
 * Implement defaultConfig() if your task needs a default configuration that can be overriden from
 * phutler.json.
 *
 * Implement init() to check if all needed configuration values are present and return true/false to signal
 * if everything could be initialized correctly.
 */
class Task
{
	/**
	 * @var \React\EventLoop\LoopInterface
	 * The Event-loop object available for all tasks to schedule any code to be executed regularly.
	 */
	protected $loop;

	protected $config;

	final function __construct($taskConfig, \React\EventLoop\LoopInterface $_loop)
	{

		$this->config=new Config($taskConfig,$this->defaultConfig());
		$this->loop=$_loop;
		$this->loop->addPeriodicTimer(1,array($this,"doEverySecond"));
		$this->loop->addPeriodicTimer(60,array($this,"doEveryMinute"));
		$this->loop->addPeriodicTimer(60*60,array($this,"doEveryHour"));

	}

	/**
	 * Starts the task. This makes sure that all doEvery*() methods are executed
	 * at the beginning of the livecycle.
	 *
	 */
	final function start()
	{
		$this->doEverySecond();
		$this->doEveryMinute();
		$this->doEveryHour();
	}



	/**
	 * Initialization method that can be overridden by tasks to to initialize themselves.
	 *
	 * @return boolean True if initialization was successfull, false otherwise.
	 */
	public function init()
	{
		return true;
	}

	/**
	 * Handy method that can be overridden in tasks to implement stuff that should be executed
	 * every hour.
	 */
	public function doEveryHour()
	{

	}

	/**
	 * Handy method that can be overridden in tasks to implement stuff that should be executed
	 * every minute.
	 */
	public function doEveryMinute()
	{

	}

	/**
	 * Handy method that can be overridden in tasks to implement stuff that should be executed
	 * every second.
	 */
	public function doEverySecond()
	{

	}



	/**
	 * @return \stdClass The default configuration of the task.
	 */
	protected function defaultConfig()
	{
		return new \stdClass;
	}



}