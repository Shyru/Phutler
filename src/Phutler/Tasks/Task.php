<?php
/**
 * This file is part of Phutler.
 * Please check the file LICENSE for information about the license.
 *
 * @copyright Daniel Haas 2013
 * @author Daniel Haas <daniel@file-factory.de>
 */

namespace Phutler\Tasks;


use Cron\CronExpression;
use Phutler\Config;
use Phutler\CronMethodExecutor;
use Phutler\Persistence\Persistor;

/**
 * Base class for all Tasks.
 *
 * To implement your own Task inherit from this class and implement any of the
 * doEvery* methods to do your stuff. You can also use cron-expressions to define when
 * a method should be executed. Simply annotate your method with \@cron and add a cron-statement after it.
 * The method will then be automatically executed when necessary. Supported cron-expressions are documented
 * here: https://github.com/mtdowling/cron-expression.
 *
 * Also implement defaultConfig() if your task needs a default configuration that can be overridden from
 * phutler.json.
 *
 * Implement init() to check if all needed configuration values are present and return true/false to signal
 * if everything could be initialized correctly.
 *
 * Most certainly you will also want to use some data-sources and actions to do your stuff. To get a data-source
 * or action to use just implement a set*() method that expects a data-source or action as parameter. This methods
 * will automatically be called from phutler and you will get the data-sources or actions you need.
 *
 * If you want to log something, use $this->log, this is a readily configured Monolog\Logger instance.
 *
 * If you need to persist some data between phutler restarts use $this->persistor which is an object implementing the
 * Persistor interface. It allows you to easily get() and set() values.
 *
 *
 */
class Task
{
	private $cronMethodExecutor;
	/**
	 * @var \React\EventLoop\LoopInterface
	 * The Event-loop object available for all tasks to schedule any code to be executed regularly.
	 */
	protected $loop;

	/**
	 * @var \Monolog\Logger
	 * The Logger instance to use when you want to log something.
	 */
	protected $log;

	protected $config;

	protected $persistor;

	final function __construct($taskConfig, \React\EventLoop\LoopInterface $_loop, \Monolog\Logger $_log, Persistor $_persistor)
	{

		$this->config=new Config($taskConfig,$this->defaultConfig());
		$this->loop=$_loop;
		$this->log=$_log;
		$this->persistor=$_persistor;
		$this->loop->addPeriodicTimer(1,array($this,"doEverySecond"));
		$this->loop->addPeriodicTimer(60,array($this,"doEveryMinute"));
		$this->loop->addPeriodicTimer(60*60,array($this,"doEveryHour"));

		//initialize a CronMethodExecutor to execute cron-methods when due
		$this->cronMethodExecutor=new CronMethodExecutor($this);
		$this->loop->addPeriodicTimer(60,array($this->cronMethodExecutor,'executeCronMethods'));
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