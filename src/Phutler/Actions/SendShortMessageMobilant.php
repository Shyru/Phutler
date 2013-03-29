<?php
/**
 * This file is part of Phutler.
 * Please check the file LICENSE for information about the license.
 *
 * @copyright Daniel Haas 2013
 * @author Daniel Haas <daniel@file-factory.de>
 */

namespace Phutler\Actions;


use React\Promise\Deferred;

/**
 * Implementation of the SendShortMessage interface that uses Mobilant to do the heavy lifting.
 * It needs the following config values to work correctly:
 *  - apiKey: The key to identify yourself at the gateway of mobilant
 *  - defaultSender: The default sender for the message. Note: Tasks may override this!
 *  - route: The route to use when sending messages. Allowed values: 'lowcost', 'lowcostplus', 'direct', 'directplus'
 */
class SendShortMessageMobilant extends Action implements Interfaces\SendShortMessage
{
	private $dnsResolver;

	function checkConfig()
	{
		return $this->requireConfigValues(array("apiKey"=>"No apiKey specified for SendShortMessageMobilant!",
												"defaultSender"=>"No defaultSender specified for SendShortMessageMobilant!",
												"route"=>"No route specified for SendShortMessageMobilant!"));
	}

	/**
	 * Sends a short message to the given \c $_receiver.
	 *
	 * @param string $_receiver The receiver that should receive the message.
	 * @param string $_message The message that should be sent.
	 * @param string $_sender The sender that sends this message.
	 * @return \React\Promise\PromiseInterface A promise that can be used to check if sending the message worked or not.
	 */
	function send($_receiver, $_message, $_sender=null)
	{
		if (!$this->dnsResolver)
		{
			$dnsResolverFactory = new \React\Dns\Resolver\Factory();
			$this->dnsResolver = $dnsResolverFactory->createCached('8.8.8.8', $this->loop);
		}
		$sender=$_sender;
		if (!$sender) $sender=$this->config->data->defaultSender;

		$factory = new \React\HttpClient\Factory();
		$client = $factory->create($this->loop, $this->dnsResolver);
		$parameters=array("key"=>$this->config->data->apiKey,
						  "message"=>$_message,
						  "to"=>$_receiver,
						  "from"=>$sender,
						  "route"=>$this->config->data->route);

		$request = $client->request('GET', 'http://gw.mobilant.net/'.http_build_query($parameters));
		$fullResponse="";
		$deferred=new Deferred();

		$request->on('response', function ($response) use ($fullResponse,$deferred) {
			$response->on('data', function ($data) use ($fullResponse) {
				$fullResponse.=$data;
			});
			$response->on("end",function($_error) use ($fullResponse,$deferred) {
				if (!$_error && $fullResponse=="100")
				{ //all went well!
					$deferred->resolve(true);
				}
				else $deferred->reject("Response was no success!");
			});
			$response->on("error",function() use ($deferred){
				$deferred->reject("Error with response!");
			});
		});
		$request->on('error',function() use ($deferred){
			$deferred->reject("Error with http-request!");
		});
		$request->end();
		return $deferred->promise();
	}
}