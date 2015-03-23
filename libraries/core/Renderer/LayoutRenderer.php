<?php
/**
 * Joomla! Next Application Platform
 *
 * @copyright  Copyright (C) Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

namespace Joomla\CMS\Renderer;

use Joomla\Filesystem\Path;
use Joomla\Registry\Registry;
use Joomla\Renderer\AbstractRenderer;

/**
 * Base class for rendering layout
 *
 * @see    https://docs.joomla.org/Sharing_layouts_across_views_or_extensions_with_JLayout
 * @since  1.0
 */
class LayoutRenderer extends AbstractRenderer
{
	/**
	 * Base path to use when loading layout files
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $basePath;

	/**
	 * Debug information messages
	 *
	 * @var    array
	 * @since  1.0
	 */
	protected $debugMessages = [];

	/**
	 * Full path to actual layout files, after possible template override check
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $fullPath;

	/**
	 * Paths to search for layouts
	 *
	 * @var    array
	 * @since  1.0
	 */
	protected $includePaths = [];

	/**
	 * Dot separated path to the layout file, relative to base path
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $layoutId = '';

	/**
	 * Options object
	 *
	 * @var    Registry
	 * @since  1.0
	 */
	protected $options;

	/**
	 * Name of the active template
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $template;

	/**
	 * Method to instantiate the file-based layout.
	 *
	 * @param   string          $template  The name of the active template
	 * @param   string          $basePath  Base path to use when loading layout files
	 * @param   array|Registry  $options   Optional custom options to load.
	 *
	 * @since   1.0
	 */
	public function __construct($template, $basePath = null, $options = null)
	{
		// Initialise / Load options
		$this->setOptions($options);

		// Main properties
		$this->basePath = $basePath;
		$this->setTemplate($template);

		// Init Enviroment
		$this->setComponent($this->options->get('component', 'auto'));
		$this->setClient($this->options->get('client', 'auto'));
	}

	/**
	 * Add a debug message to the debug messages array
	 *
	 * @param   string  $message  Message to save
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function addDebugMessage($message)
	{
		$this->debugMessages[] = $message;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @since  1.0
	 */
	public function addFolder($alias, $directory)
	{
		array_unshift($this->includePaths, $directory);

		return $this;
	}

	/**
	 * Get the debug messages array
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function getDebugMessages()
	{
		return $this->debugMessages;
	}

	/**
	 * Method to finds the full real file path, checking possible overrides
	 *
	 * @return  string  The full path to the layout file
	 *
	 * @since   1.0
	 */
	protected function getPath($template)
	{
		if (is_null($this->fullPath) && !empty($this->layoutId))
		{
			$this->addDebugMessage('<strong>Layout:</strong> ' . $this->layoutId);

			// Refresh paths
			$this->refreshIncludePaths();

			$this->addDebugMessage('<strong>Include Paths:</strong> ' . print_r($this->includePaths, true));

			$suffixes = $this->options->get('suffixes', []);

			// Search for suffixed versions. Example: tags.j31.php
			if (!empty($suffixes))
			{
				$this->addDebugMessage('<strong>Suffixes:</strong> ' . print_r($suffixes, true));

				foreach ($suffixes as $suffix)
				{
					$rawPath  = str_replace('.', '/', $this->layoutId) . '.' . $suffix . '.php';
					$this->addDebugMessage('<strong>Searching layout for:</strong> ' . $rawPath);

					if ($this->pathExists($rawPath))
					{
						$this->addDebugMessage('<strong>Found layout:</strong> ' . $this->fullPath);

						return $this->fullPath;
					}
				}
			}

			// Standard version
			$rawPath  = str_replace('.', '/', $this->layoutId) . '.php';
			$this->addDebugMessage('<strong>Searching layout for:</strong> ' . $rawPath);
			$this->pathExists($rawPath);

			if ($this->fullPath)
			{
				$this->addDebugMessage('<strong>Found layout:</strong> ' . $this->fullPath);
			}
		}

		return $this->fullPath;
	}

	/**
	 * Get the options
	 *
	 * @return  Registry  Object with the options
	 *
	 * @since   1.0
	 */
	public function getOptions()
	{
		// Always return a Registry instance
		if (!($this->options instanceof Registry))
		{
			$this->resetOptions();
		}

		return $this->options;
	}

	/**
	 * Get the rendering engine
	 *
	 * @return  $this
	 *
	 * @since   1.0
	 */
	public function getRenderer()
	{
		return $this;
	}

	/**
	 * Get the current template
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	public function getTemplate()
	{
		if ($this->template)
		{
			return $this->template;
		}

		throw new \RuntimeException('The template has not been defined.');
	}

	/**
	 * {@inheritdoc}
	 *
	 * @since   1.0
	 * @throws  \InvalidArgumentException
	 */
	public function pathExists($path)
	{
		if (empty($this->includePaths))
		{
			throw new \InvalidArgumentException('The include paths lookup is empty.');
		}

		$this->fullPath = Path::find($this->includePaths, $path);

		return (bool) $this->fullPath;
	}

