<?php
/**
 * This file is part of Phutler.
 * Please check the file LICENSE for information about the license.
 *
 * @copyright Daniel Haas 2013
 * @author Daniel Haas <daniel@file-factory.de>
 */

namespace Phutler\Actions;


use Phutler\Phutler;

/**
 * Basic implementation of the SendMail interface.
 * This currently just uses php's mail() method.
 *
 *
 */
class SendMail extends Action implements Interfaces\SendMail
{
	private $headers;

	/**
	 * Sends a mail with \c $_subject and \c $_body to \c $_to.
	 *
	 * @param string $_to The adress where the mail should be sent to.
	 * @param string $_subject The subject of the mail that should be sent.
	 * @param string $_body The body (text) of the mail
	 * @param array $_headers The headers that should be sent along with the mail. Note: This should be an associative array with all headers. Example:
	 * 				- array("Reply-To"=>"admin@example.com")
	 * @return bool True if sending the mail worked, false otherwise
	 */
	function send($_to, $_subject, $_body, $_headers = array())
	{
		$headers=array_merge($this->headers,$_headers);
		$additionalHeaders=array();
		foreach ($headers as $header=>$value)
		{
			$additionalHeaders[]=$header.": ".$value;
		}
		return $this->isolator->mail($_to,$_subject,$_body,implode("\r\n",$additionalHeaders));
	}

	function checkConfig()
	{

		if (!isset($this->config->data->from))
		{
			$this->log->error("No from specified in options for SendMail action!");
			return false;
		}
		else
		{
			$this->headers["From"]=$this->config->data->from;
			$this->headers["X-Mailer"]="Phutler/".Phutler::VERSION;
			return true;
		}
	}
}