<?php
/**
 * Joomla! Next Application Platform
 *
 * @copyright  Copyright (C) Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

namespace Joomla\Profiler;

/**
 * Factory for handling Profiler instances
 *
 * @since  1.0
 */
class ProfilerFactory
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
	 * @param   string  $profiler  Name of the profiler to retrieve.
	 *
	 * @return  Profiler
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public function getProfiler($profiler)
	{
		if (!isset(self::$profilers[$profiler]))
		{
			throw new \RuntimeException('The requested profiler does not exist.');
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
		// Validate the profiler exists and try to create it if we're allowed, otherwise fail
		if (!isset(self::$profilers[$profiler]))
		{
			if (!$create)
			{
				throw new \RuntimeException('The requested profiler does not exist.');
			}

			self::$profilers[$profiler] = new Profiler($profiler);
		}

		self::$profilers[$profiler]->mark($name);

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
}
