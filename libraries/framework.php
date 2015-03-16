<?php
/**
 * Joomla! Next Application Bootstrap
 *
 * This file is used to bootstrap the Joomla! application.
 *
 * @copyright  Copyright (C) Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

/**
 * Checks for existance of a configuration file and whether the installation application exists (production releases only).
 * If the configuration file is not found or is potentially a spoof (based on file size), we redirect to the installation
 * application if it exists.  If the installation application isn't there, all we can do is alert the user.
 */
if (!defined('JOOMLA_INSTALLER'))
{
	if (!file_exists(JPATH_CONFIGURATION . '/configuration.php')
		|| (filesize(JPATH_CONFIGURATION . '/configuration.php') < 10) /*|| file_exists(JPATH_INSTALLATION . '/index.php')*/)
	{
		if (file_exists(JPATH_INSTALLATION . '/index.php'))
		{
			header('Location: ' . substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], 'index.php')) . 'installation/index.php');

			exit;
		}

		header('HTTP/1.1 500 Internal Server Error', null, 500);
		header('Content-Type: text/html; charset=utf-8');
		echo 'No configuration file found and no installation code available. Exiting...';

		exit;
	}
}

/**
 * Verify that Composer is properly configured on the environment.  If running from the git repository, this requires a user
 * has run 'composer install'.  If the autoloader isn't included in the production package, someone really screwed up...
 */
if (!file_exists(JPATH_LIBRARIES . '/vendor/autoload.php'))
{
	header('HTTP/1.1 500 Internal Server Error', null, 500);
	header('Content-Type: text/html; charset=utf-8');
	echo 'Composer is not set up properly, please run "composer install".';

	exit;
}

require JPATH_LIBRARIES . '/vendor/autoload.php';

// Define the application version if not already defined.
if (!defined('JVERSION'))
{
	$jversion = new \Joomla\Version;
	define('JVERSION', $jversion->getShortVersion());
	unset($jversion);
}
