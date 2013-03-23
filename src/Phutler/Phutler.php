<?php
/**
 * This file is part of Phutler.
 * Please check the file LICENSE for information about the license.
 *
 * @copyright Daniel Haas 2013
 * @author Daniel Haas <daniel@file-factory.de>
 */

namespace Phutler;


use Monolog\Handler\StreamHandler;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use Phutler\Persistence\Proxy;

class Phutler
{
	const VERSION="0.0.1";

	private $loop;
	private $config;
	private $webInterface;
	private $webInterfaceLogHandler;
	private $tasks;
	private $dir;
	private $logHandlers;


	/**
	 * Constructs a new Phutler instance.
	 * Expects to be given \c $_config for configuration data.
	 *
	 * @param \stdClass $_config The configuration read from the phutler.json file as object.
	 * @param string $_dir The path where the config was found. This is needed so that relative paths in the config file work as expected.
	 * @throws \Exception When the default configuration is broken. (Should normally not happen)
	 */
	function __construct($_config,$_dir)
	{
		$defaultConfig='{
                "name":"Phutler",
                "WebInterface":{
                    "port":1337,
                    "enable":"true",
                    "logBufferSize":20
                },
                "Persistence":{
                	"filePath":"phutler.state"
                },
                "implementations":{
                    "Phutler::Actions::Interfaces::SendMail":"Phutler::Actions::SendMail",
                    "Phutler::DataSources::Interfaces::DiskFree":"Phutler::DataSources::DiskFree",
                    "Phutler::DataSources::Interfaces::Ping":"Phutler::DataSources::Ping",
                    "Phutler::Persistence::Persistor":"Phutler::Persistence::FilePersistor"
                },
                "dirs":{
                    "tasks":"Tasks"
                },
                "tasks":[]
            }';
		$defaultConfig=json_decode(str_replace("::","\\\\",$defaultConfig));
		if (!$defaultConfig) throw new \Exception("Default configuration is broken, check syntax!!");
		$this->config=new Config($_config,$defaultConfig);
		$this->dir=$_dir;

		$logFormatter=new LogFormatter();
		//create a stdout log handler
		$stdoutHandler=new StreamHandler('php://stdout');
		$stdoutHandler->setFormatter($logFormatter);
		$this->logHandlers[]=$stdoutHandler;

		//create a log handler for the webinterface
		$this->webInterfaceLogHandler=new WebInterface\LogHandler($this->config->data->WebInterface->logBufferSize);
		$this->webInterfaceLogHandler->setFormatter($logFormatter);
		$this->logHandlers[]=$this->webInterfaceLogHandler;