	/**
	 * Refresh the list of include paths
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function refreshIncludePaths()
	{
		// Reset includePaths
		$this->includePaths = [];

		// (1 - lower priority) Frontend base layouts
		$this->addFolder(null, JPATH_ROOT . '/layouts');

		// (2) Standard Joomla! layouts overriden
		$this->addFolder(null, JPATH_THEMES . '/' . $this->getTemplate() . '/html/layouts');

		// Component layouts & overrides if exist
		$component = $this->options->get('component', null);

		if (!empty($component))
		{
			// (3) Component path
			if ($this->options->get('client') == 'site')
			{
				$this->addFolder(null, JPATH_SITE . '/components/' . $component . '/layouts');
			}
			else
			{
				$this->addFolder(null, JPATH_ADMINISTRATOR . '/components/' . $component . '/layouts');
			}

			// (4) Component template overrides path
			$this->addFolder(null, JPATH_THEMES . '/' . $this->getTemplate() . '/html/layouts/' . $component);
		}

		// (5 - highest priority) Received a custom high priority path ?
		if (!is_null($this->basePath))
		{
			$this->addFolder(null, rtrim($this->basePath, DIRECTORY_SEPARATOR));
		}
	}

	/**
	 * Remove one or more paths to exclude in layout search
	 *
	 * @param   string  $path  The path to remove
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function removeFolder($path)
	{
		if (!empty($path))
		{
			$paths = (array) $path;

			$this->includePaths = array_diff($this->includePaths, $paths);
		}
	}

	/**
	 * {@inheritdoc}
	 *
	 * @since  1.0
	 */
	public function render($template, array $data = [])
	{
		$this->setLayout($template);

		// Merge the display data
		$displayData = array_merge($this->data, $data);

		// Check possible overrides, and build the full path to layout file
		$path = $this->getPath($template);

		if ($this->options->get('debug', false))
		{
			echo "<pre>" . $this->renderDebugMessages() . "</pre>";
		}

		// If there exists such a layout file, include it and collect its output
		if (!empty($path))
		{
			return self::renderLayout($path, $displayData);
		}

		return '';
	}

	/**
	 * Renders a requested layout file with the specified data.
	 *
	 * This isolates the scope of the rendered file.
	 *
	 * @param   string  $path         Full file path of the layout file to render.
	 * @param   array   $displayData  Array containing the data the layout can render.
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	private static function renderLayout($path, array $displayData)
	{
		ob_start();
		include $path;

		return ob_get_clean();
	}

	/**
	 * Function to empty all the options
	 *
	 * @return  $this
	 *
	 * @since   1.0
	 */
	public function resetOptions()
	{
		return $this->setOptions(null);
	}

	/**
	 * Method to change the component where search for layouts
	 *
	 * @param   string  $option  URL Option of the component. Example: com_content
	 *
	 * @return  mixed  Component option string | null for none
	 *
	 * @since   1.0
	 */
	public function setComponent($option)
	{
		$component = null;

		switch ((string) $option)
		{
			case 'none':
				$component = null;
				break;

			case 'auto':
				if (defined('JPATH_COMPONENT'))
				{
					$parts = explode('/', JPATH_COMPONENT);
					$component = end($parts);
				}

				break;

			default:
				$component = $option;
				break;
		}

		// Extra checks
		if (!$this->validComponent($component))
		{
			$component = null;
		}

		$this->options->set('component', $component);

		// Refresh include paths
		$this->refreshIncludePaths();
	}

	/**
	 * Function to initialise the application client
	 *
	 * @param   string  $client  Client name to set
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function setClient($client)
	{
		// Force string conversion to avoid unexpected states
		switch ((string) $client)
		{
			case 'site':
			case 'admin':
				$client = $client;
				break;

			default:
				// Assume the frontend if we don't know
				$client = 'site';
				break;
		}

		$this->options->set('client', $client);

		// Refresh include paths
		$this->refreshIncludePaths();
	}

	/**
	 * {@inheritdoc}
	 *
	 * @since  1.0
	 */
	public function setFileExtension($extension)
	{
		// TODO: Implement setFileExtension() method.
	}

	/**
	 * Change the layout
	 *
	 * @param   string  $layoutId  Layout to render
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function setLayout($layoutId)
	{
		$this->layoutId = $layoutId;
		$this->fullPath = null;
	}

	/**
	 * Set the options
	 *
	 * @param   array|Registry  $options  Array / Registry object with the options to load
	 *
	 * @return  $this
	 *
	 * @since   1.0
	 */
	public function setOptions($options = null)
	{
		// Received Registry
		if ($options instanceof Registry)
		{
			$this->options = $options;
		}
		// Received array
		elseif (is_array($options))
		{
			$this->options = new Registry($options);
		}
		else
		{
			$this->options = new Registry;
		}

		return $this;
	}

	/**
	 * Set the active application template
	 *
	 * @param   string  $template  The name of the active template
	 *
	 * @return  $this
	 *
	 * @since   1.0
	 */
	public function setTemplate($template)
	{
		$this->template = $template;

		return $this;
	}

	/**
	 * Validate that the active component is valid
	 *
	 * @param   string  $option  URL Option of the component. Example: com_content
	 *
	 * @return  boolean
	 *
	 * @since   1.0
	 */
	protected function validComponent($option = null)
	{
		// TODO - Revisit when component API is present
		return false;

		// By default we will validate the active component
		$component = ($option !== null) ? $option : $this->options->get('component', null);

		if (!empty($component))
		{
			// Valid option format
			if (substr_count($component, 'com_'))
			{
				// Latest check: component exists and is enabled
				return JComponentHelper::isEnabled($component);
			}
		}

		return false;
	}
}
