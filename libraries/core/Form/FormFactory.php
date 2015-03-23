<?php
/**
 * Joomla! Next Application Platform
 *
 * @copyright  Copyright (C) Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

namespace Joomla\CMS\Form;

use Joomla\DI\Container;
use Joomla\Form\FormHelper;

/**
 * Factory for handling form classes
 *
 * @since  1.0
 */
class FormFactory
{
	/**
	 * Application DI Container
	 *
	 * @var    Container
	 * @since  1.0
	 */
	private static $container;

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
	 * Load the requested form field
	 *
	 * @param   string   $type        The type of form field to load
	 * @param   Form     $form        A Form object to inject into the Field
	 * @param   boolean  $useDefault  Flag to use a default form field if the requested field is not found
	 *
	 * @return  \Joomla\Form\Field
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public function loadField($type, Form $form, $useDefault = true)
	{
		// Load the Field object for the field.
		$class = FormHelper::loadFieldClass($type);

		// If the object could not be loaded, get a text field object if allowed.
		if ($class === false)
		{
			if ($useDefault === false)
			{
				throw new \RuntimeException(sprintf(
					'The %s form field could not be loaded.', $type
				));
			}

			$class = FormHelper::loadFieldClass('text');
		}

		// Temporarily set the Form object to the DI Container for proper resolution
		$this->getContainer()->set('Joomla\\Form\\Form', $form);

		// Instantiate the Field object
		/** @var \Joomla\Form\Field $field */
		$field = $this->getContainer()->buildObject($class);

		// Now unset our object
		$this->getContainer()->set('Joomla\\Form\\Form', null);

		/*
		 * Try to inject a Text object into the field
		 * First, check if the Container has a Text instance registered and prefer it
		 * Next, check for a Language instance and build the Text object from that
		 */
		$text = null;

		if ($this->getContainer()->exists('Joomla\\Language\\Text'))
		{
			$text = $this->getContainer()->get('Joomla\\Language\\Language');
		}
		elseif ($this->getContainer()->exists('Joomla\\Language\\Language'))
		{
			$text = $this->getContainer()->get('Joomla\\Language\\Language')->getText();
		}

		if ($text)
		{
			$field->setText($text);
		}

		return $field;
	}

	/**
	 * Load an instance of a form.
	 *
	 * @param   string       $name     The name of the form.
	 * @param   string       $data     The name of an XML file or string to load as the form definition.
	 * @param   array        $options  An array of form options.
	 * @param   boolean      $replace  Flag to toggle whether form fields should be replaced if a field
	 *                                 already exists with the same group/name.
	 * @param   bool|string  $xpath    An optional xpath to search for the fields.
	 *
	 * @return  Form
	 *
	 * @since   1.0
	 */
	public function loadForm($name, $data = null, $options = array(), $replace = true, $xpath = false)
	{
		$form = Form::getInstance($name, $data, $options, $replace, $xpath);

		/*
		 * Try to inject a Text object into the field
		 * First, check if the Container has a Text instance registered and prefer it
		 * Next, check for a Language instance and build the Text object from that
		 */
		$text = null;

		if ($this->getContainer()->exists('Joomla\\Language\\Text'))
		{
			$text = $this->getContainer()->get('Joomla\\Language\\Language');
		}
		elseif ($this->getContainer()->exists('Joomla\\Language\\Language'))
		{
			$text = $this->getContainer()->get('Joomla\\Language\\Language')->getText();
		}

		if ($text)
		{
			$form->setText($text);
		}

		return $form;
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
