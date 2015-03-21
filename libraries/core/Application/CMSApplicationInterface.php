<?php
/**
 * Joomla! Next Application Platform
 *
 * @copyright  Copyright (C) Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

namespace Joomla\CMS\Application;

use Joomla\CMS\User;

/**
 * Interface defining a Joomla! CMS Application class
 *
 * @since  1.0
 */
interface CMSApplicationInterface
{
	/**
	 * Enqueue a system message.
	 *
	 * @param   string  $msg   The message to enqueue.
	 * @param   string  $type  The message type.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function enqueueMessage($msg, $type = 'message');

	/**
	 * Get the system message queue.
	 *
	 * @return  array  The system message queue.
	 *
	 * @since   1.0
	 */
	public function getMessageQueue();

	/**
	 * Gets the name of the current running application.
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	public function getName();

	/**
	 * Initialise the application.
	 *
	 * @param   array  $options  An optional associative array of configuration settings.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function initialiseApp($options = array());

	/**
	 * Loads a User object to the application as the active identity.
	 *
	 * @param   User  $user  An optional User object.
	 *
	 * @return  $this
	 *
	 * @since   1.0
	 */
	public function loadIdentity(User $user = null);
}
