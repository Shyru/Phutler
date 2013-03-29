<?php
/**
 * This file is part of Phutler.
 * Please check the file LICENSE for information about the license.
 *
 * @copyright Daniel Haas 2013
 * @author Daniel Haas <daniel@file-factory.de>
 */

namespace Phutler\Actions\Interfaces;

/**
 * Simple interface to give your phutler some personality and let it speak.
 * Implementations could be done in a multitude of ways:
 *  - converting the text to a wave file via an http api, and playing the resulting file
 *  - using tts engines such as simon
 *  - use os level functionality like 'speak'
 *
 */
interface SpeakText
{
	/**
	 * Speaks the given \c $_text. The text must be in the given \c $_language.
	 *
	 * @param string $_text The text that should be spoken.
	 * @param string $_language The language in which \c $_text is. Should be two-letter country code like 'en','de','fr', etc.
	 */
	function speak($_text,$_language);
}