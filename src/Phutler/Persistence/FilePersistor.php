<?php
/**
 * This file is part of Phutler.
 * Please check the file LICENSE for information about the license.
 *
 * @copyright Daniel Haas 2013
 * @author Daniel Haas <daniel@file-factory.de>
 */

namespace Phutler\Persistence;


use Phutler\Config;
use React\EventLoop\LoopInterface;
use React\Promise\Deferred;
use React\Promise\When;

/**
 * Implements Persistor and provides async file-based
 * storage of state.
 */
class FilePersistor extends AbstractPersistor
{
	private $filePath;
	private $loop;
	private $stateString;
	private $state;
	private $loaded;
	/**
	 * @var Deferred[]
	 */
	private $deferredReads=array();

	function __construct(\stdClass $_config, LoopInterface $_loop)
	{
		$this->loop=$_loop;
		$this->filePath=$_config->filePath;
		$this->loaded=false;
		$this->state=array();
		if (file_exists($this->filePath) && is_readable($this->filePath))
		{
			$stream = new \React\Stream\Stream(fopen($this->filePath, 'r'), $_loop);
			$stream->on('data',array($this,"onFileData"));
			$stream->on('end',array($this,"onFileEnd"));
			$stream->on('error',array($this,"onFileError"));
		}
		else
		{ //there is nothing to load, so we are loaded but have an empty state
			$this->loaded=true;
			$this->state=array();
		}
	}

	function onFileData($_data)
	{
		$this->stateString.=$_data;
	}

	function onFileEnd()
	{
		//we read all of the state file, we can now parse it
		$this->state=unserialize($this->stateString);
		//now resolve all promises we may have given out
		foreach ($this->deferredReads as $key=>$deferred)
		{
			if (!isset($this->state[$key])) $deferred->reject();
			else $deferred->resolve($this->state[$key]);
		}
	}

	function onFileError()
	{
		echo "\nfile error!\n";
	}

	private function persist()
	{
		$state=serialize($this->state);
		file_put_contents($this->filePath,$state);
		return When::resolve(true);


		/* this would be the async version but unfortunately it does not work! :-(
		$fp=fopen($this->filePath,'w+');
		var_dump($fp);
		var_dump(feof($fp));
		$stream=new \React\Stream\Stream($fp, $this->loop);
		$stream->on("error",function(\Exception $e){
			echo "Stream error happened: ";
			echo $e->getMessage();
		});
		$stream->on('close',function(){
			echo "\nstream close!";
		});
		var_dump($stream->isWritable());
		$numWritten=$stream->write($state);
		//$stream->end($state);
		//$stream->close();
		var_dump(strlen($state),$numWritten,$stream->isWritable());

		//$this->stream->
		*/

	}


	/**
	 * @param $_key
	 * @return \React\Promise\PromiseInterface
	 */
	public function get($_key)
	{
		if ($this->loaded)
		{ //ok, we are already loaded, lets check if we have this value
			if (!isset($this->state[$_key])) return When::reject();
			return When::resolve($this->state[$_key]);
		}
		else
		{ //we have not yet loaded from file, return a promise
			if (!isset($this->deferredReads[$_key]))
			{
				$deferred=new Deferred();
				$this->deferredReads[$_key]=$deferred;
			}
			return $this->deferredReads[$_key]->promise();
		}
	}

	public function set($_key, $_value)
	{
		$this->state[$_key]=$_value;
		return $this->persist();
	}

	public function remove($_key)
	{
		unset($this->state[$_key]);
		return $this->persist();
	}


}