<?php
/**
 * Joomla! Next Application Platform
 *
 * @copyright  Copyright (C) Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

namespace Joomla\CMS\Renderer\Helper;

use Joomla\CMS\Renderer\RendererFactory;
use Joomla\CMS\Renderer\RendererHelperInterface;

/**
 * Renderer helper for implementing Bootstrap
 *
 * @since  1.0
 */
class BootstrapHelper extends jQueryHelper
{
	/**
	 * {@inheritdoc}
	 *
	 * @since  1.0
	 */
	public function getName()
	{
		return 'bootstrap';
	}

	/**
	 * Method to load the Bootstrap JavaScript framework into the document head
	 *
	 * If debugging mode is on an uncompressed version of Bootstrap is included for easier debugging.
	 *
	 * @param   mixed  $debug  Is debugging mode on? [optional]
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function loadBootstrap($debug = null)
	{
		// Fetch the document
		/** @var \Joomla\CMS\Document\DocumentInterface $document */
		$document = $this->container->get('document');

		// Only process HTML documents
		if ($document->getType() != 'html')
		{
			return;
		}

		// Only load once
		if (!empty(static::$loaded[__METHOD__]))
		{
			return;
		}

		// Load jQuery
		$this->loadJquery();

		// If no debugging value is set, use the configuration setting
		if ($debug === null)
		{
			$debug = JDEBUG;
		}

		// Fetch the script helper
		/** @var ScriptHelper $helper */
		$helper = $this->factory->getHelpers()['script'];

		$helper->addScript('jui/bootstrap.min.js', true, false, true, $debug);

		static::$loaded[__METHOD__] = true;
	}

	/**
	 * Loads the Bootstrap CSS framework
	 *
	 * @param   boolean  $includeMainCss  If true, main bootstrap.css files are loaded
	 * @param   string   $direction       rtl or ltr direction. If empty, ltr is assumed
	 * @param   array    $attribs         Optional array of attributes to be passed to the stylesheet helper
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function loadCss($includeMainCss = true, $direction = 'ltr', $attribs = [])
	{
		// Fetch the document
		/** @var \Joomla\CMS\Document\DocumentInterface $document */
		$document = $this->container->get('document');

		// Only process HTML documents
		if ($document->getType() != 'html')
		{
			return;
		}

		// Fetch the stylesheet helper
		/** @var StylesheetHelper $helper */
		$helper = $this->factory->getHelpers()['stylesheet'];

		// Load Bootstrap main CSS
		if ($includeMainCss)
		{
			$helper->addStylesheet('jui/bootstrap.min.css', $attribs, true);
			$helper->addStylesheet('jui/bootstrap-responsive.min.css', $attribs, true);
			$helper->addStylesheet('jui/bootstrap-extended.css', $attribs, true);
		}

		// Load Bootstrap RTL CSS
		if ($direction === 'rtl')
		{
			$helper->addStylesheet('jui/bootstrap-rtl.css', $attribs, true);
		}
	}
}
