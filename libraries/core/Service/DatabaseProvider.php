<?php
/**
 * Joomla! Next Application Platform
 *
 * @copyright  Copyright (C) Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

namespace Joomla\CMS\Service;

use Joomla\Database\DatabaseDriver;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Registry\Registry;

/**
 * Database service provider.
 *
 * @since  1.0
 */
class DatabaseProvider implements ServiceProviderInterface
{
	/**
	 * Application configuration object.
	 *
	 * @var    Registry
	 * @since  1.0
	 */
	private $config;

	/**
	 * Constructor.
	 *
	 * @param   Registry  $config  Application configuration object.
	 *
	 * @since   1.0
	 */
	public function __construct(Registry $config)
	{
		$this->config = $config;
	}

	/**
	 * Registers the service provider with a DI container.
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  Container  Returns itself to support chaining.
	 *
	 * @since   1.0
	 */
	public function register(Container $container)
	{
		$container->set('Joomla\\Database\\DatabaseDriver',
			function ()
			{
				$options = array(
					'driver' => $this->config->get('dbtype'),
					'host' => $this->config->get('host'),
					'user' => $this->config->get('user'),
					'password' => $this->config->get('password'),
					'database' => $this->config->get('db'),
					'prefix' => $this->config->get('prefix')
				);

				$db = DatabaseDriver::getInstance($options);
				$db->setDebug(JDEBUG);

				return $db;
			}, true, true
		);

		// Alias the database
		$container->alias('db', 'Joomla\\Database\\DatabaseDriver');
	}
}
