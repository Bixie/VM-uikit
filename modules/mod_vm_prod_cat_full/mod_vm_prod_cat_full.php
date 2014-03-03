<?php
/**
* VirtueMart Full Category List Module for Joomla! and Virtuemart: 
* displays the list of categories and sub-categories in menu style.
* @author		Andrew Patton
* @version		1.2.0
* @copyright	(C) 2012 Andrew Patton. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* This module is free software.
* See COPYRIGHT.php for copyright notices and details.
*/

/// no direct access
defined( '_JEXEC' ) or die;

// Set up VirtueMart (based on example in mod_virtuemart_category)
if ( ! class_exists( 'VmConfig' ) ) require( JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart' . DS . 'helpers' . DS . 'config.php' );
$config = VmConfig::loadConfig();
if ( ! class_exists( 'VirtueMartModelVendor' ) ) require( JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'vendor.php' );
if ( ! class_exists( 'TableMedias') ) require( JPATH_VM_ADMINISTRATOR . DS . 'tables' . DS . 'medias.php' );
if ( ! class_exists( 'TableCategories') ) require( JPATH_VM_ADMINISTRATOR . DS . 'tables' . DS . 'categories.php' );
if ( ! class_exists( 'VirtueMartModelCategory' ) ) require( JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'category.php' );

// Include the syndicate functions only once
require_once( 'helper.php' );

// Set up variables
$current_cat = JRequest::getInt( 'virtuemart_category_id', -1 );
$settings = array(
	'level_start' => (int) $params->get('level_start'),
	'level_end' => (int) $params->get('level_end'),	
	'menu_class' => $params->get('menuclass_sfx'),
	'current_filter' => $params->get('current_filter', 0)
);
$cat_filter = $params->get('cat_filter', 0);

//fabrikanten
if (!class_exists( 'VmImage' )) require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'image.php');
if(!class_exists('TableManufacturer_medias')) require(JPATH_VM_ADMINISTRATOR.DS.'tables'.DS.'manufacturer_medias.php');
if(!class_exists('TableManufacturers')) require(JPATH_VM_ADMINISTRATOR.DS.'tables'.DS.'manufacturers.php');
if (!class_exists( 'VirtueMartModelManufacturer' )){
   JLoader::import( 'manufacturer', JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart' . DS . 'models' );
}
VmConfig::loadJLang('mod_virtuemart_manufacturer', true);
$virtuemart_manufacturer_id = JRequest::getInt('virtuemart_manufacturer_id', 0);
$model = VmModel::getModel('Manufacturer');
$manufacturers = $model->getManufacturers(true, true,true);

require( JModuleHelper::getLayoutPath( 'mod_vm_prod_cat_full' ) );




// TODO: use helper function to generate an object list to represent the menu
// Use a parameter called depth to specify how many levels the menu goes to for output
// General:
//$menu = new stdClass();
//$menu->depth = $totalLevel;
//$menu->menuclass = $params->get('menuclass_sfx');
// Specific to each menu item:
//$menu->level = $level;
//$menu->name = htmlspecialchars($row->category_name, ENT_COMPAT, 'UTF-8');
//$menu->link;
//$menu->itemid;
//$menu->class;
//$menu->current; // either '' or 'id="current"'
//$menu->child; // either false or the child object

?>