		//now create a base log for our self
		$this->log=$this->initializeLogger(new Logger("phutler"));



	}

	/**
	 * Initialize a logger.
	 * This pushes all necessary handlers to the logger.
	 *
	 * @param Logger $_log The logger that should be initialized
	 * @return Logger The initialized logger
	 */
	private function initializeLogger(Logger $_log)
	{
		foreach ($this->logHandlers as $logHandler)
		{
			$_log->pushHandler($logHandler);
		}
		return $_log;
	}

	private function initTasks()
	{
		$this->log->info("Summoning Tasks...");

		$taskDirs=array($this->dir."/".$this->config->data->dirs->tasks,__DIR__."/Tasks");


		foreach ($this->config->data->tasks as $className=>$taskConfig)
		{
			$found=false;
			foreach ($taskDirs as $taskDir)
			{
				$taskFileName=$taskDir."/".$className.".php";
				if (file_exists($taskFileName))
				{ //we found a class with this name, fine use it
					include_once $taskFileName;
					$found=true;
					$log=new Logger($className);
					$this->initializeLogger($log);
					/** @var $task \Phutler\Tasks\Task */
					$task=new $className($taskConfig,$this->loop,$log,new Proxy($this->persistor,$className.":"));
					if ($this->injectDependencies($task,$taskConfig))
					{
						if ($task->init())
						{
							$this->log->info("Initialized task '$className'!");
							$task->start();
							$this->tasks[]=$task;
						}
						else $this->log->addError("Failed to init task '$className'!");
					}
					else $this->log->addError("Failed to inject dependencies into task '$className'!");
				}
			}
			if (!$found)
			{
				$this->log->addError("Could not find implementation for task '$className' in any of the following locations:");
				foreach ($taskDirs as $taskDir)
				{
					$this->log->debug(" - $taskDir");
				}
			}
		}
	}

	private function injectDependencies(Tasks\Task $_task, $_config)
	{
		$taskReflector=new \ReflectionClass($_task);
		foreach ($taskReflector->getMethods() as $method)
		{
			if ($method->isPublic())
			{ //we have a public method, check the name
				if (strpos($method->name,"set")===0)
				{ //This is a setter method
					$this->log->debug("Checking $method->name for needed injections:");
					$params=array();
					foreach ($method->getParameters() as $parameter)
					{
						$class=$parameter->getClass();
						if (substr_count($class->name,"Interfaces")>0)
						{ //we found an interface, lets check if we have an implementation for it
							//var_dump($this->config->data->implementations);
							$interfaceName=substr($class->name,strrpos($class->name,"\\")+1);
							$implementationName="";
							$config=null;
							if (isset($_config->dependencies) &&
								isset($_config->dependencies->{$interfaceName}) &&
								isset($_config->dependencies->{$interfaceName}->implementation))
							{ //we got a task-specific implementation!
								$implementationName=$_config->dependencies->{$interfaceName}->implementation;
								if (!class_exists($implementationName,true))
								{ //we found no implementation with that name, its probably an implementation from phutler itself, prepend the correct namespace
									$implementationName=substr($class->name,0,strpos($class->name,"Interfaces")).$implementationName;
								}
							}
							else if (isset($this->config->data->implementations->{$class->name}))
							{ //we got an implementation from the default config
								$implementationName=$this->config->data->implementations->{$class->name};
							}
							//check if we have a specific configuration for this dependency
							if (isset($_config->dependencies) &&
								isset($_config->dependencies->{$interfaceName}) &&
								isset($_config->dependencies->{$interfaceName}->config))
							{ //yes we got a config, we must pass this to the dependency
								$config=$_config->dependencies->{$interfaceName}->config;
								$this->log->debug("Config of dependency is: ".json_encode($config));
							}

							if ($implementationName)
							{ //we got an implementation name
								$params[] = new $implementationName($this->loop,$this->initializeLogger(new Logger($implementationName)),$config);
							}
							else
							{
								$this->log->error("  Could not find implementation for $class->name! :-(");
								return false;
							}
						}
						//echo "Parameter is of class $class\n";
					}
					if (count($params) > 0)
					{
						$this->log->debug("  Injecting dependencies into $method->name...");
						$method->invokeArgs($_task, $params);
					} else $this->log->debug("  Nothing to do.");
				}
			}
		}
		return true;
	}

	/**
	 * Runs phutler.
	 * This method will never end, unless phutler is killed using the webinterface.
	 */
	function run()
	{
		$this->log->info("This is ".$this->config->data->name." starting up...");
		$this->loop = \React\EventLoop\Factory::create();

		//setup the persistor:
		$persistorClass=$this->config->data->implementations->{"Phutler\\Persistence\\Persistor"};
		//prepend the dir to the path so that it is relative to the config file
		$this->config->data->Persistence->filePath=$this->dir."/".$this->config->data->Persistence->filePath;
		$this->persistor=new $persistorClass($this->config->data->Persistence,$this->loop);

		$this->initTasks();
		if (count($this->tasks)==0)
		{
			$this->log->critical("No task could be initialized, exiting!");
			return;
		}

		if ($this->config->data->WebInterface->enable)
		{
			$this->webInterface=new WebInterface\Server($this->config,$this->loop,$this->initializeLogger(new Logger("WebInterface")),$this->webInterfaceLogHandler);
		}
		else
		{
			$this->log->info("WebInterface disabled, not starting one.");
		}



		$this->log->info("Now running...");
		$this->loop->run();
	}



}
