<?php
/**
 * Joomla! Next Application Platform
 *
 * @copyright  Copyright (C) Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

namespace Joomla\CMS\Renderer;

use Joomla\DI\Container;

/**
 * Factory for handling renderer classes
 *
 * @since  1.0
 */
class RendererFactory
{
	/**
	 * Application DI Container
	 *
	 * @var    Container
	 * @since  1.0
	 */
	private static $container;

	/**
	 * Container for holding registered RendererHelperInterface objects
	 *
	 * @var    RendererHelperInterface[]
	 * @since  1.0
	 */
	private static $helpers = [];

	/**
	 * Container with active LayoutRenderer objects
	 *
	 * @var    LayoutRenderer[]
	 * @since  1.0
	 */
	private static $renderers = [];

	/**
	 * Add a RendererHelperInterface object to the available renderer helpers
	 *
	 * @param   RendererHelperInterface  $helper  Helper object to add
	 *
	 * @return  $this
	 *
	 * @since   1.0
	 */
	public function addHelper(RendererHelperInterface $helper)
	{
		if (array_key_exists($helper->getName(), self::$helpers))
		{
			throw new \InvalidArgumentException(sprintf('A "%s" helper is already registered.', $helper->getName()));
		}

		self::$helpers[$helper->getName()] = $helper;

		return $this;
	}

	/**
	 * Retrieve the DI Container
	 *
	 * @return  Container
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public function getContainer()
	{
		if (self::$container)
		{
			return self::$container;
		}

		throw new \RuntimeException('DI Container not registered in ' . __CLASS__);
	}

	/**
	 * Retrieve the active renderer helpers
	 *
	 * @return  RendererHelperInterface[]
	 *
	 * @since   1.0
	 */
	public function getHelpers()
	{
		return self::$helpers;
	}

	/**
	 * Retrieve a LayoutRenderer for the given template
	 *
	 * @param   string                           $template  The name of the active template
	 * @param   string                           $basePath  Base path to use when loading layout files
	 * @param   array|\Joomla\Registry\Registry  $options   Optional custom options to load.
	 *
	 * @return  LayoutRenderer
	 *
	 * @since   1.0
	 */
	public function getRenderer($template, $basePath = null, $options = [])
	{
		if (!isset(self::$renderers[$template]))
		{
			self::$renderers[$template] = new LayoutRenderer($template, $basePath, $options);
		}

		return self::$renderers[$template];
	}

	/**
	 * Remove a RendererHelperInterface object from the available renderer helpers
	 *
	 * @param   string  $name  Name of the helper to remove
	 *
	 * @return  $this
	 *
	 * @since   1.0
	 */
	public function removeHelper($name)
	{
		unset(self::$helpers[$name]);

		return $this;
	}

	/**
	 * Registers the application DI Container to the Factory
	 *
	 * @param   Container  $container  DI Container
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public static function setContainer(Container $container)
	{
		self::$container = $container;
	}
}
