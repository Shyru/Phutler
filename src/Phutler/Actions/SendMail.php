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
 * Basic implementation of the SendMail interface.
 * This currently just uses php's mail() method.
 *
 *
 */
class SendMail extends Action implements Interfaces\SendMail
{

	/**
	 * Sends a mail with \c $_subject and \c $_body to \c $_to.
	 *
	 * @param string $_to The adress where the mail should be sent to.
	 * @param string $_subject The subject of the mail that should be sent.
	 * @param string $_body The body (text) of the mail
	 * @param string $_headers The headers that should be sent along with the
	 * @return bool True if sending the mail worked, false otherwise
	 */
	function send($_to, $_subject, $_body, $_headers = null)
	{
		return mail($_to,$_subject,$_body,$_headers);
	}
}