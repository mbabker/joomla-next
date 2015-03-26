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
 * Renderer helper for managing stylesheets
 *
 * @since  1.0
 */
class StylesheetHelper implements RendererHelperInterface
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
	 * Adds a path to a stylesheet to an HTML document
	 *
	 * @param   string   $file            Path to CSS file to load
	 * @param   array    $attribs         Attributes to be added to the stylesheet
	 * @param   boolean  $relative        Path to file is relative to /media folder
	 * @param   boolean  $path_only       Return the path to the file only
	 * @param   boolean  $detect_browser  Detect browser to include specific browser CSS files
	 *                                    will try to include file, file_*browser*, file_*browser*_*major*, file_*browser*_*major*_*minor*
	 *                                    <table>
	 *                                       <tr><th>Navigator</th>                  <th>browser</th>	<th>major.minor</th></tr>
	 *
	 *                                       <tr><td>Safari 3.0.x</td>               <td>konqueror</td>	<td>522.x</td></tr>
	 *                                       <tr><td>Safari 3.1.x and 3.2.x</td>     <td>konqueror</td>	<td>525.x</td></tr>
	 *                                       <tr><td>Safari 4.0 to 4.0.2</td>        <td>konqueror</td>	<td>530.x</td></tr>
	 *                                       <tr><td>Safari 4.0.3 to 4.0.4</td>      <td>konqueror</td>	<td>531.x</td></tr>
	 *                                       <tr><td>iOS 4.0 Safari</td>             <td>konqueror</td>	<td>532.x</td></tr>
	 *                                       <tr><td>Safari 5.0</td>                 <td>konqueror</td>	<td>533.x</td></tr>
	 *
	 *                                       <tr><td>Google Chrome 1.0</td>          <td>konqueror</td>	<td>528.x</td></tr>
	 *                                       <tr><td>Google Chrome 2.0</td>          <td>konqueror</td>	<td>530.x</td></tr>
	 *                                       <tr><td>Google Chrome 3.0 and 4.x</td>  <td>konqueror</td>	<td>532.x</td></tr>
	 *                                       <tr><td>Google Chrome 5.0</td>          <td>konqueror</td>	<td>533.x</td></tr>
	 *
	 *                                       <tr><td>Internet Explorer 5.5</td>      <td>msie</td>		<td>5.5</td></tr>
	 *                                       <tr><td>Internet Explorer 6.x</td>      <td>msie</td>		<td>6.x</td></tr>
	 *                                       <tr><td>Internet Explorer 7.x</td>      <td>msie</td>		<td>7.x</td></tr>
	 *                                       <tr><td>Internet Explorer 8.x</td>      <td>msie</td>		<td>8.x</td></tr>
	 *
	 *                                       <tr><td>Firefox</td>                    <td>mozilla</td>	<td>5.0</td></tr>
	 *                                    </table>
	 *                                    a lot of others
	 * @param   boolean  $detect_debug    Detect debug to search for uncompressed files if debug is on
	 *
	 * @return  array|string|void
	 *
	 * @since   1.0
	 */
	public function addStylesheet($file, $attribs = [], $relative = false, $path_only = false, $detect_browser = true, $detect_debug = true)
	{
		// Fetch the document
		/** @var \Joomla\CMS\Document\DocumentInterface $document */
		$document = $this->container->get('document');

		// Only process HTML documents
		if ($document->getType() != 'html')
		{
			return;
		}

		$includes = $this->includeRelativeFiles('css', $file, $relative, $detect_browser, $detect_debug);

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
			$document->addStylesheet($include, 'text/css', null, $attribs);
		}
	}

	/**
	 * {@inheritdoc}
	 *
	 * @since  1.0
	 */
	public function getName()
	{
		return 'stylesheet';
	}
}
