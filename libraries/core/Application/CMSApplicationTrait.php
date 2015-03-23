<?php
/**
 * Joomla! Next Application Platform
 *
 * @copyright  Copyright (C) Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

namespace Joomla\CMS\Application;

use Joomla\CMS\Document\DocumentFactory;
use Joomla\CMS\Document\DocumentInterface;
use Joomla\Language\Language;

/**
 * Trait for defining common methods in CMS Application classes
 *
 * @since  1.0
 */
trait CMSApplicationTrait
{
	/**
	 * The application document object.
	 *
	 * @var    DocumentInterface
	 * @since  1.0
	 */
	private $document;

	/**
	 * The application Language object.
	 *
	 * @var    Language
	 * @since  1.0
	 */
	private $language;

	/**
	 * The application message queue.
	 *
	 * @var    array
	 * @since  1.0
	 */
	protected $messageQueue = [];

	/**
	 * Flag if the application services have already been loaded
	 *
	 * @var    boolean
	 * @since  1.0
	 */
	private $servicesLoaded = false;

	/**
	 * Prepares the DocumentFactory for the application
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function buildDocumentFactory()
	{
		DocumentFactory::setContainer($this->getContainer());
	}

	/**
	 * Enqueue a system message.
	 *
	 * @param   string  $msg   The message to enqueue.
	 * @param   string  $type  The message type. Default is message.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function enqueueMessage($msg, $type = 'message')
	{
		// Don't add empty messages.
		if (!strlen($msg))
		{
			return;
		}

		// For empty queue, if messages exists in the session, enqueue them first.
		$this->getMessageQueue();

		// Enqueue the message.
		$this->messageQueue[] = array('message' => $msg, 'type' => strtolower($type));
	}

	/**
	 * Retrieve the document object.
	 *
	 * @return  DocumentInterface
	 *
	 * @since   1.0
	 */
	public function getDocument()
	{
		if ($this->document)
		{
			return $this->document;
		}

		throw new \RuntimeException('Document object not registered in ' . __CLASS__);
	}

	/**
	 * Retrieve the language object.
	 *
	 * @return  Language
	 *
	 * @since   1.0
	 */
	public function getLanguage()
	{
		if ($this->language)
		{
			return $this->language;
		}

		throw new \RuntimeException('Language object not registered in ' . __CLASS__);
	}

	/**
	 * Get the system message queue.
	 *
	 * @return  array  The system message queue.
	 *
	 * @since   1.0
	 */
	public function getMessageQueue()
	{
		// For empty queue, if messages exists in the session, enqueue them.
		if (!count($this->messageQueue))
		{
			$session      = $this->getSession();
			$sessionQueue = $session->get('application.queue');

			if (count($sessionQueue))
			{
				$this->messageQueue = $sessionQueue;
				$session->set('application.queue', null);
			}
		}

		return $this->messageQueue;
	}

	/**
	 * Registers the services for a CMS application
	 *
	 * This method should be called in the application constructor or initialise method to ensure services are established
	 * at the earliest point practical in bootup.
	 *
	 * @return  void
	 *
	 * @note    Each service loader is called separately to enable an application to override the service if needed
	 * @since   1.0
	 */
	protected function registerServices()
	{
		// Only load once
		if ($this->servicesLoaded)
		{
			throw new \RuntimeException('Services are already loaded for the application.');
		}

		$this->buildDocumentFactory();

		$this->servicesLoaded = true;
	}

	/**
	 * Set the document object.
	 *
	 * @param   DocumentInterface  $document  The document object.
	 *
	 * @return  $this
	 *
	 * @since   1.0
	 */
	public function setDocument(DocumentInterface $document)
	{
		$this->document = $document;

		return $this;
	}

	/**
	 * Set the language object.
	 *
	 * @param   Language  $language  The Language object.
	 *
	 * @return  $this
	 *
	 * @since   1.0
	 */
	public function setLanguage(Language $language)
	{
		$this->language = $language;

		return $this;
	}
}
