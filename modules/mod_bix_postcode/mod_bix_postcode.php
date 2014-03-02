<?php
/**
 *	com_bix_printshop - Online-PrintStore for Joomla
 *  Copyright (C) 2010-2012 Matthijs Alles
 *	Bixie.nl
 *
 */

// no direct access
defined('_JEXEC') or die;


$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));
$document = JFactory::getDocument();

$document->addScript('plugins/system/bixsystem/assets/mod_bix_postcode.js');
$id = uniqid();
$js = "window.addEvent('domready',function() {"
			."bixPostcode = new BixPostcode('postcode','check','register',{"
				."spinnerclass: 'uk-icon-spinner uk-icon-spin',"
				."iconclass: 'uk-icon-sign-in'"
			."});"
		."});";
$document->addScriptDeclaration($js);

require JModuleHelper::getLayoutPath('mod_bix_postcode', $params->get('layout', 'default'));
