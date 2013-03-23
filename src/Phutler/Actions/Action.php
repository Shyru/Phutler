<?php
/**
 * This file is part of Phutler.
 * Please check the file LICENSE for information about the license.
 *
 * @copyright Daniel Haas 2013
 * @author Daniel Haas <daniel@file-factory.de>
 */

namespace Phutler\Actions;



/**
 * Base-class for all actions.
 * This provides the protected member variables \c $loop, \c $config and \c $isolator to action implementations.
 *
 * To improve testability of actions, you should use the isolator instance to make calls to php's built-in php functions.
 * This allows you to assert in unit-tests that your action makes the right php calls when used.
 *
 * If your action needs some form of configuration use $config to access configuration values.
 *
 */
class Action extends \Phutler\Dependency
{


}