<?php
/**
*
* User details, Orderlist
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
* @version $Id: edit_orderlist.php 5351 2012-02-01 13:40:13Z alatak $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access'); ?>

<div id="editcell">
	<ul class="uk-list uk-list-striped">
	<?php
		foreach ($this->orderlist as $i => $row) :
			$editlink = JRoute::_('index.php?option=com_virtuemart&view=orders&layout=details&order_number=' . $row->order_number);
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
