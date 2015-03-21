<?php
/**
 * Joomla! Next Installation Application Entry Point
 *
 * This file is the main entry point for the Joomla! application.  From here, we will bootstrap the application
 * and prepare it for execution.  We also do some basic checks during the bootstrap to ensure the application
 * can be used in the deployed environment.
 *
 * @copyright  Copyright (C) Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

// Hard coded for now
error_reporting(-1);
ini_set('display_errors', 1);

/**
 * Define the application's minimum supported PHP version as a constant so it can be referenced within the application.
 */
define('JOOMLA_MINIMUM_PHP', '5.4');

if (version_compare(PHP_VERSION, JOOMLA_MINIMUM_PHP, '<'))
{
	die('Your host needs to use PHP ' . JOOMLA_MINIMUM_PHP . ' or higher to run this version of Joomla!');
}

/**
 * Internal flag that we are in the installer application
 */
define('JOOMLA_INSTALLER', true);

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
	define('JPATH_ROOT', realpath(dirname(__DIR__)));
	define('JPATH_BASE', realpath(__DIR__));
	require_once JPATH_ROOT . '/libraries/defines.php';
}

/**
 * Check if the application is already installed and redirect to the site app if so
 */
if (file_exists(JPATH_CONFIGURATION . '/configuration.php') && (filesize(JPATH_CONFIGURATION . '/configuration.php') > 10))
{
	header('Location: ../index.php');

	exit;
}

/**
 * Bootstrap the application framework
 */
require_once JPATH_LIBRARIES . '/framework.php';

/**
 * Register the install application to the autoloader
 */
/** @var \Composer\Autoload\ClassLoader $loader */
$loader = include JPATH_LIBRARIES . '/vendor/autoload.php';
$loader->addPsr4('Installation\\', __DIR__ . '/src');

/**
 * We register the JDEBUG constant as it is checked throughout the CMS API.  Additionally, we can use it within the
 * installer as a debugging tool by editing the define to true.
 */
define('JDEBUG', false);

/**
 * Instantiate and execute our application - If an exception manages to get caught here then something is really out
 * of whack but better this than an uncaught exception
 */
try
{
	$container = new Joomla\DI\Container;

	$application = new Installation\Application;
	$application->setContainer($container);
	$application->execute();
}
catch (\Exception $e)
{
	var_dump($e);die;
	header('HTTP/1.1 500 Internal Server Error', null, 500);
	header('Content-Type: text/html; charset=utf-8');
	echo 'An error occurred while executing the application: ' . $e->getMessage();
}
