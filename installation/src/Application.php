<?php
/**
 * Joomla! Next Installation Application
 *
 * @copyright  Copyright (C) Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

namespace Installation;

use Joomla\Application\AbstractWebApplication;
use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Application\CMSApplicationTrait;
use Joomla\CMS\Document\DocumentFactory;
use Joomla\CMS\Language\LanguageHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\User;
use Joomla\DI\Container;
use Joomla\DI\ContainerAwareInterface;
use Joomla\DI\ContainerAwareTrait;
use Joomla\Language\Language;
use Joomla\Registry\Registry;
use Joomla\Session\Session;

/**
 * CMS installation application class.
 *
 * @since  1.0
 */
final class Application extends AbstractWebApplication implements CMSApplicationInterface, ContainerAwareInterface
{
	use CMSApplicationTrait, ContainerAwareTrait;

	/**
	 * Class constructor.
	 *
	 * @param   Container  $container  DI Container
	 *
	 * @since   1.0
	 */
	public function __construct(Container $container)
	{
		$this->setContainer($container);

		// Register this to the DI container now
		$this->getContainer()->protect('Installation\\Application', $this)
			->alias('Joomla\\CMS\\Application\\CMSApplicationInterface', 'Installation\\Application')
			->alias('Joomla\\Application\\AbstractWebApplication', 'Installation\\Application')
			->alias('Joomla\\Application\\AbstractApplication', 'Installation\\Application')
			->alias('app', 'Installation\\Application');

		parent::__construct();

		$this->registerServices();
		$this->loadSession();

		// Store the debug value to config based on the JDEBUG flag.
		$this->set('debug', JDEBUG);

		// Create the base URI object for the application
		$uri = Uri::getInstance();

		// Set the base URI
		$baseUri = (array) $this->get('uri.base');
		$uri->setBase($baseUri);

		// Now set the root URI
		$parts = explode('/', $baseUri['full']);
		array_pop($parts);
		$baseUri['full'] = implode('/', $parts);
		$uri->setRoot($baseUri);
	}

	/**
	 * {@inheritdoc}
	 *
	 * @since  1.0
	 */
	protected function doExecute()
	{
		$this->initialiseApp();

		try
		{
			// Retrieve a document object
			$lang = $this->getLanguage();
			$type = $this->input->getWord('format', 'html');

			$attributes = array(
				'characterSet' => 'utf-8',
				'language' => $lang->getTag(),
				'direction' => $lang->isRTL() ? 'rtl' : 'ltr'
			);

			$this->setDocument((new DocumentFactory)->getDocument($type, $attributes));

			// Set up the params
			$document = $this->getDocument();
			$this->getContainer()->share('Joomla\\CMS\\Document\\AbstractDocument', $document)
				->alias('Joomla\\CMS\\Document\\DocumentInterface', 'Joomla\\CMS\\Document\\AbstractDocument')
				->alias('document', 'Joomla\\CMS\\Document\\AbstractDocument');

			if ($document->getType() == 'html')
			{
				// Set metadata
				$document->setTitle($lang->getText()->translate('INSTL_PAGE_TITLE'));
			}

			$controller = $this->fetchController($this->input->getCmd('task', 'display'));
			$controller->execute();

			// If debug language is set, append its output to the contents.
			if ($this->get('language.debug'))
			{
				$buffer   = $document->getBuffer();
				$contents = $buffer['component'] . $this->debugLanguage();
				$document->setBuffer($contents, ['type' => 'component']);
			}

			$file = $this->input->getCmd('tmpl', 'index');

			$options = [
				'template'  => $this->getTemplate(),
				'file'      => $file . '.php',
				'directory' => JPATH_THEMES,
				'params'    => '{}'
			];

			// Render the response
			$this->setBody($document->render(false, $options));
		}
		catch (\Exception $e)
		{
			var_dump($e);die;
			echo $e->getMessage();
			$this->close($e->getCode());
		}
	}

	/**
	 * Fetch a controller for the requested task
	 *
	 * @param   string  $task  The task being executed in a dotted notation (i.e. install.config)
	 *
	 * @return  BaseController
	 *
	 * @since   1.0
	 * @throws  \RuntimeException if a controller for the task is not found
	 */
	protected function fetchController($task)
	{
		// Explode the task out so we can assemble the name
		$pieces = explode('.', $task);

		// Set the controller class name based on the task.
		$class = __NAMESPACE__ . '\\Controller';

		foreach ($pieces as $piece)
		{
			$class .= '\\' . ucfirst(strtolower($piece));
		}

		$class .= 'Controller';

		// If the requested controller exists let's use it.
		if (class_exists($class))
		{
			$controller = $this->getContainer()->buildObject($class);

			if ($controller instanceof ContainerAwareInterface)
			{
				$controller->setContainer($this->getContainer());
			}

			return $controller;
		}

		// Nothing found. Panic.
		throw new \RuntimeException(
			$this->getLanguage()->getText()->sprintf('INSTL_CONTROLLER_NOT_FOUND', $task)
		);
	}

