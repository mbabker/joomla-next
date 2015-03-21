<?php
/**
 * Joomla! Next Application Platform
 *
 * @copyright  Copyright (C) Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

namespace Joomla\CMS\Document;

/**
 * Interface representing a Document object
 *
 * @since  1.0
 */
interface DocumentInterface
{
	/**
	 * Get the contents of the document buffer.
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function getBuffer();

	/**
	 * Returns the document character set.
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	public function getCharacterSet();

	/**
	 * Returns the document direction declaration.
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	public function getDirection();

	/**
	 * Returns the document language.
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	public function getLanguage();

	/**
	 * Return the document MIME encoding that is sent to the browser.
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	public function getMimeEncoding();

	/**
	 * Returns the document modified date
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	public function getModifiedDate();

	/**
	 * Returns the document type.
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	public function getType();

	/**
	 * Parses the document and prepares the buffers
	 *
	 * @param   array  $params  The array of parameters
	 *
	 * @return  $this
	 *
	 * @since   1.0
	 */
	public function parse($params = []);

	/**
	 * Outputs the document
	 *
	 * @param   boolean  $cache   If true, cache the output
	 * @param   array    $params  Associative array of attributes
	 *
	 * @return  string  The rendered data
	 *
	 * @since   1.0
	 */
	public function render($cache = false, $params = []);

	/**
	 * Set the contents of the document buffer.
	 *
	 * @param   string  $content  The content to be set in the buffer.
	 * @param   array   $options  Array of optional elements.
	 *
	 * @return  $this
	 *
	 * @since   1.0
	 */
	public function setBuffer($content, $options = []);

	/**
	 * Sets the document character set.
	 *
	 * @param   string  $charSet  Character set encoding string
	 *
	 * @return  $this
	 *
	 * @since   1.0
	 */
	public function setCharacterSet($charSet = 'utf-8');

	/**
	 * Sets the document direction declaration.
	 *
	 * @param   string  $dir  The language direction to be set
	 *
	 * @return  $this
	 *
	 * @since   1.0
	 */
	public function setDirection($dir = 'ltr');

	/**
	 * Sets the document language declaration.
	 *
	 * @param   string  $lang  The language to be set.
	 *
	 * @return  $this
	 *
	 * @since   1.0
	 */
	public function setLanguage($lang = 'en-gb');

	/**
	 * Sets the document MIME encoding that is sent to the browser.
	 *
	 * @param   string   $type  The document type to be sent
	 * @param   boolean  $sync  Should the type be synced with HTML?
	 *
	 * @return  $this
	 *
	 * @link    http://www.w3.org/TR/xhtml-media-types
	 * @since   1.0
	 */
	public function setMimeEncoding($type = 'text/html', $sync = true);

	/**
	 * Sets the document modified date
	 *
	 * @param   string  $date  The date to be set
	 *
	 * @return  $this
	 *
	 * @since   1.0
	 */
	public function setModifiedDate($date);

	/**
	 * Set the document type.
	 *
	 * @param   string  $type  Type document is to set to
	 *
	 * @return  $this
	 *
	 * @since   1.0
	 */
	public function setType($type);
}
