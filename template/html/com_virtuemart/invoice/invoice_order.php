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

		




<?php if ($this->invoiceNumber) { ?>
<h1><?php echo JText::_('COM_VIRTUEMART_INVOICE').' '.$this->invoiceNumber; ?><br/></h1>
<?php } ?>

<table cellpadding="2" width="100%">

    <tr>
    <td valign="top" >
		<h2><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_SHIP_TO_LBL') ?></h2><br/>
	    <table cellpadding="2" border="0"><?php
			$sorted = array();
		  foreach ($this->shipmentfields['fields'] as $field) {
			
			if (!empty($field['value'])) {
				switch ($field['title']) {
					case 'Voornaam':
						$sorted['Voornaam'] = $field['value'];
					break;
					case 'Naam':
						$sorted['Achternaam'] = $field['value'];
					break;
					case 'Adres':
						$sorted['Adres'] = $field['value'];
					break;
					case 'Postcode':
						$sorted['Postcode'] = $field['value'];
					break;
					case 'Plaats':
						$sorted['Plaats'] = $field['value'];
					break;
					case 'Land':
						$sorted['Land'] = $field['value'];
					break;
				}
			}
	    }
			echo '<tr><td class="key">' . 
				$sorted['Voornaam'].' '.$sorted['Achternaam'].'<br/>'.
				$sorted['Adres'].'<br/>'.
				$sorted['Postcode'].' '.$sorted['Plaats'].'<br/>'.	
				$sorted['Land'].'<br/>'.
			 '</td></tr>';
	    ?></table>
	</td>
	<td valign="top">
		<h2><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_BILL_TO_LBL') ?></h2> <br/>
	    <table cellpadding="2" border="0"><?php
	    foreach ($this->userfields['fields'] as $field) {
		if (!empty($field['value'])) {
		    echo '<tr><td class="key">' . $field['title'] . '</td>'
		    . '<td>' . $field['value'] . '</td></tr>';
		}
	    }
	    ?></table>
	</td>
	
    </tr>
</table>

	

<table width="50%" cellspacing="0" cellpadding="2" border="0">
	<?php if ($this->invoiceNumber) { ?>
    <tr>
		<td class=""><?php echo JText::_('COM_VIRTUEMART_INVOICE_DATE') ?></td>
		<td align="left"><?php echo vmJsApi::date($this->invoiceDate, 'LC4', true); ?></td>
    </tr>
	    <?php } ?>
    <tr>
		<td ><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_PO_NUMBER') ?></td>
		<td align="left"><?php echo $this->orderDetails['details']['BT']->order_number; ?></td>
    </tr>

    <tr>
		<td class=""><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_PO_DATE') ?></td>
		<td align="left"><?php echo vmJsApi::date($this->orderDetails['details']['BT']->created_on, 'LC4', true); ?></td>
    </tr>
    <tr>
		<td class=""><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_PO_STATUS') ?></td>
		<td align="left"><?php echo $this->orderstatuses[$this->orderDetails['details']['BT']->order_status]; ?></td>
    </tr>
    <tr>
		<td class=""><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_SHIPMENT_LBL') ?></td>
		<td align="left"><?php
		    echo $this->orderDetails['shipmentName'];
		    ?></td>
    </tr>
    <tr>
		<td class=""><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_PAYMENT_LBL') ?></td>
		<td align="left"><?php echo $this->orderDetails['paymentName']; ?>
		</td>
    </tr>
<?php if ($this->orderDetails['details']['BT']->customer_note) { ?>
	 <tr>
	    <td><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_CUSTOMER_NOTE') ?></td>
	    <td valign="top" align="left" width="50%"><?php echo $this->orderDetails['details']['BT']->customer_note; ?></td>
</tr>
<?php } ?>

     <tr>
		<td class="orders-key"><br/><br/><h1><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_TOTAL') ?></h1></td>
		<td class="orders-key" align="left"><br/><br/><h1><?php echo $this->currency->priceDisplay($this->orderDetails['details']['BT']->order_total,$this->currency); ?></h1></td>
    </tr>
    <tr>
		<td colspan="2"></td>
    </tr>
</table>

