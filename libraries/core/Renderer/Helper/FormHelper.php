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
 * Renderer helper for adding form enhancements
 *
 * @since  1.0
 */
class FormHelper extends jQueryHelper
{
	/**
	 * {@inheritdoc}
	 *
	 * @since  1.0
	 */
	public function getName()
	{
		return 'form';
	}

	/**
	 * Add unobtrusive JavaScript support for form validation.
	 *
	 * To enable form validation the form tag must have class="form-validate".
	 * Each field that needs to be validated needs to have class="validate".
	 * Additional handlers can be added to the handler for username, password,
	 * numeric and email. To use these add class="validate-email" and so on.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function loadValidator()
	{
		// Fetch the document
		/** @var \Joomla\CMS\Document\DocumentInterface $document */
		$document = $this->container->get('document');

		// Only process HTML documents
		if ($document->getType() != 'html')
		{
			return;
		}

		// Only load once
		if (!empty(static::$loaded[__METHOD__]))
		{
			return;
		}

		// Fetch the script helper
		/** @var ScriptHelper $helper */
		$helper = $this->factory->getHelpers()['script'];

		$helper->addScript('system/punycode.js', true);
		$helper->addScript('system/validate.js', true);

		static::$loaded[__METHOD__] = true;
	}
}
