<?php
/**
 * Joomla! Next Application Platform
 *
 * @copyright  Copyright (C) Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

namespace Joomla\CMS\Application;

use Joomla\Application\AbstractWebApplication;
use Joomla\Application\Web\WebClient;
use Joomla\DI\Container;
use Joomla\DI\ContainerAwareInterface;
use Joomla\DI\ContainerAwareTrait;
use Joomla\Input\Input;
use Joomla\Registry\Registry;

/**
 * Base web application for the CMS application.
 *
 * @since  1.0
 */
abstract class AbstractCMSWebApplication extends AbstractWebApplication implements CMSApplicationInterface, ContainerAwareInterface
{
	use CMSApplicationTrait, ContainerAwareTrait;

	/**
	 * Class constructor.
	 *
	 * @param   Container  $container  The application's DI container
	 * @param   Input      $input      An optional argument to provide dependency injection for the application's
	 *                                 input object.  If the argument is a Input object that object will become
	 *                                 the application's input object, otherwise a default input object is created.
	 * @param   Registry   $config     An optional argument to provide dependency injection for the application's
	 *                                 config object.  If the argument is a Registry object that object will become
	 *                                 the application's config object, otherwise a default config object is created.
	 * @param   WebClient  $client     An optional argument to provide dependency injection for the application's
	 *                                 client object.  If the argument is a Web\WebClient object that object will become
	 *                                 the application's client object, otherwise a default client object is created.
	 *
	 * @since   1.0
	 */
	public function __construct(Container $container, Input $input = null, Registry $config = null, WebClient $client = null)
	{
		$this->setContainer($container);

		// Register this to the DI container now
		$this->getContainer()->protect('Joomla\\CMS\\Application\\AbstractCMSWebApplication', $this)
			->alias('Joomla\\CMS\\Application\\CMSApplicationInterface', 'Joomla\\CMS\\Application\\AbstractCMSWebApplication')
			->alias('Joomla\\Application\\AbstractWebApplication', 'Joomla\\CMS\\Application\\AbstractCMSWebApplication')
			->alias('Joomla\\Application\\AbstractApplication', 'Joomla\\CMS\\Application\\AbstractCMSWebApplication')
			->alias('app', 'Joomla\\CMS\\Application\\AbstractCMSWebApplication');

		parent::__construct($input, $config, $client);

		$this->registerServices();

		// Store the debug value to config based on the JDEBUG flag.
		$this->set('debug', JDEBUG);

		// Enable sessions by default.
		$this->config->def('session', true);

		// Set the session default name.
		$this->config->def('session_name', $this->getName());

		// Create the session if a session name is passed.
		if ($this->config->get('session') !== false)
		{
			$this->loadSession();
		}
	}

	/**
	 * {@inheritdoc}
	 *
	 * @since  1.0
	 */
	public function isCli()
	{
		return false;
	}
}
