<?php
/**
 * Joomla! Next Application Platform
 *
 * @copyright  Copyright (C) Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

namespace Joomla\Service;

use Joomla\Registry\Registry;

/**
 * Configuration service provider.
 *
 * @since  1.0
 */
class ConfigurationParser
{
	/**
	 * Configuration instance.
	 *
	 * @var    Registry
	 * @since  1.0
	 */
	private $config;

	/**
	 * Constructor.
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public function __construct()
	{
		// Set the configuration file path for the application.
		$file = JPATH_CONFIGURATION . '/configuration.php';

		// Verify the configuration exists and is readable.
		if (!is_readable($file))
		{
			throw new \RuntimeException('Configuration file does not exist or is unreadable.');
		}

		// As the Registry package lacks reading from a PHP file right now, we must parse this ourselves.  Sad panda.
		require_once $file;

		$jConfig      = new \JConfig;
		$this->config = new Registry;

		foreach (get_class_vars('\\JConfig') as $key => $value)
		{
			$this->config->set($key, $value);
		}

		// Set the language.basedir key to our app root
		$this->config->set('language.basedir', JPATH_BASE);
	}

	/**
	 * Retrieve the configuration object
	 *
	 * @return  Registry
	 *
	 * @since   1.0
	 */
	public function getConfig()
	{
		return $this->config;
	}
}
