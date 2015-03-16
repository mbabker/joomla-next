<?php
/**
 * Joomla! Next Installation Application
 *
 * @copyright  Copyright (C) Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

namespace Installation;

use Joomla\Application\AbstractWebApplication;

/**
 * Application for the CMS installation application.
 *
 * @since  1.0
 */
final class Application extends AbstractWebApplication
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
	}
}
