<?php
/**
 * Joomla! Next Application Platform
 *
 * @copyright  Copyright (C) Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

namespace Joomla\CMS;

/**
 * Version information class for the application.
 *
 * @since  1.0
 */
final class Version
{
	/**
	 * Product name.
	 *
	 * @var    string
	 * @since  1.0
	 */
	const PRODUCT = 'Joomla! Next';

	/**
	 * Major version number.
	 *
	 * @var    integer
	 * @since  1.0
	 */
	const MAJOR_LEVEL = 1;

	/**
	 * Minor version number.
	 *
	 * @var    integer
	 * @since  1.0
	 */
	const MINOR_LEVEL = 0;

	/**
	 * Patch version number.
	 *
	 * @var    integer
	 * @since  1.0
	 */
	const PATCH_LEVEL = 0;

	/**
	 * Extra information to append to the version string.
	 *
	 * @var    string
	 * @since  1.0
	 */
	const EXTRA_LEVEL = '-dev';

	/**
	 * Development status.
	 *
	 * @var    string
	 * @since  1.0
	 */
	const DEV_STATUS = 'Development';

	/**
	 * Code name
	 *
	 * @var    string
	 * @since  1.0
	 */
	const CODENAME = 'Refresh';

	/**
	 * Release date.
	 *
	 * @var    string
	 * @since  1.0
	 */
	const RELEASE_DATE = '2015-03-15';

	/**
	 * Release time.
	 *
	 * @var    string
	 * @since  1.0
	 */
	const RELEASE_TIME = '23:00';

	/**
	 * Timezone the release time is calculated on.
	 *
	 * @var    string
	 * @since  1.0
	 */
	const RELEASE_TIMEZONE = 'GMT';

	/**
	 * Project copyright notice.
	 *
	 * @var    string
	 * @since  1.0
	 */
	const COPYRIGHT = 'Copyright (C) Open Source Matters, Inc. All rights reserved.';

	/**
	 * Link text.
	 *
	 * @var    string
	 * @since  1.0
	 */
	const URL = '<a href="http://www.joomla.org">Joomla!</a> is Free Software released under the GNU General Public License.';

	/**
	 * Compares two a "PHP standardized" version number against the current application version.
	 *
	 * @param   string  $minimum  The minimum version of the application which is compatible.
	 *
	 * @return  boolean
	 *
	 * @see     http://www.php.net/version_compare
	 * @since   1.0
	 */
	public function isCompatible($minimum)
	{
		// A user may have customized the JVERSION constant (or tried using this before it was defined) so double check it's defined
		$compareVersion = defined('JVERSION') ? JVERSION : $this->getShortVersion();

		return version_compare($compareVersion, $minimum, 'ge');
	}

	/**
	 * Method to get the help file version.
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	public function getHelpVersion()
	{
		return '.' . Version::MAJOR_LEVEL . Version::MINOR_LEVEL;
	}

	/**
	 * Gets a "PHP standardized" version string for the current application.
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	public function getShortVersion()
	{
		return Version::MAJOR_LEVEL . '.' . Version::MINOR_LEVEL . '.' . Version::PATCH_LEVEL . Version::EXTRA_LEVEL;
	}

	/**
	 * Gets a version string for the current application with all release information.
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	public function getLongVersion()
	{
		return Version::PRODUCT . ' ' . $this->getShortVersion() . ' ' . Version::DEV_STATUS . ' [ ' . Version::CODENAME . ' ] '
			. Version::RELEASE_DATE . ' ' . Version::RELEASE_TIME . ' ' . Version::RELEASE_TIMEZONE;
	}

	/**
	 * Returns the user agent.
	 *
	 * @param   string   $component   Name of the component.
	 * @param   boolean  $mask        Flag to mask the user agent.
	 * @param   boolean  $addVersion  Append a version string to the user agent.
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	public function getUserAgent($component = null, $mask = false, $addVersion = true)
	{
		$component = ($component === null) ? 'Framework' : $component;

		if ($addVersion)
		{
			$component .= '/' . Version::MAJOR_LEVEL . '.' . Version::MINOR_LEVEL;
		}

		// If masked pretend to look like Mozilla 5.0 but still identify ourselves.
		if ($mask)
		{
			return 'Mozilla/5.0 ' . Version::PRODUCT . '/' . $this->getShortVersion() . ($component ? ' ' . $component : '');
		}

		return Version::PRODUCT . '/' . $this->getShortVersion() . ($component ? ' ' . $component : '');
	}
}
