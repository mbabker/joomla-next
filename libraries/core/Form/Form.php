<?php
/**
 * Joomla! Next Application Platform
 *
 * @copyright  Copyright (C) Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

namespace Joomla\CMS\Form;

use Joomla\DI\ContainerAwareInterface;
use Joomla\DI\ContainerAwareTrait;
use Joomla\Form\Form as BaseForm;

/**
 * Extended Form object for the Joomla! CMS
 *
 * @since  1.0
 */
class Form extends BaseForm implements ContainerAwareInterface
{
	use ContainerAwareTrait;

	/**
	 * FormFactory instance
	 *
	 * @var    FormFactory
	 * @since  1.0
	 */
	protected $factory;

	/**
	 * Method to instantiate the form object.
	 *
	 * @param   string  $name     The name of the form.
	 * @param   array   $options  An array of form options.
	 *
	 * @since   1.0
	 */
	public function __construct($name, array $options = array())
	{
		parent::__construct($name, $options);

		// Register an instance of the FormFactory
		$this->factory = new FormFactory;

		// Extract the container from the Factory
		$this->setContainer($this->factory->getContainer());
	}

	/**
	 * {@inheritdoc}
	 *
	 * @since  1.0
	 */
	protected function loadField($element, $group = null, $value = null)
	{
		// Make sure there is a valid SimpleXMLElement.
		if (!($element instanceof \SimpleXMLElement))
		{
			return false;
		}

		// Get the field type.
		$type = $element['type'] ? (string) $element['type'] : 'text';

		// Load the form field via the Factory
		$field = $this->factory->loadField($type, $this);

		/*
		 * Get the value for the form field if not set.
		 * Default to the translated version of the 'default' attribute
		 * if 'translate_default' attribute if set to 'true' or '1'
		 * else the value of the 'default' attribute for the field.
		 */
		if ($value === null)
		{
			$default = (string) $element['default'];

			// Try to translate the default value if translations are enabled
			try
			{
				$lang = $this->getContainer()->get('Joomla\\Language\\Language');

				if (($translate = $element['translate_default']) && ((string) $translate == 'true' || (string) $translate == '1'))
				{
					if ($lang->hasKey($default))
					{
						$debug = $lang->setDebug(false);
						$default = $this->getText()->translate($default);
						$lang->setDebug($debug);
					}
					else
					{
						$default = $this->getText()->translate($default);
					}
				}
			}
			catch (\InvalidArgumentException $exception)
			{
				// A Language object doesn't exist in the Container, try to keep going as we should be able to work without translations
			}

			$value = $this->getValue((string) $element['name'], $group, $default);
		}

		if ($field->setup($element, $value, $group))
		{
			return $field;
		}

		throw new \RuntimeException(sprintf(
			'Could not setup form field %s', get_class($field)
		));
	}
}
