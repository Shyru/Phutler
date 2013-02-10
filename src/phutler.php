<?php

/**
 * This file is part of Phutler.
 * Please check the file LICENSE for information about the license.
 *
 * @copyright Daniel Haas 2013
 * @author Daniel Haas <daniel@file-factory.de>
 */


require_once("autoload.php");
require_once(__DIR__."/../vendor/autoload.php");

use Phutler\Phutler;


if ($argc<=1)
{
	echo "This is Phutler version ".Phutler::VERSION.". :-)\n";
	echo "You must pass the path to a valid phutler.json config file as first parameter!\n";
}
else
{
	$configPath=$argv[1];

	if (is_readable($configPath))
	{
		echo "Reading config from '$configPath'...\n";
		$config=json_decode(file_get_contents($configPath));
		if ($config)
		{
			$phutler=new Phutler($config,dirname($configPath));
			$phutler->run();
		}
		else echo "Cannot parse config file '$configPath', it looks like it is no valid json :-(";
	}
	else
	{
		echo "Cannot read config file '$configPath'!";
	}


}


