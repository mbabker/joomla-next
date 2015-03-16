<?php
/**
 * Joomla! Next Application Platform
 *
 * @copyright  Copyright (C) Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

namespace Joomla\Application;

use Joomla\DI\ContainerAwareInterface;
use Joomla\DI\ContainerAwareTrait;

/**
 * Base web application for the CMS application.
 *
 * @since  1.0
 */
abstract class AbstractCMSWebApplication extends AbstractWebApplication implements ContainerAwareInterface
{
	use ContainerAwareTrait;
}
