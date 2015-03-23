<?php
/**
 * Joomla! Next Installation Application
 *
 * @copyright  Copyright (C) Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

namespace Installation\Model;

use Joomla\CMS\Form\FormFactory;
use Joomla\Database\DatabaseDriver;
use Joomla\CMS\Form\Form;
use Joomla\Form\FormHelper;
use Joomla\Language\Text;
use Joomla\Model\AbstractModel;
use Joomla\Session\Session;

/**
 * Setup model for the install application.
 *
 * @since  1.0
 */
class SetupModel extends AbstractModel
{
	/**
	 * Text object
	 *
	 * @var    Text
	 * @since  1.0
	 */
	private $text;

	/**
	 * Method to get the form.
	 *
	 * @param   string  $view  The view being processed.
	 *
	 * @return  Form|boolean  Form object on success, false on failure.
	 *
	 * @since   1.0
	 */
	public function getForm($view)
	{
		// Get the form.
		FormHelper::addFormPath(JPATH_BASE . '/forms');

		$form = (new FormFactory())->loadForm('jform', $view, ['control' => 'jform']);

		// Check the session for previously entered form data.
		$data = (array) $this->getOptions();

		// Bind the form data if present.
		if (!empty($data))
		{
			$form->bind($data);
		}

		return $form;
	}

	/**
	 * Checks the availability of the parse_ini_file and parse_ini_string functions.
	 *
	 * @return	boolean  True if the method exists.
	 *
	 * @since	1.0
	 */
	public function getIniParserAvailability()
	{
		$disabled_functions = ini_get('disable_functions');

		if (!empty($disabled_functions))
		{
			// Attempt to detect them in the disable_functions black list.
			$disabled_functions = explode(',', trim($disabled_functions));
			$number_of_disabled_functions = count($disabled_functions);

			for ($i = 0; $i < $number_of_disabled_functions; $i++)
			{
				$disabled_functions[$i] = trim($disabled_functions[$i]);
			}

			return !in_array('parse_ini_string', $disabled_functions);
		}

		// Attempt to detect their existence; even pure PHP implementation of them will trigger a positive response, though.
		return function_exists('parse_ini_string');
	}

	/**
	 * Get the current setup options from the session.
	 *
	 * @return  array  An array of options from the session.
	 *
	 * @since   1.0
	 */
	public function getOptions()
	{
		$session = Session::getInstance('none');
		$options = $session->get('setup.options', []);

		return $options;
	}

	/**
	 * Gets PHP options.
	 *
	 * @return	array  Array of PHP config options
	 *
	 * @since   1.0
	 */
	public function getPhpOptions()
	{
		$options = [];

		// Check the PHP Version.
		$option = new \stdClass;
		$option->label  = $this->getText()->translate('INSTL_PHP_VERSION') . ' >= ' . JOOMLA_MINIMUM_PHP;
		$option->state  = version_compare(PHP_VERSION, JOOMLA_MINIMUM_PHP, '>=');
		$option->notice = null;
		$options[] = $option;

		// Check for magic quotes gpc.
		$option = new \stdClass;
		$option->label  = $this->getText()->translate('INSTL_MAGIC_QUOTES_GPC');
		$option->state  = (ini_get('magic_quotes_gpc') == false);
		$option->notice = null;
		$options[] = $option;

		// Check for register globals.
		$option = new \stdClass;
		$option->label  = $this->getText()->translate('INSTL_REGISTER_GLOBALS');
		$option->state  = (ini_get('register_globals') == false);
		$option->notice = null;
		$options[] = $option;

		// Check for zlib support.
		$option = new \stdClass;
		$option->label  = $this->getText()->translate('INSTL_ZLIB_COMPRESSION_SUPPORT');
		$option->state  = extension_loaded('zlib');
		$option->notice = null;
		$options[] = $option;

		// Check for XML support.
		$option = new \stdClass;
		$option->label  = $this->getText()->translate('INSTL_XML_SUPPORT');
		$option->state  = extension_loaded('xml');
		$option->notice = null;
		$options[] = $option;

		// Check for database support.
		// We are satisfied if there is at least one database driver available.
		$available = DatabaseDriver::getConnectors();
		$option = new \stdClass;
		$option->label  = $this->getText()->translate('INSTL_DATABASE_SUPPORT');
		$option->label .= '<br />(' . implode(', ', $available) . ')';
		$option->state  = count($available);
		$option->notice = null;
		$options[] = $option;

		// Check for mbstring options.
		if (extension_loaded('mbstring'))
		{
			// Check for default MB language.
			$option = new \stdClass;
			$option->label  = $this->getText()->translate('INSTL_MB_LANGUAGE_IS_DEFAULT');
			$option->state  = (strtolower(ini_get('mbstring.language')) == 'neutral');
			$option->notice = ($option->state) ? null : $this->getText()->translate('INSTL_NOTICEMBLANGNOTDEFAULT');
			$options[] = $option;

			// Check for MB function overload.
			$option = new \stdClass;
			$option->label  = $this->getText()->translate('INSTL_MB_STRING_OVERLOAD_OFF');
			$option->state  = (ini_get('mbstring.func_overload') == 0);
			$option->notice = ($option->state) ? null : $this->getText()->translate('INSTL_NOTICEMBSTRINGOVERLOAD');
			$options[] = $option;
		}

		// Check for a missing native parse_ini_file implementation.
		$option = new \stdClass;
		$option->label  = $this->getText()->translate('INSTL_PARSE_INI_FILE_AVAILABLE');
		$option->state  = $this->getIniParserAvailability();
		$option->notice = null;
		$options[] = $option;

		// Check for missing native json_encode / json_decode support.
		$option = new \stdClass;
		$option->label  = $this->getText()->translate('INSTL_JSON_SUPPORT_AVAILABLE');
		$option->state  = function_exists('json_encode') && function_exists('json_decode');
		$option->notice = null;
		$options[] = $option;

		// Check for configuration file writable.
		$writable = (is_writable(JPATH_CONFIGURATION . '/configuration.php')
			|| (!file_exists(JPATH_CONFIGURATION . '/configuration.php') && is_writable(JPATH_ROOT)));

		$option = new \stdClass;
		$option->label  = $this->getText()->sprintf('INSTL_WRITABLE', 'configuration.php');
		$option->state  = $writable;
		$option->notice = ($option->state) ? null : $this->getText()->translate('INSTL_NOTICEYOUCANSTILLINSTALL');
		$options[] = $option;

		return $options;
	}

	/**
	 * Checks if all of the mandatory PHP options are met.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   1.0
	 */
	public function getPhpOptionsSufficient()
	{
		$result  = true;
		$options = $this->getPhpOptions();

		foreach ($options as $option)
		{
			if (is_null($option->notice))
			{
				$result = ($result && $option->state);
			}
		}

		return $result;
	}

	/**
	 * Retrieves the Text object
	 *
	 * @return  Text
	 *
	 * @since   1.0
	 */
	public function getText()
	{
		if ($this->text)
		{
			return $this->text;
		}

		throw new \RuntimeException('A Text object is not set in ' . __CLASS__);
	}

	/**
	 * Set a Text object to the class
	 *
	 * @param   Text  $text  Text object
	 *
	 * @return  $this
	 *
	 * @since   1.0
	 */
	public function setText(Text $text)
	{
		$this->text = $text;

		return $this;
	}
}
