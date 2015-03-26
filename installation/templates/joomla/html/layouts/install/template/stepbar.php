<?php
/**
 * Joomla! Next Installation Application
 *
 * @copyright  Copyright (C) Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

/** @var array $displayData */

/** @var \Joomla\CMS\Renderer\LayoutRenderer $renderer */
$renderer = $displayData['renderer'];

/** @var \Joomla\Language\Text $text */
$text = $displayData['layoutHelpers']['translate']->getTranslator();

// Determine if the configuration file path is writable.
$path = JPATH_CONFIGURATION . '/configuration.php';
$useftp = (file_exists($path)) ? !is_writable($path) : !is_writable(JPATH_CONFIGURATION . '/');

$tabs = array();
$tabs[] = 'site';
$tabs[] = 'database';

if ($useftp)
{
	$tabs[] = 'ftp';
}

$tabs[] = 'summary';

?>
<ul class="nav nav-tabs">
	<?php foreach ($tabs as $tab) : ?>
		<?php echo $renderer->render('install.template.stepbar_tab', ['currentView' => $displayData['currentView'], 'tabs' => $tabs, 'currentTab' => $tab]); ?>
	<?php endforeach; ?>
</ul>
