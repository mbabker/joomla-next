<?php
/**
 * Joomla! Next Application Path Constants
 *
 * This file is used to create the main path constants used throughout the application.
 *
 * NOTE: If overriding this file, you MUST define the JPATH_ROOT and JPATH_BASE path constants.  For improved
 * operating system compatibility, we recommend you define the paths using `realpath()`, i.e.
 * define('JPATH_ROOT', realpath(__DIR__));
 *
 * @copyright  Copyright (C) Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

define('JPATH_SITE',          JPATH_ROOT);
define('JPATH_CONFIGURATION', JPATH_ROOT);
define('JPATH_ADMINISTRATOR', JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator');
define('JPATH_LIBRARIES',     JPATH_ROOT . DIRECTORY_SEPARATOR . 'libraries');
define('JPATH_PLUGINS',       JPATH_ROOT . DIRECTORY_SEPARATOR . 'plugins');
define('JPATH_INSTALLATION',  JPATH_ROOT . DIRECTORY_SEPARATOR . 'installation');
define('JPATH_MANIFESTS',     JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'manifests');

// The themes and cache directories are application dependent and use the JPATH_BASE constant as the base path lookup
define('JPATH_THEMES',        JPATH_BASE . DIRECTORY_SEPARATOR . 'templates');
define('JPATH_CACHE',         JPATH_BASE . DIRECTORY_SEPARATOR . 'cache');
