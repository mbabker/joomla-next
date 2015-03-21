<?php
/**
 * Joomla! Next Installation Application
 *
 * @copyright  Copyright (C) Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

namespace Installation\Form\Rule;

use Joomla\Form\Rule;

/**
 * Form Rule class for the prefix DB.
 *
 * @since  1.0
 */
class PrefixRule extends Rule
{
	/**
	 * The regular expression to use in testing a form field value.
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $regex = '^[a-z][a-z0-9]*_$';

	/**
	 * The regular expression modifiers to use when testing a form field value.
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $modifiers = 'i';
}
