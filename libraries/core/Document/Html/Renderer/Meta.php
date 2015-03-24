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
 * HTML renderer for rendering the document Meta tags
 *
 * @since  1.0
 */
class Meta extends AbstractDocumentRenderer
{
	/**
	 * {@inheritdoc}
	 *
	 * @since  1.0
	 */
	public function render($meta, $params = array(), $content = null)
	{
		return $this->fetchMeta();
	}

	/**
	 * Generates the head meta tags and return the results as a string
	 *
	 * @return  string  The head hTML
	 *
	 * @since   1.0
	 */
	public function fetchMeta()
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

		return $buffer;
	}
}
