<?php
/**
 * Joomla! Next Application Platform
 *
 * @copyright  Copyright (C) Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

namespace Joomla\CMS\Document;

use Joomla\CMS\Uri\Uri;
use Joomla\Filter\InputFilter;
use Joomla\Registry\Registry;

/**
 * HTML Document class
 *
 * @since  1.0
 */
class HtmlDocument extends AbstractDocument
{
	/**
	 * Base URL
	 *
	 * @var    string
	 * @since  1.0
	 */
	private $baseurl = null;

	/**
	 * Integer with caching setting
	 *
	 * @var    integer
	 * @since  1.0
	 */
	private $caching = 0;

	/**
	 * Array of template parameters
	 *
	 * @var    array
	 * @since  1.0
	 */
	private $params = null;

	/**
	 * String holding parsed template
	 *
	 * @var    string
	 * @since  1.0
	 */
	private $template = '';

	/**
	 * Array of parsed template <jdoc> tags
	 *
	 * @var    array
	 * @since  1.0
	 */
	private $templateTags = [];

	/**
	 * Document title
	 *
	 * @var    string
	 * @since  1.0
	 */
	private $title = '';

	/**
	 * Class constructor.
	 *
	 * @since   1.0
	 */
	public function __construct()
	{
		// Set document type
		$this->setType('html');

		// Set default mime type and document metadata (meta data syncs with mime type by default)
		$this->setMimeEncoding('text/html');
	}

	/**
	 * Fetch the template, and initialise the params
	 *
	 * @param   array  $params  Parameters to determine the template
	 *
	 * @return  $this
	 *
	 * @since   1.0
	 */
	private function fetchTemplate($params = [])
	{
		// Check
		$directory = isset($params['directory']) ? $params['directory'] : 'templates';
		$filter    = new InputFilter;
		$template  = $filter->clean($params['template'], 'cmd');
		$file      = $filter->clean($params['file'], 'cmd');

		if (!file_exists($directory . '/' . $template . '/' . $file))
		{
			$template = 'system';
		}

		// Load the language file for the template
		/** @var \Joomla\Language\Language $lang */
		$lang = $this->getContainer()->get('language');

		// 1.5 or core then 1.6
		$lang->load('tpl_' . $template, JPATH_BASE, null, false, true)
			|| $lang->load('tpl_' . $template, $directory . '/' . $template, null, false, true);

		// Assign the variables
		$this->template = $template;
		$this->baseurl  = Uri::getInstance()->base(true);
		$this->params   = isset($params['params']) ? $params['params'] : new Registry;

		// Load
		$this->template = $this->loadTemplate($directory . '/' . $template, $file);

		return $this;
	}

	/**
	 * Get the contents of a document include
	 *
	 * @param   string  $type     The type of renderer
	 * @param   string  $name     The name of the element to render
	 * @param   array   $attribs  Associative array of remaining attributes.
	 *
	 * @return  The output of the renderer
	 *
	 * @since   1.0
	 * @todo    Strict standards issue
	 */
	public function getBuffer($type = null, $name = null, $attribs = array())
	{
		// If no type is specified, return the whole buffer
		if ($type === null)
		{
			return $this->buffer;
		}

		$title = (isset($attribs['title'])) ? $attribs['title'] : null;

		if (isset($this->buffer[$type][$name][$title]))
		{
			return $this->buffer[$type][$name][$title];
		}

		$renderer = (new DocumentFactory())->getRenderer($type, $this);

		if ($this->caching == true && $type == 'modules')
		{
			throw new \RuntimeException('Not yet supported.');

			$cache = JFactory::getCache('com_modules', '');
			$hash = md5(serialize(array($name, $attribs, null, $renderer)));
			$cbuffer = $cache->get('cbuffer_' . $type);

			if (isset($cbuffer[$hash]))
			{
				return JCache::getWorkarounds($cbuffer[$hash], array('mergehead' => 1));
			}
			else
			{
				$options = array();
				$options['nopathway'] = 1;
				$options['nomodules'] = 1;
				$options['modulemode'] = 1;

				$this->setBuffer($renderer->render($name, $attribs, null), $type, $name);
				$data = $this->buffer[$type][$name][$title];

				$tmpdata = JCache::setWorkarounds($data, $options);

				$cbuffer[$hash] = $tmpdata;

				$cache->store($cbuffer, 'cbuffer_' . $type);
			}
		}
		else
		{
			$this->setBuffer($renderer->render($name, $attribs, null), ['type' => $type, 'name' => $name, 'title' => $title]);
		}

		return $this->buffer[$type][$name][$title];
	}

