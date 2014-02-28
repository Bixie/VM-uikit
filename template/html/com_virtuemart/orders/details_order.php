<?php
/**
 *
 * Order detail view
 *
 * @package	VirtueMart
 * @subpackage Orders
 * @author Oscar van Eijk, Valerie Isaksen
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: details_order.php 5341 2012-01-31 07:43:24Z alatak $
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

?>
<div class="uk-grid">
	<div class="uk-width-medium-1-2">
		<dl class="uk-description-list uk-description-list-horizontal">
			<dt><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_PO_NUMBER'); ?></dt>
			<dd><?php echo $this->orderdetails['details']['BT']->order_number; ?></dd>
			<dt><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_PO_DATE'); ?></dt>
			<dd><?php echo JHtml::_('date',$this->orderdetails['details']['BT']->created_on, 'd F Y'); ?></dd>
			<dt><?php echo JText::_('COM_VIRTUEMART_LAST_UPDATED'); ?></dt>
			<dd><?php echo JHtml::_('date',$this->orderdetails['details']['BT']->modified_on, 'd F Y'); ?></dd>
			<dt><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_PO_STATUS'); ?></dt>
			<dd><?php echo $this->orderstatuses[$this->orderdetails['details']['BT']->order_status]; ?></dd>
		</dl>
	</div>
	<div class="uk-width-medium-1-2">
		<dl class="uk-description-list uk-description-list-horizontal">
			<dt><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_SHIPMENT_LBL'); ?></dt>
			<dd><?php echo $this->shipment_name?$this->shipment_name:'-'; ?></dd>
			<dt><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_PAYMENT_LBL'); ?></dt>
			<dd><?php echo $this->payment_name?$this->payment_name:'-'; ?></dd>
			<dt><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_TOTAL'); ?></dt>
			<dd><?php echo $this->currency->priceDisplay($this->orderdetails['details']['BT']->order_total, $this->currency); ?></dd>
		</dl>
	</div>
</div>
<div class="uk-grid uk-form">
	<div class="uk-width-1-1">
		<fieldset>
			<legend><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_CUSTOMER_NOTE') ?></legend>
			<span><?php
			$note = $this->orderdetails['details']['BT']->customer_note;
			echo $note!=''?$note:'-';
			?></span>
		</fieldset>
	</div>
</div>
<div class="uk-grid uk-form">
	<div class="uk-width-medium-1-2">
		<fieldset>
			<legend><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_BILL_TO_LBL') ?></legend>
			<dl class="uk-description-list uk-description-list-horizontal uk-margin-top-remove">
			<?php
			foreach ($this->userfields['fields'] as $field) {
				if (!empty($field['value'])) {
					echo '<dt>' . $field['title'] . '</dt>'
					. '<dd>' . $field['value'] . '</dd>';
				}
			}
			?>
			</dl>
		</fieldset>
	</div>
	<div class="uk-width-medium-1-2">
		<fieldset>
			<legend><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_SHIP_TO_LBL') ?></legend>
			<dl class="uk-description-list uk-description-list-horizontal uk-margin-top-remove">
			<?php
			foreach ($this->shipmentfields['fields'] as $field) {
				if (!empty($field['value'])) {
					echo '<dt>' . $field['title'] . '</dt>'
					. '<dd>' . $field['value'] . '</dd>';
				}
			}
			?>
			</dl>
		</fieldset>
	</div>
</div>
