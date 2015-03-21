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
 * HTML renderer for rendering the document <head>
 *
 * @since  1.0
 */
class Head extends AbstractDocumentRenderer
{
	/**
	 * {@inheritdoc}
	 *
	 * @since  1.0
	 */
	public function render($head, $params = array(), $content = null)
	{
		return $this->fetchHead();
	}

	/**
	 * Generates the head HTML and return the results as a string
	 *
	 * @return  string  The head hTML
	 *
	 * @since   1.0
	 */
	public function fetchHead()
	{
		// Still broken, return empty string
		return '';

		$document = $this->doc;

		// Get line endings
		$tagEnd = ' />';
		$buffer = '';

		// Generate charset when using HTML5 (should happen first)
		if ($document->isHtml5())
		{
			$buffer .= "\t" . '<meta charset="' . $document->getCharacterSet() . '" />' . "\n";
		}

		// Generate base tag (need to happen early)
		$base = $document->getBase();

		if (!empty($base))
		{
			$buffer .= "\t" . '<base href="' . $base . '" />' . "\n";
		}

		// Generate META tags (needs to happen as early as possible in the head)
		foreach ($document->_metaTags as $type => $tag)
		{
			foreach ($tag as $name => $content)
			{
				if ($type == 'http-equiv' && !($document->isHtml5() && $name == 'content-type'))
				{
					$buffer .= "\t" . '<meta http-equiv="' . $name . '" content="' . htmlspecialchars($content) . '" />' . "\n";
				}
				elseif ($type == 'standard' && !empty($content))
				{
					$buffer .= "\t" . '<meta name="' . $name . '" content="' . htmlspecialchars($content) . '" />' . "\n";
				}
			}
		}

		// Don't add empty descriptions
		$documentDescription = $document->getDescription();

		if ($documentDescription)
		{
			$buffer .= "\t" . '<meta name="description" content="' . htmlspecialchars($documentDescription) . '" />' . "\n";
		}

		// Don't add empty generators
		$generator = $document->getGenerator();

		if ($generator)
		{
			$buffer .= "\t" . '<meta name="generator" content="' . htmlspecialchars($generator) . '" />' . "\n";
		}

		$buffer .= "\t" . '<title>' . htmlspecialchars($document->getTitle(), ENT_COMPAT, 'UTF-8') . '</title>' . "\n";

		// Generate link declarations
		foreach ($document->_links as $link => $linkAtrr)
		{
			$buffer .= "\t" . '<link href="' . $link . '" ' . $linkAtrr['relType'] . '="' . $linkAtrr['relation'] . '"';

			if ($temp = ArrayHelper::toString($linkAtrr['attribs']))
			{
				$buffer .= ' ' . $temp;
			}

			$buffer .= ' />' . "\n";
		}

		// Generate stylesheet links
		foreach ($document->_styleSheets as $strSrc => $strAttr)
		{
			$buffer .= "\t" . '<link rel="stylesheet" href="' . $strSrc . '"';

			if (!is_null($strAttr['mime']) && (!$document->isHtml5() || $strAttr['mime'] != 'text/css'))
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
		foreach ($document->_style as $type => $content)
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

		// Generate script file links
		foreach ($document->_scripts as $strSrc => $strAttr)
		{
			$buffer .= "\t" . '<script src="' . $strSrc . '"';
			$defaultMimes = array(
				'text/javascript', 'application/javascript', 'text/x-javascript', 'application/x-javascript'
			);

			if (!is_null($strAttr['mime']) && (!$document->isHtml5() || !in_array($strAttr['mime'], $defaultMimes)))
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
		foreach ($document->_script as $type => $content)
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
		$text = $this->doc->getApplication()->getLanguage()->getText();

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

		foreach ($document->_custom as $custom)
		{
			$buffer .= "\t" . $custom . "\n";
		}

		return $buffer;
	}
}
