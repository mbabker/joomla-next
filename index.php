<?php
/**
 * Joomla! Next Site Application Entry Point
 *
 * This file is the main entry point for the Joomla! application.  From here, we will bootstrap the application
 * and prepare it for execution.  We also do some basic checks during the bootstrap to ensure the application
 * can be used in the deployed environment.
 *
 * @copyright  Copyright (C) Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

/**
 * Define the application's minimum supported PHP version as a constant so it can be referenced within the application.
 */
define('JOOMLA_MINIMUM_PHP', '5.4');

if (version_compare(PHP_VERSION, JOOMLA_MINIMUM_PHP, '<'))
{
	die('Your host needs to use PHP ' . JOOMLA_MINIMUM_PHP . ' or higher to run this version of Joomla!');
}

/**
 * Allows a user to override the application's base path constants.  Check for the existance of an override file within
 * this folder and include it if present.  This override file MUST define '_JDEFINES' to indicate the path constants
 * were overridden and the override file MUST define all constants found in the application's defines as well as set
 * the JPATH_BASE and JPATH_ROOT constants or there may be errors later on.
 */
if (file_exists(__DIR__ . '/defines.php'))
{
	include_once __DIR__ . '/defines.php';
}

if (!defined('_JDEFINES'))
{
	define('JPATH_ROOT', realpath(__DIR__));
	define('JPATH_BASE', JPATH_ROOT);
	require_once JPATH_ROOT . '/libraries/defines.php';
}

/**
 * Bootstrap the application framework
 */
require_once JPATH_LIBRARIES . '/framework.php';

/**
 * Read the application configuration and build the application's DI container.
 */
try
{
	$config = (new Joomla\Service\ConfigurationParser)->getConfig();

	// Set error reporting based on config
	switch ($config->get('error_reporting', 'default'))
	{
		case 'default':
		case '-1':
			break;

		case 'none':
		case '0':
			error_reporting(0);

			break;

		case 'simple':
			error_reporting(E_ERROR | E_WARNING | E_PARSE);
			ini_set('display_errors', 1);

			break;

		case 'maximum':
			error_reporting(E_ALL);
			ini_set('display_errors', 1);

			break;

		case 'development':
			error_reporting(-1);
			ini_set('display_errors', 1);

			break;

		default:
			error_reporting($config->get('error_reporting'));
			ini_set('display_errors', 1);

			break;
	}

	define('JDEBUG', $config->get('debug', false));

	// Mark afterLoad to our profiler if debugging
	if (JDEBUG)
	{
		(new Joomla\Profiler\ProfilerFactory)->mark('application', 'afterLoad');
	}

	$container = (new Joomla\DI\Container)
		->registerServiceProvider(new Joomla\Service\DatabaseProvider($config));
}
catch (\Exception $e)
{
	header('HTTP/1.1 500 Internal Server Error', null, 500);
	header('Content-Type: text/html; charset=utf-8');
	echo 'An error occurred while booting the application: ' . $e->getMessage();

	exit;
}

/**
 * Instantiate and execute our application - If an exception manages to get caught here then something is really out
 * of whack but better this than an uncaught exception
 */
try
{
	if (JDEBUG)
	{
		(new Joomla\Profiler\ProfilerFactory)->mark('application', 'beforeApplicationLaunch');
	}

	$application = new Joomla\Application\SiteApplication(new Joomla\Input\Input, $config);
	$application->setContainer($container);
	$application->execute();

	if (JDEBUG)
	{
		echo '<br />' . (new Joomla\Profiler\ProfilerFactory)->getProfiler('application')->render();
	}
}
catch (\Exception $e)
{
	header('HTTP/1.1 500 Internal Server Error', null, 500);
	header('Content-Type: text/html; charset=utf-8');
	echo 'An error occurred while executing the application: ' . $e->getMessage();
}
