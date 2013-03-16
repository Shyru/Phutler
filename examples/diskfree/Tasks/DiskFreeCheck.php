<?php
/**
 * This file is part of Phutler.
 * Please check the file LICENSE for information about the license.
 *
 * @copyright Daniel Haas 2013
 * @author Daniel Haas <daniel@file-factory.de>
 */

/**
 * A simple Task that shows how to implement basic Tasks.
 */
class DiskFreeCheck extends \Phutler\Tasks\Task
{
	/** @var \Phutler\DataSources\Interfaces\DiskFree */
	private $diskFree;
	/** @var \Phutler\Actions\Interfaces\SendMail */
	private $sendMail;

	private $mailSent=array();

	/**
	 * This method is automatically called by Phutler to provide an implementation of the DiskFree data-source.
	 *
	 * @param Phutler\DataSources\Interfaces\DiskFree $_diskFree
	 */
	function setDataSources(\Phutler\DataSources\Interfaces\DiskFree $_diskFree)
	{
		$this->diskFree=$_diskFree;
	}

	/**
	 * This method is automatically called by Phutler to provide an implementation of the SendMail action.
	 *
	 * @param Phutler\Actions\Interfaces\SendMail $_sendMail
	 */
	function setActions(\Phutler\Actions\Interfaces\SendMail $_sendMail)
	{
		$this->sendMail=$_sendMail;
	}

	/**
	 * Checks if the configuration for the Task is sufficient.
	 *
	 * @return bool
	 */
	function init()
	{
		if (count($this->config->data->disks)==0)
		{
			$this->log->addError("You must at least specify one disk to check for the DiskFreeCheck to work!");
			return false;
		}
		if (!isset($this->config->data->email))
		{
			$this->log->addError("You must specify an email for the DiskFreeCheck!");
			return false;
		}
		return true;
	}

	function defaultConfig()
	{
		return json_decode('{
            "disks":[],
            "alertLevel":"10%"
        }');
	}

	/**
	 * Checks disk space every minute and sends a mail if disk space runs low.
	 *
	 */
	function doEveryMinute()
	{
		$this->log->info("Checking disk-space...");
		$alertLevel=$this->config->data->alertLevel;

		foreach ($this->config->data->disks as $disk)
		{
			if ($alertLevel[strlen($alertLevel)-1]=='%')
			{ //check against percentage
				$percentFree=$this->diskFree->getFreeDiskPercent($disk);
				if ($percentFree<(int)$alertLevel && !isset($this->mailSent[$disk]))
				{
					$this->sendMail->send($this->config->data->email,"Disk Free Alert","The Disk '$disk' is more than $alertLevel full!");
					//save that we already sent a mail for this disk, to not re-send the mail every minute
					$this->mailSent["$disk"]=true;
				}
			}
			else
			{ //check against byte value
				$bytesFree=$this->diskFree->getFreeDiskBytes($disk);
				if ($bytesFree<$alertLevel && !isset($this->mailSent[$disk]))
				{
					$this->sendMail->send($this->config->data->email,"Disk Free Alert","The Disk '$disk' has less than $alertLevel free space!");
					//save that we already sent a mail for this disk, to not re-send the mail every minute
					$this->mailSent["$disk"]=true;
				}
			}
		}
	}

	/**
	 * Resets mail-sending
	 */
	function doEveryHour()
	{
		$this->mailSent=array();
	}
}