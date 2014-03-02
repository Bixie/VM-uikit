<?php
/**
*
* Modify user form view
*
* @package	VirtueMart
* @subpackage User
* @author Oscar van Eijk
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: edit.php 6472 2012-09-19 08:46:21Z alatak $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

//AdminMenuHelper::startAdminArea($this);
// vmdebug('User edit',$this);
// Implement Joomla's form validation
JHTML::_('behavior.formvalidation');
JHTML::stylesheet('vmpanels.css', JURI::root().'components/com_virtuemart/assets/css/'); // VM_THEMEURL


// $usermodel = VmModel::getModel('user');

// Trigger the onContentChangeState event.
// $dispatcher = JDispatcher::getInstance();
// $result = $dispatcher->trigger('onNewVMuser', array($usermodel,$this->userDetails->virtuemart_user_id));
// if (in_array(false, $result, true)) {
	// print($dispatcher->getError());
// }

//bixie
$bekendePostcode = JFactory::getApplication()->getUserState('plugin.system.bixsystem.postcodechecked','');

JFactory::getDocument()->addScript('plugins/system/bixsystem/assets/mod_bix_postcode.js');



?>
<script language="javascript">
	window.addEvent('domready',function() {
		bixPostcode = new BixPostcode('zip_field',false,false,{
			userForm: true,
			bekendePostcode: '<?php echo $bekendePostcode;?>',
			formEls: {
				postcode: 'zip_field',
				huisnummer: 'address_2_field',
				straat: 'address_1_field',
				plaats: 'city_field'
			}
		});
		document.id('virtuemart_country_id').set('value',150); //==NL... sst
	});



function myValidator(f, t)
{
	f.task.value=t;
	if (document.formvalidator.isValid(f)) {
		f.submit();
		return true;
	} else {
	console.log(document.formvalidator);
		$$('input[aria-invalid],select[aria-invalid]').each(function (el) {
			if (el.get('aria-invalid') == 'true')
				el.addClass('uk-form-danger'); 
			else 
				el.removedClass('uk-form-danger')
		});
		var msg = '<?php echo addslashes( JText::_('COM_VIRTUEMART_USER_FORM_MISSING_REQUIRED_JS') ); ?>';
		jQuery.UIkit.notify(msg,'danger');
	}
	return false;
}
</script>
<div class="manufacturer-details-view">
	<div class="uk-grid">
		<div class="uk-width-1-1">
			<h1><?php echo $this->page_title ?></h1>
		</div>
	</div>


	<div class="uk-grid">
		<div class="uk-width-medium-2-3">
		<?php
			jimport('joomla.application.module.helper');
			$renderer = JFactory::getDocument()->loadRenderer('module');
			$contents = '';
			$position = $this->userDetails->virtuemart_user_id==0?'registratie':'klantprofiel';
			foreach (JModuleHelper::getModules($position) as $mod)  {
				$contents .= $renderer->render($mod, array('style'=>'blank'));
			}
			if($this->userDetails->virtuemart_user_id==0) : ?>
			
			<h2><?php echo JText::_('COM_VIRTUEMART_YOUR_ACCOUNT_REG');?></h2>
			
			<?php echo $contents; ?>
			
		<?php else: ?>
			<?php echo $contents; ?>
			
		<?php endif; ?>
		</div>
		<div class="uk-width-medium-1-3">
			<div class="uk-panel uk-panel-box">
			<?php echo shopFunctionsF::getLoginForm(false); ?>
			</div>
		</div>
	</div>
	<div class="uk-grid">
		<div class="uk-width-1-1">
			<form method="post" id="adminForm" name="userForm" action="<?php echo JRoute::_('index.php?view=user',$this->useXHTML,$this->useSSL) ?>" class="uk-form uk-form-horizontal form-validate">
			<?php if($this->userDetails->user_is_vendor) : ?>
				<div class="uk-width-1-1 uk-text-center uk-margin-bottom">
					<button class="uk-button" type="submit" onclick="javascript:return myValidator(userForm, 'saveUser');" >
						<i class="uk-icon-check uk-margin-small-right"></i><?php echo $this->button_lbl ?></button>
					<button class="uk-button" type="reset" onclick="window.location.href='<?php echo JRoute::_('index.php?option=com_virtuemart&view=user', FALSE); ?>'" >
						<i class="uk-icon-ban uk-margin-small-right"></i><?php echo JText::_('COM_VIRTUEMART_CANCEL'); ?></button>
				</div>
			<?php endif; ?>
				<?php // Loading Templates in Tabs
				if($this->userDetails->virtuemart_user_id!=0) {
					$tabarray = array();
					if($this->userDetails->user_is_vendor){
						if(!empty($this->add_product_link)) {
							echo $this->add_product_link;
						}
						$tabarray['vendor'] = 'COM_VIRTUEMART_VENDOR';
					}
					$tabarray['shopper'] = 'COM_VIRTUEMART_SHOPPER_FORM_LBL';
					$tabarray['address_addshipto'] =  'COM_VIRTUEMART_USER_FORM_SHIPTOS_LBL';
					//$tabarray['user'] = 'COM_VIRTUEMART_USER_FORM_TAB_GENERALINFO';
					if (!empty($this->shipto)) { //????
						$tabarray['shipto'] = 'COM_VIRTUEMART_USER_FORM_ADD_SHIPTO_LBL';
					}
					if (($_ordcnt = count($this->orderlist)) > 0) {
						$tabarray['orderlist'] = 'COM_VIRTUEMART_YOUR_ORDERS';
					}
					shopFunctionsF::buildTabs ( $this, $tabarray);

				 } else {
					echo $this->loadTemplate ( 'shopper' );
				 }
				?>
			
			
			
				<input type="hidden" name="option" value="com_virtuemart" />
				<input type="hidden" name="controller" value="user" />
				<?php echo JHTML::_( 'form.token' ); ?>
			</form>
		</div>
	</div>
</div>

<?php
/*
 * TODO this Stuff should be converted in a payment module. But the idea to show already saved payment information to the user is a good one
 * So maybe we should place here a method (joomla plugin hook) which loads all published plugins, which already used by the user and display
 * them.
 */
