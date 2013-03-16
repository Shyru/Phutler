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

/**
 * Base class for all classes that want to implement a Persistor.
 *
 */
abstract class AbstractPersistor implements Persistor
{
	abstract function __construct(\stdClass $_config, LoopInterface $_loop);

}