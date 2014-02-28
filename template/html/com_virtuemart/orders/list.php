<?php
/**
*
* Orderlist
* NOTE: This is a copy of the edit_orderlist template from the user-view (which in turn is a slighly
*       modified copy from the backend)
*
* @package	VirtueMart
* @subpackage Orders
* @author Oscar van Eijk
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: list.php 5434 2012-02-14 07:59:10Z electrocity $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
?>
<h1><?php echo JText::_('COM_VIRTUEMART_ORDERS_VIEW_DEFAULT_TITLE'); ?></h1>
<?php
if (count($this->orderlist) == 0) {
	//echo JText::_('COM_VIRTUEMART_ACC_NO_ORDER');
	 echo shopFunctionsF::getLoginForm(false,true);
} else {
 ?>
<div id="editcell">
	<div class="uk-grid">
		<div class="uk-width-1-1">
			<h1><?php echo $this->page_title ?></h1>
		</div>
	</div>
	<ul class="uk-list uk-list-striped">
	<?php
		foreach ($this->orderlist as $row) :
			$editlink = JRoute::_('index.php?option=com_virtuemart&view=orders&layout=details&order_number=' . $row->order_number, FALSE);
			?>
			<li>
				<div class="uk-grid"><div class="uk-width-large-4-10">
					<dl class="uk-description-list uk-description-list-horizontal">
						<dt><?php echo JText::_('COM_VIRTUEMART_ORDER_LIST_ORDER_NUMBER'); ?></dt>
						<dd><a href="<?php echo $editlink; ?>" rel="nofollow"><?php echo $row->order_number; ?></a></dd>
						<dt><?php echo JText::_('COM_VIRTUEMART_ORDER_LIST_STATUS'); ?></dt>
						<dd><?php echo ShopFunctions::getOrderStatusName($row->order_status); ?></dd>
						<dt><?php echo JText::_('COM_VIRTUEMART_ORDER_LIST_TOTAL'); ?></dt>
						<dd><?php echo $this->currency->priceDisplay($row->order_total); ?></dd>
					</dl>
				</div>
				<div class="uk-width-large-4-10 uk-width-4-5">
					<dl class="uk-description-list uk-description-list-horizontal">
						<dt><?php echo JText::_('COM_VIRTUEMART_ORDER_LIST_CDATE'); ?></dt>
						<dd><?php echo JHTML::_('date', $row->created_on, 'd F Y'); ?></dd>
						<dt><?php echo JText::_('COM_VIRTUEMART_ORDER_LIST_MDATE'); ?></dt>
						<dd><?php echo JHTML::_('date', $row->modified_on, 'd F Y'); ?></dd>
					</dl>
				</div>
				<div class="uk-width-1-5">
					<a class="uk-button uk-button-small uk-float-right uk-margin-small-right" rel="nofollow" title="<?php echo JText::_('COM_VIRTUEMART_ACC_ORDER_INFO'); ?>" href="<?php echo $editlink; ?>"><i class="uk-icon-search"></i></a>
				</div>
			</li>
			
	<?php endforeach; ?>
	</ul>
</div>
<?php } ?>
