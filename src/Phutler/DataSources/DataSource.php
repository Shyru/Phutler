<?php
/**
 * This file is part of Phutler.
 * Please check the file LICENSE for information about the license.
 *
 * @copyright Daniel Haas 2013
 * @author Daniel Haas <daniel@file-factory.de>
 */

namespace Phutler\DataSources;

use Phutler\Config;
use React\EventLoop\LoopInterface;


/**
 * Base-class for all data-sources.
 * This provides the protected member variable $loop and $config to data-source implementations.
 *
 */
class DataSource extends \Phutler\Dependency
{

}