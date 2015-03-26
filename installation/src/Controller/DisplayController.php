<?php
/**
 * Joomla! Next Installation Application
 *
 * @copyright  Copyright (C) Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

namespace Installation\Controller;

use Installation\Model\SetupModel;
use Joomla\CMS\Renderer\LayoutRenderer;
use Joomla\Controller\AbstractController;
use Joomla\DI\ContainerAwareInterface;
use Joomla\DI\ContainerAwareTrait;
use Joomla\Model\ModelInterface;
use Joomla\View\BaseHtmlView;

/**
 * Controller for managing displaying a page
 *
 * @since  1.0
 */
class DisplayController extends AbstractController implements ContainerAwareInterface
{
	use ContainerAwareTrait;

	/**
	 * The default view
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $defaultView = 'site';

	/**
	 * {@inheritdoc}
	 *
	 * @since   1.0
	 */
	public function execute()
	{
		// Get the application
		/* @var \Installation\Application $app */
		$app = $this->getApplication();

		// Get the document object.
		$document = $app->getDocument();

		// Set the default view name and format from the request.
		if (file_exists(JPATH_CONFIGURATION . '/configuration.php') && (filesize(JPATH_CONFIGURATION . '/configuration.php') > 10)
			&& file_exists(JPATH_INSTALLATION . '/index.php'))
		{
			$this->defaultView = 'remove';
		}

		$vName   = $this->getInput()->getWord('view', $this->defaultView);
		$vFormat = $document->getType();
		$lName   = $this->getInput()->getWord('layout', 'default');

		if (strcmp($vName, $this->defaultView) == 0)
		{
			$this->getInput()->set('view', $this->defaultView);
		}

		/** @var \Joomla\Language\LanguageFactory $langFactory */
		$langFactory = $this->getContainer()->get('Joomla\\Language\\LanguageFactory');

		switch ($vName)
		{
			case 'preinstall':
				$model = new SetupModel;
				$model->setText($langFactory->getText());

				$sufficient   = $model->getPhpOptionsSufficient();
				$checkOptions = false;
				$options      = $model->getOptions();

				if ($sufficient)
				{
					$app->redirect('index.php');
				}

				break;

			case 'languages':
			case 'defaultlanguage':
				$model = new InstallationModelLanguages;
				$checkOptions = false;
				$options = array();
				break;

			default:
				$model = new SetupModel;
				$model->setText($langFactory->getText());

				$sufficient   = $model->getPhpOptionsSufficient();
				$checkOptions = true;
				$options      = $model->getOptions();

				if (!$sufficient)
				{
					$app->redirect('index.php?view=preinstall');
				}

				break;
		}

		if ($vName != $this->defaultView && ($checkOptions && empty($options)))
		{
			$app->redirect('index.php');
		}

		// Initialize our view
		$view = $this->initialiseView($model);

		// Render our view and return it to the application.
		$this->getApplication()->getDocument()->setBuffer($view->render(), ['type' => 'component']);

		return true;
	}

	/**
	 * Method to initialize the view object
	 *
	 * @param   \Joomla\Model\ModelInterface  $model  Model to inject into the view class
	 *
	 * @return  \Joomla\View\BaseHtmlView  View object
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public function initialiseView(ModelInterface $model)
	{
		$view   = ucfirst($this->getInput()->getWord('view', $this->defaultView));
		$format = ucfirst($this->getInput()->getWord('format', 'html'));

		// HTML classes need a RendererInterface implementation, create it if need be
		if ($format == 'Html')
		{
			$renderer = new LayoutRenderer($this->getApplication()->getTemplate());

			$this->getContainer()->set('Joomla\\CMS\\Renderer\\LayoutRenderer', $renderer)
				->alias('Joomla\\Renderer\\RendererInterface', 'Joomla\\CMS\\Renderer\\LayoutRenderer');
		}

		$class = 'Installation\\View\\' . $view . '\\' . $view . $format . 'View';

		// Ensure the class exists, fall back to the default view otherwise
		if (!class_exists($class))
		{
			$class = 'Installation\\View\\Default' . $format . 'View';

			// If we still have nothing, abort mission
			if (!class_exists($class))
			{
				throw new \RuntimeException(
					$this->getApplication()->getLanguage()->getText()->sprintf('INSTL_VIEW_NOT_FOUND', $view, $format)
				);
			}
		}

		$object = new $class($model, $renderer);

		// If we have an HTML view, we also need to set the layout
		if ($object instanceof BaseHtmlView)
		{
			$object->setLayout('install.' . strtolower($view) . '.' . strtolower($this->getInput()->getString('layout', 'default')));
		}

		return $object;
	}
}
