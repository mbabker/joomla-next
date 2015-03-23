<?php
/**
 * Joomla! Next Installation Application
 *
 * @copyright  Copyright (C) Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

namespace Installation\Form\Field;

use Installation\Application;
use Installation\Model\SetupModel;
use Joomla\CMS\Language\LanguageHelper;
use Joomla\Form\Field\ListField;
use Joomla\Form\Form;
use Joomla\Language\Language;

/**
 * Language Form Field class.
 *
 * @since  1.0
 */
class LanguageField extends ListField
{
	/**
	 * Application object.
	 *
	 * @var    Application
	 * @since  1.0
	 */
	private $application;

	/**
	 * Language object.
	 *
	 * @var    Language
	 * @since  1.0
	 */
	private $language;

	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since   1.0
	 */
	protected $type = 'Language';

	/**
	 * Constructor.
	 *
	 * @param   Form         $form         The form to attach to the form field object.
	 * @param   Application  $application  Application object.
	 * @param   Language     $language     Language object.
	 *
	 * @since   1.0
	 */
	public function __construct(Form $form, Application $application, Language $language)
	{
		parent::__construct($form);

		$this->application = $application;
		$this->language    = $language;
	}

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   1.0
	 */
	protected function getOptions()
	{
		$app = $this->application;

		// Detect the native language.
		$languageHelper = new LanguageHelper;
		$native         = $languageHelper->detectLanguage();

		if (empty($native))
		{
			$native = 'en-GB';
		}

		// Get a forced language if it exists.
		$forced = $app->getLocalise();

		if (!empty($forced['language']))
		{
			$native = $forced['language'];
		}

		// If a language is already set in the session, use this instead
		$model   = new SetupModel;
		$options = $model->getOptions();

		if (isset($options['language']))
		{
			$native = $options['language'];
		}

		// Get the list of available languages.
		$options = $languageHelper->createLanguageList($native);

		// Fix wrongly set parentheses in RTL languages
		if ($this->language->isRTL())
		{
			foreach ($options as &$option)
			{
				$option['text'] = $option['text'] . '&#x200E;';
			}
		}

		if (!$options || $options instanceof Exception)
		{
			$options = [];
		}
		// Sort languages by name
		else
		{
			usort($options, [$this, 'sortLanguages']);
		}

		// Set the default value from the native language.
		$this->value = $native;

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}

	/**
	 * Method to sort languages by name.
	 *
	 * @param   string  $a  The first value to determine sort
	 * @param   string  $b  The second value to determine sort
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	private function sortLanguages($a, $b)
	{
		return strcmp($a['text'], $b['text']);
	}
}
