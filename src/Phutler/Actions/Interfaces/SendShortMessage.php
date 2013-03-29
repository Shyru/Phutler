<?php
/**
 * This file is part of Phutler.
 * Please check the file LICENSE for information about the license.
 *
 * @copyright Daniel Haas 2013
 * @author Daniel Haas <daniel@file-factory.de>
 */

namespace Phutler\Actions\Interfaces;


interface SendShortMessage
{
	/**
	 * Sends a short message to the given \c $_receiver.
	 *
	 * @param string $_receiver The receiver that should receive the message.
	 * @param string $_message The message that should be sent.
	 * @param string $_sender The sender that sends this message.
	 * @return \React\Promise\PromiseInterface A promise that can be used to check if sending the message worked or not.
	 */
	function send($_receiver,$_message,$_sender);
}