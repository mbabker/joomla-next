<?php
/**
 * Joomla! Next Installation Application
 *
 * @copyright  Copyright (C) Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

if (!function_exists('getTabNumber'))
{
	function getTabNumber($id, $tabs)
	{
		$num = (int) array_search($id, $tabs);
		$num++;

		return $num;
	}
}

/** @var array $displayData */

/** @var \Joomla\CMS\Renderer\LayoutRenderer $renderer */
$renderer = $displayData['renderer'];

/** @var \Joomla\Language\Text $text */
$text = $displayData['layoutHelpers']['translate']->getTranslator();

$num   = getTabNumber($displayData['currentTab'], $displayData['tabs']);
$view  = getTabNumber($displayData['currentView'], $displayData['tabs']);

$tab   = '<span class="badge">' . $num . '</span> ' . $text->translate('INSTL_STEP_' . strtoupper($displayData['currentTab']) . '_LABEL');

if ($view + 1 == $num) :
	$tab = '<a href="#" onclick="Install.submitform();">' . $tab . '</a>';
elseif ($view < $num) :
	$tab = '<span>' . $tab . '</span>';
else :
	$tab = '<a href="#" onclick="return Install.goToPage(\'' . $displayData['currentTab'] . '\')">' . $tab . '</a>';
endif;

?>
<li class="step<?php echo $num == $view ? ' active' : ''; ?>" id="<?php echo $displayData['currentTab']; ?>"><?php echo $tab; ?></li>
