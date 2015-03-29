<?php
/**
 * Joomla! Next Application Platform
 *
 * @copyright  Copyright (C) Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

namespace Joomla\CMS\Application;

use Joomla\CMS\Profiler\ProfilerFactory;
use Joomla\CMS\User;
use Joomla\Session\Session;

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
			ProfilerFactory::getProfiler('application')->mark('afterExecute');
		}
	}

	/**
	 * {@inheritdoc}
	 *
	 * @since  1.0
	 */
	public function getName()
	{
		return 'site';
	}

	/**
	 * {@inheritdoc}
	 *
	 * @since  1.0
	 */
	public function initialiseApp($options = [])
	{
		// TODO: Implement initialiseApp() method.
	}

	/**
	 * {@inheritdoc}
	 *
	 * @since  1.0
	 */
	public function loadIdentity(User $user = null)
	{
		// TODO: Implement loadIdentity() method.
	}

	/**
	 * {@inheritdoc}
	 *
	 * @since  1.0
	 */
	public function loadSession(Session $session = null)
	{
		// TODO: Implement loadSession() method.
	}
}
