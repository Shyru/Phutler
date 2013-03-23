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
use Icecave\Isolator\Isolator;

/**
 * Base-class for all actions.
 * This provides the protected member variables \c $loop and \c $isolator to action implementations.
 *
 * To improve testability of actions, you should use the isolator instance to make calls to php's built-in php functions.
 * This allows you to assert in unit-tests that your action makes the right php calls when used.
 *
 */
class Action
{
	protected $loop;
	protected $isolator;

	function __construct(LoopInterface $_loop, Isolator $_isolator=null)
	{
		$this->loop=$_loop;
		$this->isolator=Isolator::get($_isolator);
	}
}