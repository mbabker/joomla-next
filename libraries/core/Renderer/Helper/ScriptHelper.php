<?php
/**
 * Joomla! Next Application Platform
 *
 * @copyright  Copyright (C) Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

namespace Joomla\CMS\Renderer\Helper;

use Joomla\CMS\Renderer\RendererFactory;
use Joomla\CMS\Renderer\RendererHelperInterface;

/**
 * Renderer helper for managing scripts
 *
 * @since  1.0
 */
class ScriptHelper implements RendererHelperInterface
{
	use MediaTrait;

	/**
	 * DI Container
	 *
	 * @var    \Joomla\DI\Container
	 * @since  1.0
	 */
	protected $container;

	/**
	 * Constructor
	 *
	 * @since   1.0
	 */
	public function __construct()
	{
		$this->container = (new RendererFactory)->getContainer();
	}

	/**
	 * Adds a path to a script to an HTML document
	 *
	 * @param   string   $file            Path to file.
	 * @param   boolean  $relative        Path to file is relative to /media folder.
	 * @param   boolean  $path_only       Return the path to the file only.
	 * @param   boolean  $detect_browser  Detect browser to include specific browser js files.
	 * @param   boolean  $detect_debug    Detect debug to search for compressed files if debug is on.
	 *
	 * @return  array|string|void
	 *
	 * @since   1.0
	 */
	public function addScript($file, $relative = false, $path_only = false, $detect_browser = true, $detect_debug = true)
	{
		// Fetch the document
		/** @var \Joomla\CMS\Document\DocumentInterface $document */
		$document = $this->container->get('document');

		// Only process HTML documents
		if ($document->getType() != 'html')
		{
			return;
		}

		$includes = $this->includeRelativeFiles('js', $file, $relative, $detect_browser, $detect_debug);

		// If only path is required
		if ($path_only)
		{
			if (count($includes) == 0)
			{
				return;
			}

			if (count($includes) == 1)
			{
				return $includes[0];
			}

			return $includes;
		}

		// If inclusion is required
		foreach ($includes as $include)
		{
			$document->addScript($include);
		}
	}

	/**
	 * {@inheritdoc}
	 *
	 * @since  1.0
	 */
	public function getName()
	{
		return 'script';
	}
}
