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

	public function plgBixprintshopAdresPostcodefill(&$subject, $config = array()) {
		parent::__construct($subject, $config);
	}
	/*Statics
	* must be called from within VM!
	*/
	public static function noProducts () {
		jimport('joomla.application.module.helper');
		//init vars
		$html = '';
		$position = false;
		if (!self::validUser()) { //geen valid user
			$position = 'noproducts';
		}
		//modulehelper
		if ($position) {
			$renderer = JFactory::getDocument()->loadRenderer('module');
			foreach (JModuleHelper::getModules($position) as $mod)  {
				$html .= $renderer->render($mod, array('style'=>'blank'));
			}
		}
// echo '<pre style="margin-top:100px;">';
// print_r($vmUser);
// echo '</pre>';
		return $html;
	}
	
	public static function notValidUser () {
		jimport('joomla.application.module.helper');
		//init vars
		$document = JFactory::getDocument();
		$html = '';
		$position = 'notvaliduser';
		//modulehelper
		if ($position) {
			$renderer = JFactory::getDocument()->loadRenderer('module');
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

	public static function formatPostcode($postcodeRaw) {
		$regEx = '/^(?P<num>[0-9]{4}).?(?P<alph>[a-z|A-Z]{2})?$/';
		$postcodeArr = array();
		if (preg_match($regEx,trim($postcodeRaw),$match)) {
			$postcodeArr['format'] = $match['num'].' '.strtoupper($match['alph']);
			$postcodeArr['num'] = $match['num'];
			$postcodeArr['alph'] = strtoupper($match['alph']);
			$postcodeArr['raw'] = $postcodeRaw;
		} else {
//print_r_pre($match);
			$postcodeArr['raw'] = $postcodeRaw;
		}
		$postcodeArr['valid'] = !empty($postcodeArr['num']) && !empty($postcodeArr['alph']);
		return $postcodeArr;
	}

	/*Events*/
	public function onAfterInitialise() {
		$app = JFactory::getApplication();
		$template = $app->getTemplate();
		if (!class_exists('JDocumentRendererMessage') && file_exists(JPATH_THEMES . '/' . $template . '/html/message.php')) {
			require_once JPATH_THEMES . '/' . $template . '/html/message.php';
		}
		return true;
	}
	
	public function onAfterDispatch () {
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

	// JFactory::getApplication()->enqueueMessage('Postcode niet binnen verzorgingsgebied','success');
	// JFactory::getApplication()->enqueueMessage('Postcode niet binnen verzorgingsgebied','warning');
	// JFactory::getApplication()->enqueueMessage('Postcode niet binnen verzorgingsgebied','error');
	// JFactory::getApplication()->enqueueMessage('Postcode niet binnen verzorgingsgebied','error');
	// JFactory::getApplication()->enqueueMessage('Postcode niet binnen verzorgingsgebied','info');

	}

	function onNewVMuser($userModel, $newId) {
		if ($newId) {
			if (!$this->params->get('allowedGroup',0)) {
				$this->_subject->setError('Stel usergroup in!');
				return false;
			}
			$vmUser = $userModel->getUser($newId);
			$factuurAdres = false;
			foreach ($vmUser->userInfo as $address) {
				if ($address->address_type == 'BT') {
					$factuurAdres = $address;
				}
			}
			if ($factuurAdres) {
				$postcode = $factuurAdres->zip;
				if ($this->validPostcode($postcode)) {
					if (!in_array($this->params->get('allowedGroup'),$vmUser->shopper_groups)) {
						$shoppergroupData = array('virtuemart_user_id'=>$newId,'virtuemart_shoppergroup_id'=>$this->params->get('allowedGroup'));
						$paths = JTable::addIncludePath(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'tables');
						$user_shoppergroups_table = JTable::getInstance('vmuser_shoppergroups', 'Table', array());
						$shoppergroupData = $user_shoppergroups_table->bindChecknStore($shoppergroupData);
						$errors = $user_shoppergroups_table->getErrors();
						foreach($errors as $error){
							$this->setError($error);
							$this->_subject->setError('Set shoppergroup '.$error);
							return false;
						}
					} 
				} else {
					$this->_subject->setError('Adres valt niet binnen verzorgingsgebied!');
					return false;
				}
			} else {
				$this->_subject->setError('Geen factuuradres!');
				return false;
			}
				// echo '<pre>'.$newId;
				// print_r($user_shoppergroups_table);
				// echo '</pre>';
			// echo $this['sdg'];
		}
		return true;
	}
	// index.php?option=com_virtuemart&controller=plugin&task=display&type=vmcustom&name=bixsystem
	public function plgVmOnSelfCallFE($type, $name, &$render) {
		JPlugin::loadLanguage('plg_system_bixsystem');
		if ($type != 'vmcustom' || $name != 'bixsystem') jExit();
		$data = JRequest::get('GET');
		$return = array('valid'=>false,'message'=>'','info'=>array());
		//format postcode
		$postcodeArr = self::formatPostcode($data['postcode']);
		if (!empty($data['validateFormat']) && !empty($postcodeArr['valid']) || empty($postcodeArr['num'])) { //invalid postcode 
			$return['message'] = 'Postcode niet in geldig formaat';
			echo json_encode($return);
			jExit();
		}
		if (!empty($data['huisnummer'])) {
			//validatie postcode.nl
			require_once(dirname(__FILE__).DS.'helpers/postcodenl_api.php');
			$helper = new PostcodeNl_Api_Helper_Data($this->params);
			$return['info'] = $helper->lookupAddress($postcodeArr['num'].$postcodeArr['alph'], $data['huisnummer'], @$data['huisnummer_toevoeging']);
			if (!isset($return['info']['street'])) {
				$return['message'] = $return['info']['message'];
				echo json_encode($return);
				jExit();
			}
		}
		if ($this->validPostcode($data['postcode'])) {
			$return['valid'] = true;
			$return['message'] = 'Postcode binnen verzorgingsgebied';
		} else {
			$return['message'] = 'Postcode niet binnen verzorgingsgebied';
		}
// echo '<pre>';
// print_r($return);
// echo '</pre>';
		//data in sessie
		JFactory::getApplication()->setUserState('plugin.system.bixsystem.postcodechecked',$data['postcode']);
		echo json_encode($return);
		jExit();
	}

	public function plgVmOnUserStore(&$data) {
		if ($this->validPostcode($data['zip'])) {
			if (!in_array($this->params->get('allowedGroup',0),$data['virtuemart_shoppergroup_id'])) {
				$data['virtuemart_shoppergroup_id'][] = $this->params->get('allowedGroup',0);
			}
		} else {
			if (in_array($this->params->get('allowedGroup',0),$data['virtuemart_shoppergroup_id'])) {
				JFactory::getApplication()->enqueueMessage('Postcode niet binnen verzorgingsgebied','warning');
				$data['virtuemart_shoppergroup_id'] = array(); //leeg is ook default bij vm
			}
		}
// echo $this['sdg'];
		return true;
	}
/*
{
	"reeksen" : [{
			"van" : 7400,
			"tot" : 7699
		}, {
			"van" : 8000,
			"tot" : 8049
		}
	],
	"losgebied" : [
		7712,7713
	],
	"losPc" : [
		"1012AB","1013AB"
	],
	"exceptgebied" : [
		7511,7575
	],
	"exceptPc" : [
		"7400AB","7680AB"
	]
}//*/
	private function validPostcode($postcode) {
		$postcodeGebieden = json_decode(file_get_contents(dirname(__FILE__).'/postcodegebieden.json'));
		$postcodeArr = self::formatPostcode($postcode);
		if (empty($postcodeArr['num'])) return false;
	// echo '<pre>';
	// print_r($postcodeArr);
	// print_r($postcodeGebieden->losPc);
	// echo '</pre>';
		// uitzonderingen altijd goed
		if (in_array($postcodeArr['num'],$postcodeGebieden->losgebied) || ($postcodeArr['valid'] 
				&& in_array($postcodeArr['num'].$postcodeArr['alph'],$postcodeGebieden->losPc))) { 
			return true;
		}
		//in reeksen?
		$valid = false;
		foreach ($postcodeGebieden->reeksen as $reeks) {
			if ($postcodeArr['num'] >= $reeks->van && $postcodeArr['num'] <= $reeks->tot) {
				$valid = true;
			}
		}
		//of toch uitzondering?
		if (in_array($postcodeArr['num'],$postcodeGebieden->exceptgebied) || ($postcodeArr['valid'] 
				&& in_array($postcodeArr['num'].$postcodeArr['alph'],$postcodeGebieden->exceptPc))) { 
			$valid = false;
		}
		return $valid;
	}
	
}
