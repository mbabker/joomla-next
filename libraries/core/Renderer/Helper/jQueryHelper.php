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
 * Renderer helper for implementing jQuery
 *
 * @since  1.0
 */
class jQueryHelper implements RendererHelperInterface
{
	use MediaTrait;

	/**
	 * DI Container
	 *
	 * @var    \Joomla\DI\Container
	 * @since  1.0
	 */
	protected $container;

	/**
	 * RendererFactory object
	 *
	 * @var    RendererFactory
	 * @since  1.0
	 */
	protected $factory;

	/**
	 * Constructor
	 *
	 * @since   1.0
	 */
	public function __construct()
	{
		$this->factory   = new RendererFactory;
		$this->container = $this->factory->getContainer();
	}

	/**
	 * {@inheritdoc}
	 *
	 * @since  1.0
	 */
	public function getName()
	{
		return 'jquery';
	}

	/**
	 * Method to load the jQuery JavaScript framework into the document head
	 *
	 * If debugging mode is on an uncompressed version of jQuery is included for easier debugging.
	 *
	 * @param   boolean  $noConflict  True to load jQuery in noConflict mode [optional]
	 * @param   mixed    $debug       Is debugging mode on? [optional]
	 * @param   boolean  $migrate     True to enable the jQuery Migrate plugin
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function loadJquery($noConflict = true, $debug = null, $migrate = true)
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

		// If no debugging value is set, use the configuration setting
		if ($debug === null)
		{
			$debug = JDEBUG;
		}

		// Fetch the script helper
		/** @var ScriptHelper $helper */
		$helper = $this->factory->getHelpers()['script'];

		$helper->addScript('jui/jquery.min.js', true, false, true, $debug);

		// Check if we are loading in noConflict
		if ($noConflict)
		{
			$helper->addScript('jui/jquery-noconflict.js', true, false, true, false);
		}

		// Check if we are loading Migrate
		if ($migrate)
		{
			$helper->addScript('jui/jquery-migrate.min.js', false, true, false, false, $debug);
		}

		static::$loaded[__METHOD__] = true;
	}
}
