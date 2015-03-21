<?php
/**
 * Joomla! Next Application Platform
 *
 * @copyright  Copyright (C) Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

namespace Joomla\CMS\Document;

use Joomla\CMS\Uri\Uri;

/**
 * Base implementation of the DocumentRendererInterface
 *
 * @since  1.0
 */
abstract class AbstractDocumentRenderer implements DocumentRendererInterface
{
	/**
	 * The Document object that instantiated the renderer
	 *
	 * @var    DocumentInterface
	 * @since  1.0
	 */
	protected $doc = null;

	/**
	 * Class constructor
	 *
	 * @param   DocumentInterface  $doc  The Document object that instantiated the renderer
	 *
	 * @since   1.0
	 */
	public function __construct(DocumentInterface $doc)
	{
		$this->doc = $doc;
	}

	/**
	 * Convert links in a text from relative to absolute
	 *
	 * @param   string  $text  The text processed
	 *
	 * @return  string   Text with converted links
	 *
	 * @since   1.0
	 */
	protected function relToAbs($text)
	{
		$uri  = Uri::getInstance();
		$base = $uri->base();
		$text = preg_replace("/(href|src)=\"(?!http|ftp|https|mailto|data)([^\"]*)\"/", "$1=\"$base\$2\"", $text);

		return $text;
	}
}
