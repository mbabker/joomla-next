<?php
/**
 * Joomla! Next Installation Application
 *
 * @copyright  Copyright (C) Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

namespace Installation\View;

use Joomla\View\BaseHtmlView;

/**
 * Default HTML view class for the installation application
 *
 * @property-read  \Installation\Model\SetupModel  $model  The model object.
 *
 * @since  3.1
 */
class DefaultHtmlView extends BaseHtmlView
{
	/**
	 * {@inheritdoc}
	 *
	 * @since  1.0
	 */
	public function render()
	{
		// Extract the view name based on the layout
		$layout = explode('.', $this->getLayout());

		$this->addData('form', $this->model->getForm($layout[1]));

		// TODO - Need a renderer helper for things like accessing the Text object in layouts
		$this->addData('text', $this->model->getText());

		return parent::render();
	}
}
