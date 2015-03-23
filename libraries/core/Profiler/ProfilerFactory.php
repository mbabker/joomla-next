<?php
/**
 * Joomla! Next Application Platform
 *
 * @copyright  Copyright (C) Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

namespace Joomla\CMS\Profiler;

use Joomla\Profiler\Profiler;

/**
 * Factory for handling Profiler instances
 *
 * @since  1.0
 */
final class ProfilerFactory
{
	/**
	 * Container for Profiler instances.
	 *
	 * @var    Profiler[]
	 * @since  1.0
	 */
	private static $profilers = [];

	/**
	 * Get a Profiler instance.
	 *
	 * @param   string   $profiler  Name of the profiler to retrieve.
	 * @param   boolean  $create    Flag to create a new Profiler instance if the requested one does not exist.
	 *
	 * @return  Profiler
	 *
	 * @since   1.0
	 * @throws  \RuntimeException if the requested profile does not exist and instructed to not create a new instance.
	 */
	public static function getProfiler($profiler, $create = true)
	{
		if (!isset(self::$profilers[$profiler]))
		{
			if (!$create)
			{
				throw new \RuntimeException('The requested profiler does not exist.');
			}

			self::$profilers[$profiler] = new Profiler($profiler);
		}

		return self::$profilers[$profiler];
	}

	/**
	 * Mark a profile point for the requested profiler.
	 *
	 * @param   string   $profiler  Name of the profiler to add the profile point to.
	 * @param   string   $name      The profile point name.
	 * @param   boolean  $create    Flag to create a new Profiler instance if the requested one does not exist.
	 *
	 * @return  $this
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public function mark($profiler, $name, $create = true)
	{
		self::getProfiler($profiler, $create)->mark($name);

		return $this;
	}

	/**
	 * Register a Profiler instance.
	 *
	 * @param   string    $name      Name of the profiler to register.
	 * @param   Profiler  $profiler  Profiler instance to register.
	 *
	 * @return  $this
	 *
	 * @since   1.0
	 */
	public function registerProfiler($name, Profiler $profiler)
	{
		self::$profilers[$name] = $profiler;

		return $this;
	}

	/**
	 * Unregister a Profiler instance.
	 *
	 * @param   string  $name  Name of the profiler to unregister.
	 *
	 * @return  $this
	 *
	 * @since   1.0
	 */
	public function unregisterProfiler($name)
	{
		unset(self::$profilers[$name]);

		return $this;
	}
}
