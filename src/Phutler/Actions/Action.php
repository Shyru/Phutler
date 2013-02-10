<?php
/**
 * This file is part of Phutler.
 * Please check the file LICENSE for information about the license.
 *
 * @copyright Daniel Haas 2013
 * @author Daniel Haas <daniel@file-factory.de>
 */

namespace Phutler\Actions;


use React\EventLoop\LoopInterface;

/**
 * Base-class for all actions.
 * This provides the protected member variable $loop to action implementations.
 */
class Action
{
	protected $loop;

	function __construct(LoopInterface $_loop)
	{
		$this->loop=$_loop;
	}
}