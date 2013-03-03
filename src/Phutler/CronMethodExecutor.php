<?php
/**
 * This file is part of Phutler.
 * Please check the file LICENSE for information about the license.
 *
 * @copyright Daniel Haas 2013
 * @author Daniel Haas <daniel@file-factory.de>
 */

namespace Phutler;


/**
 * Please add documentation for CronMethodExecutor!
 */
class CronMethodExecutor
{
	/**
	 * @var CronMethod[]
	 */
	private $cronMethods;

	/**
	 * Creates a new CronMethodExecutor for the given \c $_object.
	 *
	 * @param mixed $_object The object that should be analyzed for cron-methods
	 */
	function __construct($_object)
	{
		//initialize methods that should be called with a cron config
		$parser=new \DocBlock\Parser();
		$parser->analyze($_object);
		foreach ($parser->getMethods() as $method)
		{ /** @var $method \DocBlock\Element\MethodElement */
			$cronAnnotations=$method->getAnnotations(array("cron"));
			if (count($cronAnnotations)>0)
			{
				//echo "We have a cron annotation:\n";
				//var_dump($cronAnnotations);
				foreach ($cronAnnotations as $cronAnnotation)
				{
					$cronExpression=\Cron\CronExpression::factory(implode(" ",$cronAnnotation->values));
					if ($cronExpression)
					{
						$this->cronMethods[]=new CronMethod($cronExpression,array($_object,$method->name));
					}
				}
			}
		}
	}

	/**
	 * Executes all cron-methods of the object if necessary.
	 */
	public function executeCronMethods()
	{
		foreach ($this->cronMethods as $cronMethod)
		{
			$cronMethod->executeIfDue();
		}
	}
}