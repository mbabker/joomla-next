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
 * Renderer helper for accessing the application's translator
 *
 * @since  1.0
 */
class TranslateHelper implements RendererHelperInterface
{
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
	 * {@inheritdoc}
	 *
	 * @since  1.0
	 */
	public function getName()
	{
		return 'translate';
	}

	/**
	 * Retrieve a Text object for the application's primary language
	 *
	 * @return  \Joomla\Language\Text
	 *
	 * @since   1.0
	 */
	public function getTranslator()
	{
		return $this->container->get('Joomla\\Language\\LanguageFactory')->getText();
	}
}
