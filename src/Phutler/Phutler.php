<?php
/**
 * This file is part of Phutler.
 * Please check the file LICENSE for information about the license.
 *
 * @copyright Daniel Haas 2013
 * @author Daniel Haas <daniel@file-factory.de>
 */

namespace Phutler;


class Phutler
{
	const VERSION="0.0.1";

	private $loop;
	private $config;
	private $webInterface;
	private $tasks;
	private $dir;


	/**
	 * Constructs a new Phutler instance.
	 * Expects to be given \c $_config for configuration data.
	 *
	 * @param \stdClass $_config The configuration read from the phutler.json file as object.
	 * @param string $_dir The path where the config was found. This is needed so that relative paths in the config file work as expected.
	 */
	function __construct($_config,$_dir)
	{
		$defaultConfig='{
                "name":"Phutler",
                "WebInterface":{
                    "port":1337,
                    "enable":"true"
                },
                "implementations":{
                    "Phutler::Actions::Interfaces::SendMail":"Phutler::Actions::SendMail",
                    "Phutler::DataSources::Interfaces::DiskFree":"Phutler::DataSources::DiskFree",
                    "Phutler::DataSources::Interfaces::Ping":"Phutler::DataSources::Ping"
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

	}

	private function initTasks()
	{
		echo "Summoning Tasks...\n";

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
					/** @var $task \Phutler\Tasks\Task */
					$task=new $className($taskConfig,$this->loop);
					if ($this->injectDependencies($task))
					{
						if ($task->init())
						{
							echo "Initialized task '$className'!\n";
							$task->start();
							$this->tasks[]=$task;
						}
						else echo "Failed to init task '$className'!\n";
					}
					else echo "Failed to inject dependencies into task '$className'!\n";
				}
			}
			if (!$found)
			{
				echo "Could not find implementation for task '$className' in any of the following locations:\n";
				foreach ($taskDirs as $taskDir)
				{
					echo " - $taskDir\n";
				}
			}
		}
	}

	private function injectDependencies(Tasks\Task $_task)
	{
		$taskReflector=new \ReflectionClass($_task);
		foreach ($taskReflector->getMethods() as $method)
		{
			if ($method->isPublic())
			{ //we have a public method, check the name
				if (strpos($method->name,"set")===0)
				{ //This is a setter method
					echo "Checking $method->name for needed injections:\n";
					$params=array();
					foreach ($method->getParameters() as $parameter)
					{
						$class=$parameter->getClass();
						if (substr_count($class->name,"Interfaces")>0)
						{ //we found an interface, lets check if we have an implementation for it
							//var_dump($this->config->data->implementations);
							if (isset($this->config->data->implementations->{$class->name}))
							{ //we got an implementation!
								$params[] = new $this->config->data->implementations->{$class->name};
							}
							else
							{
								echo "  Could not find implementation for $class->name! :-(\n";
								return false;
							}
						}
						//echo "Parameter is of class $class\n";
					}
					if (count($params) > 0)
					{
						echo "  Injecting dependencies into $method->name...\n";
						$method->invokeArgs($_task, $params);
					} else echo "  Nothing to do.\n";
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
		echo "This is ".$this->config->data->name." starting up...\n";
		$this->loop = \React\EventLoop\Factory::create();

		$this->initTasks();
		if (count($this->tasks)==0)
		{
			echo "No task could be initialized, exiting!";
			return;
		}

		if ($this->config->data->WebInterface->enable)
		{
			$this->webInterface=new WebInterface($this->config,$this->loop);
		}
		else
		{
			echo "WebInterface disabled, not starting one.\n";
		}



		echo "Now running...\n";
		$this->loop->run();
	}

}
