<?php
/**
 * Joomla! Next Application Platform
 *
 * @copyright  Copyright (C) Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

namespace Joomla\CMS\Renderer;

/**
 * Interface defining a helper object for a renderer object
 *
 * @since  1.0
 */
interface RendererHelperInterface
{
	/**
	 * Retrieve the name of the helper object
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	public function getName();
}