	/**
	 * Return the title of the document.
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * Load a template file
	 *
	 * @param   string  $directory  The name of the template
	 * @param   string  $filename   The actual filename
	 *
	 * @return  string  The contents of the template
	 *
	 * @since   1.0
	 */
	private function loadTemplate($directory, $filename)
	{
		$contents = '';

		// Check to see if we have a valid template file
		if (file_exists($directory . '/' . $filename))
		{
			// Store the file path
			$this->file = $directory . '/' . $filename;

			// Get the file content
			ob_start();
			require $directory . '/' . $filename;
			$contents = ob_get_contents();
			ob_end_clean();
		}

		// Try to find a favicon by checking the template and root folder
		$icon = '/favicon.ico';

		foreach (array($directory, JPATH_BASE) as $dir)
		{
			if (file_exists($dir . $icon))
			{
				$path = str_replace(JPATH_BASE, '', $dir);
				$path = str_replace('\\', '/', $path);
				$this->addFavicon(Uri::getInstance()->base(true) . $path . $icon);
				break;
			}
		}

		return $contents;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @since   1.0
	 */
	public function parse($params = [])
	{
		return $this->fetchTemplate($params)->parseTemplate();
	}

	/**
	 * Method to extract key/value pairs out of a string with XML style attributes
	 *
	 * @param   string  $string  String containing XML style attributes
	 *
	 * @return  array  Key/Value pairs for the attributes
	 *
	 * @since   1.0
	 */
	private function parseAttributes($string)
	{
		$attr = [];
		$retarray = [];

		// Let's grab all the key/value pairs using a regular expression
		preg_match_all('/([\w:-]+)[\s]?=[\s]?"([^"]*)"/i', $string, $attr);

		if (is_array($attr))
		{
			$numPairs = count($attr[1]);

			for ($i = 0; $i < $numPairs; $i++)
			{
				$retarray[$attr[1][$i]] = $attr[2][$i];
			}
		}

		return $retarray;
	}

	/**
	 * Parse a document template
	 *
	 * @return  $this
	 *
	 * @since   1.0
	 */
	private function parseTemplate()
	{
		$matches = [];

		if (preg_match_all('#<jdoc:include\ type="([^"]+)"(.*)\/>#iU', $this->template, $matches))
		{
			$template_tags_first = [];
			$template_tags_last  = [];

			// Step through the jdocs in reverse order.
			for ($i = count($matches[0]) - 1; $i >= 0; $i--)
			{
				$type    = $matches[1][$i];
				$attribs = empty($matches[2][$i]) ? [] : $this->parseAttributes($matches[2][$i]);
				$name    = isset($attribs['name']) ? $attribs['name'] : null;

				// Separate buffers to be executed first and last
				if ($type == 'module' || $type == 'modules')
				{
					$template_tags_first[$matches[0][$i]] = array('type' => $type, 'name' => $name, 'attribs' => $attribs);
				}
				else
				{
					$template_tags_last[$matches[0][$i]] = array('type' => $type, 'name' => $name, 'attribs' => $attribs);
				}
			}
			// Reverse the last array so the jdocs are in forward order.
			$template_tags_last = array_reverse($template_tags_last);

			$this->templateTags = $template_tags_first + $template_tags_last;
		}

		return $this;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @since   1.0
	 */
	public function render($caching = false, $params = [])
	{
		$this->caching = $caching;

		if (empty($this->template))
		{
			$this->parse($params);
		}

		$data = $this->renderTemplate();

		parent::render();

		return $data;
	}

	/**
	 * Render pre-parsed template
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	private function renderTemplate()
	{
		$replace = [];
		$with    = [];

		foreach ($this->templateTags as $jdoc => $args)
		{
			$replace[] = $jdoc;
			$with[]    = $this->getBuffer($args['type'], $args['name'], $args['attribs']);
		}

		return str_replace($replace, $with, $this->template);
	}

	/**
	 * {@inheritdoc}
	 *
	 * @since  1.0
	 */
	public function setBuffer($content, $options = [])
	{
		if (!isset($options['type']))
		{
			throw new \RuntimeException('The buffer type must be specified');
		}

		$type  = $options['type'];
		$name  = isset($options['name']) ? $options['name'] : null;
		$title = isset($options['title']) ? $options['title'] : null;

		$this->buffer[$type][$name][$title] = $content;

		return $this;
	}

	/**
	 * Sets the title of the document
	 *
	 * @param   string  $title  The title to be set
	 *
	 * @return  $this
	 *
	 * @since   1.0
	 */
	public function setTitle($title)
	{
		$this->title = $title;

		return $this;
	}
}
