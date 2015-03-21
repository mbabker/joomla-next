<?php
/**
 * Joomla! Next Application Platform
 *
 * @copyright  Copyright (C) Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

namespace Joomla\CMS\Document;

use Joomla\CMS\Application\CMSApplicationInterface;

/**
 * Base implementation of the DocumentInterface
 *
 * @since  1.0
 */
abstract class AbstractDocument implements DocumentInterface
{
	/**
	 * Application object
	 *
	 * @var    CMSApplicationInterface
	 * @since  1.0
	 */
	protected $application;

	/**
	 * Container for the document output buffer
	 *
	 * @var    array
	 * @since  1.0
	 */
	protected $buffer = [];

	/**
	 * The document's character set
	 *
	 * @var    string
	 * @since  1.0
	 */
	private $characterSet = 'utf-8';

	/**
	 * The document's output direction
	 *
	 * @var    string
	 * @since  1.0
	 */
	private $direction = 'ltr';

	/**
	 * The document's language
	 *
	 * @var    string
	 * @since  1.0
	 */
	private $language = 'en-gb';

	/**
	 * Document modified date
	 *
	 * @var    string
	 * @since  1.0
	 */
	private $modifiedDate = '';

	/**
	 * Document MIME type
	 *
	 * @var    string
	 * @since  1.0
	 */
	private $mime = '';

	/**
	 * The document type
	 *
	 * @var    string
	 * @since  1.0
	 */
	private $type = null;

	/**
	 * Retrieves the application reference
	 *
	 * @return  CMSApplicationInterface
	 *
	 * @since   1.0
	 */
	public function getApplication()
	{
		if ($this->application)
		{
			return $this->application;
		}

		throw new \RuntimeException('The application object is not set to the document class.');
	}

	/**
	 * {@inheritdoc}
	 *
	 * @since  1.0
	 */
	public function getBuffer()
	{
		return $this->buffer;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @since  1.0
	 */
	public function getCharacterSet()
	{
		return $this->characterSet;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @since  1.0
	 */
	public function getDirection()
	{
		return $this->direction;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @since  1.0
	 */
	public function getLanguage()
	{
		return $this->language;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @since  1.0
	 */
	public function getMimeEncoding()
	{
		return $this->_mime;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @since  1.0
	 */
	public function getModifiedDate()
	{
		return $this->modifiedDate;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @since  1.0
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @since  1.0
	 */
	public function parse($params = array())
	{
		return $this;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @since  1.0
	 */
	public function render($cache = false, $params = array())
	{
		// Make sure we have a web application
		if ($this->getApplication()->isCli())
		{
			return;
		}

		if ($mdate = $this->getModifiedDate())
		{
			$this->getApplication()->modifiedDate = $mdate;
		}

		$this->getApplication()->mimeType = $this->getMimeEncoding();
		$this->getApplication()->charSet  = $this->getCharacterSet();
	}

	/**
	 * Sets the document's application reference
	 *
	 * @param   CMSApplicationInterface  $application  Application reference
	 *
	 * @return  $this
	 *
	 * @since   1.0
	 */
	public function setApplication(CMSApplicationInterface $application)
	{
		$this->application = $application;

		return $this;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @since  1.0
	 */
	public function setBuffer($content, $options = array())
	{
		$this->buffer[] = $content;

		return $this;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @since  1.0
	 */
	public function setCharacterSet($charSet = 'utf-8')
	{
		$this->characterSet = $charSet;

		return $this;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @since  1.0
	 */
	public function setDirection($dir = "ltr")
	{
		$this->direction = strtolower($dir);

		return $this;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @since  1.0
	 */
	public function setLanguage($lang = "en-gb")
	{
		$this->language = strtolower($lang);

		return $this;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @since  1.0
	 */
	public function setMimeEncoding($type = 'text/html', $sync = true)
	{
		$this->_mime = strtolower($type);

		// Syncing with meta-data
		if ($sync)
		{
			//$this->setMetaData('content-type', $type . '; charset=' . $this->characterSet, true);
		}

		return $this;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @since  1.0
	 */
	public function setModifiedDate($date)
	{
		$this->modifiedDate = $date;

		return $this;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @since  1.0
	 */
	public function setType($type)
	{
		$this->type = $type;

		return $this;
	}
}
