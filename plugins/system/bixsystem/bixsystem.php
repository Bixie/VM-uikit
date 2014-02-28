<?php
/**
 *	COM_BIXPRINTSHOP - Online-PrintStore for Joomla
 *  Copyright (C) 2010-2012 Matthijs Alles
 *	Bixie.nl
 *
 */

// No direct access
defined('_JEXEC') or die;

class plgSystemBixsystem extends JPlugin {

	/*Statics
	* must be called from within VM!
	*/
	public static function noProducts () {
		jimport('joomla.application.module.helper');
		//init vars
		$document = JFactory::getDocument();
		$html = '';
		$position = false;
		if (!self::validUser()) { //geen valid user
			$position = 'noproducts';
		}
		//modulehelper
		if ($position) {
			$renderer = $document->loadRenderer('module');
			foreach (JModuleHelper::getModules($position) as $mod)  {
				$html .= $renderer->render($mod, array('style'=>'blank'));
			}
		}
// echo '<pre style="margin-top:100px;">';
// print_r($vmUser);
// echo '</pre>';
		return $html;
	}
	
	public static function validUser () {
		jimport('joomla.plugin.helper');
		//plugin params
		$bixPlugin = JPluginHelper::getPlugin('system','bixsystem');
		$bixParams = new JRegistry();
		$bixParams->loadString($bixPlugin->params);
		//vmuser
		$usermodel = VmModel::getModel('user');
		$vmUser = $usermodel->getUser();

		return in_array($bixParams->get('allowedGroup'),$vmUser->shopper_groups);
	}

	/*Events*/
	public function onUserLogin($user, $options = array()) {
		return true;
	}
	
	public function onAfterDispatch() {
		$app = JFactory::getApplication();
		if ($app->isAdmin()) {
			return true;
		}
		$option = JRequest::getCmd('option');
		$view = JRequest::getCmd('view');
		if ($option == 'com_users' && (in_array($view,array('registration','profile')))) {
			$app = JFactory::getApplication();
			$link = JRoute::_('index.php?Itemid='.$this->params->get('profileItemid'));
			$app->redirect($link);
		}
	}

	public function onContentPrepareForm($form, $data) {
		return true;
	}

}