//	echo $this->pane->startPanel( JText::_('COM_VIRTUEMART_SHOPPER_PAYMENT_FORM_LBL'), 'edit_payment' );
//	echo $this->loadTemplate('payment');
//	echo $this->pane->endPanel();

//	echo $this->pane->startPanel( JText::_('COM_VIRTUEMART_SHOPPER_SHIPMENT_FORM_LBL'), 'edit_shipto' );
//	echo $this->loadTemplate('shipto');
//	echo $this->pane->endPanel();
//	if ($this->shipto !== 0) {
//		// Note:
//		// Of the order of the tabs change here, change the startOffset value for
//		// JPane::getInstance() as well in view.html.php!
//		echo $this->pane->startPanel( JText::_('COM_VIRTUEMART_USER_FORM_ADD_SHIPTO_LBL'), 'edit_shipto' );
//		echo $this->loadTemplate('shipto');
//		echo $this->pane->endPanel();
//	}

// 	if (($_ordcnt = count($this->orderlist)) > 0) {
// 		echo $this->pane->startPanel( JText::_('COM_VIRTUEMART_ORDER_LIST_LBL') . ' (' . $_ordcnt . ')', 'edit_orderlist' );
// 		echo $this->loadTemplate('orderlist');
// 		echo $this->pane->endPanel();
// 	}

// 	if (!empty($this->userDetails->user_is_vendor)) {
// 		echo $this->pane->startPanel( JText::_('COM_VIRTUEMART_VENDOR_MOD'), 'edit_vendor' );
// 		echo $this->loadTemplate('vendor');
// 		echo $this->pane->endPanel();
// 	}

// 	echo $this->pane->endPane();
?>

