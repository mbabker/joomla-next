<?php
/**
 * Joomla! Next Application Platform
 *
 * @copyright  Copyright (C) Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

namespace Joomla\CMS\Language;

use Joomla\DI\ContainerAwareInterface;
use Joomla\DI\ContainerAwareTrait;
use Joomla\Language\LanguageHelper as BaseLanguageHelper;

/**
 * Language helper for the Joomla! CMS
 *
 * @since  1.0
 */
class LanguageHelper extends BaseLanguageHelper implements ContainerAwareInterface
{
	use ContainerAwareTrait;

	/**
	 * Builds a list of the system languages which can be used in a select option
	 *
	 * @param   string   $actualLanguage  Client key for the area
	 * @param   string   $basePath        Base path to use
	 * @param   boolean  $caching         True if caching is used
	 * @param   boolean  $installed       Get only installed languages
	 *
	 * @return  array  List of system languages
	 *
	 * @since   1.0
	 */
	public function createLanguageList($actualLanguage, $basePath = JPATH_BASE, $caching = false, $installed = false)
	{
		$list = array();

		// Cache activation
		$langs = $this->getKnownLanguages($basePath);

		if ($installed)
		{
			/** @var \Joomla\Database\DatabaseDriver $db */
			$db    = $this->getContainer()->get('db');
			$query = $db->getQuery(true)
				->select('element')
				->from('#__extensions')
				->where('type=' . $db->quote('language'))
				->where('state=0')
				->where('enabled=1')
				->where('client_id=' . ($basePath == JPATH_ADMINISTRATOR ? 1 : 0));
			$db->setQuery($query);
			$installed_languages = $db->loadObjectList('element');
		}

		foreach ($langs as $lang => $metadata)
		{
			if (!$installed || array_key_exists($lang, $installed_languages))
			{
				$option = array();

				$option['text'] = $metadata['name'];
				$option['value'] = $lang;

				if ($lang == $actualLanguage)
				{
					$option['selected'] = 'selected="selected"';
				}

				$list[] = $option;
			}
		}

		return $list;
	}

	/**
	 * Tries to detect the language.
	 *
	 * @return  string  locale or null if not found
	 *
	 * @since   1.0
	 */
	public function detectLanguage()
	{
		if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
		{
			$browserLangs = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
			$systemLangs  = $this->getLanguages();

			foreach ($browserLangs as $browserLang)
			{
				// Slice out the part before ; on first step, the part before - on second, place into array
				$browserLang         = substr($browserLang, 0, strcspn($browserLang, ';'));
				$primary_browserLang = substr($browserLang, 0, 2);

				foreach ($systemLangs as $systemLang)
				{
					// Take off 3 letters iso code languages as they can't match browsers' languages and default them to en
					$Jinstall_lang = $systemLang->lang_code;

					if (strlen($Jinstall_lang) < 6)
					{
						if (strtolower($browserLang) == strtolower(substr($systemLang->lang_code, 0, strlen($browserLang))))
						{
							return $systemLang->lang_code;
						}
						elseif ($primary_browserLang == substr($systemLang->lang_code, 0, 2))
						{
							$primaryDetectedLang = $systemLang->lang_code;
						}
					}
				}

				if (isset($primaryDetectedLang))
				{
					return $primaryDetectedLang;
				}
			}
		}

		return null;
	}

	/**
	 * Get available languages
	 *
	 * @param   string  $key  Array key
	 *
	 * @return  array  An array of published languages
	 *
	 * @since   1.0
	 */
	public function getLanguages($key = 'default')
	{
		static $languages;

		if (empty($languages))
		{
			/** @var \Joomla\CMS\Application\CMSApplicationInterface $app */
			$app = $this->getContainer()->get('app');

			// Installation uses a list of available languages in the application
			if ($app->getName() === 'installation')
			{
				$languages[$key] = [];
				$knownLangs      = $this->getKnownLanguages(JPATH_INSTALLATION);

				foreach ($knownLangs as $metadata)
				{
					// Take off 3 letters iso code languages as they can't match browsers' languages and default them to en
					$obj               = new \stdClass;
					$obj->lang_code    = $metadata['tag'];
					$languages[$key][] = $obj;
				}
			}
			else
			{
				/** @var \Joomla\Database\DatabaseDriver $db */
				$db    = $this->getContainer()->get('db');
				$query = $db->getQuery(true)
					->select('*')
					->from('#__languages')
					->where('published=1')
					->order('ordering ASC');
				$db->setQuery($query);

				$languages['default']   = $db->loadObjectList();
				$languages['sef']       = [];
				$languages['lang_code'] = [];

				if (isset($languages['default'][0]))
				{
					foreach ($languages['default'] as $lang)
					{
						$languages['sef'][$lang->sef]             = $lang;
						$languages['lang_code'][$lang->lang_code] = $lang;
					}
				}
			}
		}

		return $languages[$key];
	}
}
