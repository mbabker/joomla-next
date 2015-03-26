<?php
/**
 * Joomla! Next Application Platform
 *
 * @copyright  Copyright (C) Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

namespace Joomla\CMS\Document\Html\Renderer;

use Joomla\CMS\Document\AbstractDocumentRenderer;
use Joomla\Utilities\ArrayHelper;

/**
 * HTML renderer for rendering the document stylesheets
 *
 * @since  1.0
 */
class Stylesheets extends AbstractDocumentRenderer
{
	/**
	 * {@inheritdoc}
	 *
	 * @since  1.0
	 */
	public function render($stylesheets, $params = array(), $content = null)
	{
		$document = $this->doc;

		// Get line endings
		$tagEnd = ' />';
		$buffer = '';

		// Generate stylesheet links
		foreach ($document->getStylesheets() as $strSrc => $strAttr)
		{
			$buffer .= "\t" . '<link rel="stylesheet" href="' . $strSrc . '"';

			if (!is_null($strAttr['mime']) && (!$document->getHtml5() || $strAttr['mime'] != 'text/css'))
			{
				$buffer .= ' type="' . $strAttr['mime'] . '"';
			}

			if (!is_null($strAttr['media']))
			{
				$buffer .= ' media="' . $strAttr['media'] . '"';
			}

			if ($temp = ArrayHelper::toString($strAttr['attribs']))
			{
				$buffer .= ' ' . $temp;
			}

			$buffer .= $tagEnd . "\n";
		}

		// Generate stylesheet declarations
		foreach ($document->getStyleDeclarations() as $type => $content)
		{
			$buffer .= "\t" . '<style type="' . $type . '">' . "\n";

			// This is for full XHTML support.
			if ($document->_mime != 'text/html')
			{
				$buffer .= "\t" . "\t" . '/*<![CDATA[*/' . "\n";
			}

			$buffer .= $content . "\n";

			// See above note
			if ($document->_mime != 'text/html')
			{
				$buffer .= "\t" . "\t" . '/*]]>*/' . "\n";
			}

			$buffer .= "\t" . '</style>' . "\n";
		}

		return $buffer;
	}
}
