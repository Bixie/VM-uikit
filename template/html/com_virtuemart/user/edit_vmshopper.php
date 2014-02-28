<?php
/**
 *
 * Modify user form view, User info
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
 * @version $Id: edit_vmshopper.php 6203 2012-07-03 09:48:00Z enytheme $
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

?>

<fieldset>
	<legend>
		<?php echo JText::_('COM_VIRTUEMART_SHOPPER_FORM_LBL') ?>
	</legend>
<?php	if(Vmconfig::get('multix','none')!=='none'){ ?>

		<div class="uk-form-row">
			<label class="uk-form-label" for="virtuemart_vendor_id">
				<?php echo JText::_('COM_VIRTUEMART_PRODUCT_FORM_VENDOR') ?>:
			</label>
			<div class="uk-form-controls"><?php echo $this->lists['vendors']; ?></div>
		</div>
<?php } ?>

	<div class="uk-form-row">
		<label class="uk-form-label" for="perms">
			<?php echo JText::_('COM_VIRTUEMART_USER_FORM_PERMS') ?>:
		</label>
		<div class="uk-form-controls"><?php echo $this->lists['perms']; ?></div>
	</div>

	<div class="uk-form-row">
		<label class="uk-form-label" for="customer_number">
			<?php echo JText::_('COM_VIRTUEMART_USER_FORM_CUSTOMER_NUMBER') ?>:
		</label>
		<div class="uk-form-controls">
		 <?php if(Permissions::getInstance()->check('admin')) { ?>
			<input type="text" class="inputbox" name="customer_number" id="customer_number" size="40" value="<?php echo  $this->lists['custnumber'];
				?>" />
		<?php } else {
			echo $this->lists['custnumber'];
		} ?>
		</div>
	</div>

	 <?php if($this->lists['shoppergroups']) { ?>
	<div class="uk-form-row">
		<label class="uk-form-label" for="shoppergroups">
			<?php echo JText::_('COM_VIRTUEMART_SHOPPER_FORM_GROUP') ?>:
		</label>
		<div class="uk-form-controls"><?php echo $this->lists['shoppergroups']; ?></div>
	</div>
	<?php } ?>

</fieldset>
