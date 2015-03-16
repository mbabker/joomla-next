<?php
/**
 * Joomla! Next Application Platform
 *
 * @copyright  Copyright (C) Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

namespace Joomla\Application;

use Joomla\Profiler\ProfilerFactory;

/**
 * CMS site application class.
 *
 * @since  1.0
 */
final class SiteApplication extends AbstractCMSWebApplication
{
	/**
	 * Method to run the application routines.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function doExecute()
	{
		$this->setBody('Hello World!');

		if (JDEBUG)
		{
			(new ProfilerFactory)->mark('application', 'afterExecute');
		}
	}
}
