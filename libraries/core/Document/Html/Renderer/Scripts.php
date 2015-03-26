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
 * HTML renderer for rendering the document scripts
 *
 * @since  1.0
 */
class Scripts extends AbstractDocumentRenderer
{
	/**
	 * {@inheritdoc}
	 *
	 * @since  1.0
	 */
	public function render($scripts, $params = array(), $content = null)
	{
		$document = $this->doc;

		// Get line endings
		$tagEnd = ' />';
		$buffer = '';

		// Generate script file links
		foreach ($document->getScripts() as $strSrc => $strAttr)
		{
			$buffer .= "\t" . '<script src="' . $strSrc . '"';
			$defaultMimes = array(
				'text/javascript', 'application/javascript', 'text/x-javascript', 'application/x-javascript'
			);

			if (!is_null($strAttr['mime']) && (!$document->getHtml5() || !in_array($strAttr['mime'], $defaultMimes)))
			{
				$buffer .= ' type="' . $strAttr['mime'] . '"';
			}

			if ($strAttr['defer'])
			{
				$buffer .= ' defer="defer"';
			}

			if ($strAttr['async'])
			{
				$buffer .= ' async="async"';
			}

			$buffer .= '></script>' . "\n";
		}

		// Generate script declarations
		foreach ($document->getScriptDeclarations() as $type => $content)
		{
			$buffer .= "\t" . '<script type="' . $type . '">' . "\n";

			// This is for full XHTML support.
			if ($document->_mime != 'text/html')
			{
				$buffer .= "\t" . "\t" . '//<![CDATA[' . "\n";
			}

			$buffer .= $content . "\n";

			// See above note
			if ($document->_mime != 'text/html')
			{
				$buffer .= "\t" . "\t" . '//]]>' . "\n";
			}

			$buffer .= "\t" . '</script>' . "\n";
		}

		// Generate script language declarations.
		$text = $this->doc->getContainer()->get('Joomla\\Language\\LanguageFactory')->getText();

		if (count($text->script()))
		{
			$buffer .= "\t" . '<script type="text/javascript">' . "\n";

			if ($document->_mime != 'text/html')
			{
				$buffer .= "\t" . "\t" . '//<![CDATA[' . "\n";
			}

			$buffer .= "\t" . "\t" . '(function() {' . "\n";
			$buffer .= "\t" . "\t" . "\t" . 'Joomla.JText.load(' . json_encode($text->script()) . ');' . "\n";
			$buffer .= "\t" . "\t" . '})();' . "\n";

			if ($document->_mime != 'text/html')
			{
				$buffer .= "\t" . "\t" . '//]]>' . "\n";
			}

			$buffer .= "\t" . '</script>' . "\n";
		}

		foreach ($document->getCustomTags() as $custom)
		{
			$buffer .= "\t" . $custom . "\n";
		}

		return $buffer;
	}
}