	/**
	 * Returns the language code and help url set in the localise.xml file.
	 *
	 * Used for forcing a particular language in localised releases.
	 *
	 * @return  array|boolean  False on failure, array on success.
	 *
	 * @since   1.0
	 */
	public function getLocalise()
	{
		$localiseFile = JPATH_INSTALLATION . '/localise.xml';

		// Does the file even exist?
		if (!file_exists($localiseFile))
		{
			return false;
		}

		$xml = simplexml_load_file($localiseFile);

		if (!$xml)
		{
			return false;
		}

		// Check that it's a localise file.
		if ($xml->getName() != 'localise')
		{
			return false;
		}

		return [
			'language'   => (string) $xml->forceLang,
			'helpurl'    => (string) $xml->helpurl,
			'debug'      => (string) $xml->debug,
			'sampledata' => (string) $xml->sampledata
		];
	}

	/**
	 * {@inheritdoc}
	 *
	 * @since  1.0
	 */
	public function getName()
	{
		return 'installation';
	}

	/**
	 * Gets the name of the current template.
	 *
	 * @param   boolean  $params  True to return the template parameters.
	 *
	 * @return  \stdClass|string  The name of the template or an object containing the template name and parameters.
	 *
	 * @since   1.0
	 */
	public function getTemplate($params = false)
	{
		if ($params)
		{
			$template = new \stdClass;
			$template->template = 'joomla';
			$template->params = new Registry;

			return $template;
		}

		return 'joomla';
	}

	/**
	 * {@inheritdoc}
	 *
	 * @since  1.0
	 */
	public function initialiseApp($options = array())
	{
		// Check for localisation information provided in a localise.xml file.
		$forced = $this->getLocalise();

		// Check the request data for the language.
		if (empty($options['language']))
		{
			$requestLang = $this->input->getCmd('lang', null);

			if (!is_null($requestLang))
			{
				$options['language'] = $requestLang;
			}
		}

		// Check the session for the language.
		if (empty($options['language']))
		{
			$sessionOptions = $this->getSession()->get('setup.options');

			if (isset($sessionOptions['language']))
			{
				$options['language'] = $sessionOptions['language'];
			}
		}

		// This could be a first-time visit - try to determine what the client accepts.
		if (empty($options['language']))
		{
			if (!empty($forced['language']))
			{
				$options['language'] = $forced['language'];
			}
			else
			{
				$languageHelper = new LanguageHelper;
				$languageHelper->setContainer($this->getContainer());
				$options['language'] = $languageHelper->detectLanguage();

				if (empty($options['language']))
				{
					$options['language'] = 'en-GB';
				}
			}
		}

		// Last resort, give the user English.
		if (empty($options['language']))
		{
			$options['language'] = 'en-GB';
		}

		// Check for a custom help URL.
		if (empty($forced['helpurl']))
		{
			$options['helpurl'] = 'https://help.joomla.org/proxy/index.php?option=com_help&amp;keyref=Help{major}{minor}:{keyref}';
		}
		else
		{
			$options['helpurl'] = $forced['helpurl'];
		}

		// Store the help URL in the session.
		$this->getSession()->set('setup.helpurl', $options['helpurl']);

		// Set the language configuration.
		$this->set('language.code', $options['language']);
		$this->set('language.debug', $forced['debug']);
		$this->set('sampledata', $forced['sampledata']);
		$this->set('helpurl', $options['helpurl']);

		// Instantiate our Langauge instance
		$this->setLanguage(Language::getInstance($this->get('language.code'), JPATH_INSTALLATION, $this->get('language.debug')));
		$this->getContainer()->share('Joomla\\Language\\Language', $this->getLanguage())
			->alias('language', 'Joomla\\Language\\Language');
	}

	/**
	 * {@inheritdoc}
	 *
	 * @since  1.0
	 */
	public function isCli()
	{
		return false;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @since  1.0
	 */
	public function loadIdentity(User $user = null)
	{
		// The install application doesn't have the concept of an identity, so for now just return a null
		return;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @since  1.0
	 */
	public function loadSession(Session $session = null)
	{
		// Initialize the options for Session.
		$options = array(
			'name'      => md5($this->get('uri.base.host') . $this->getName()),
			'expire'    => 900
		);

		// Instantiate the session object.
		$session = Session::getInstance('none', $options);
		$session->initialise($this->input);

		if ($session->getState() == 'expired')
		{
			$session->restart();
		}
		else
		{
			$session->start();
		}

		if (!$session->get('registry') instanceof Registry)
		{
			// Registry has been corrupted somehow.
			$session->set('registry', new Registry('session'));
		}

		// Set the session object.
		$this->setSession($session);
		$this->getContainer()->share('Joomla\\Session\\Session', $session)
			->alias('session', 'Joomla\\Session\\Session');

		return $this;
	}
}
