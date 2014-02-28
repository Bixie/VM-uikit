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
* @version $Id: details.php 6246 2012-07-09 19:00:20Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
JHTML::stylesheet('vmpanels.css', JURI::root().'components/com_virtuemart/assets/css/');
if($this->print) :
	?>

		<body onload="javascript:print();">
		<div><img src="<?php  echo JURI::root() . $this-> vendor->images[0]->file_url ?>"></div>
		<h2><?php  echo $this->vendor->vendor_store_name; ?></h2>
		<?php  echo $this->vendor->vendor_name .' - '.$this->vendor->vendor_phone ?>
		<h1><?php echo JText::_('COM_VIRTUEMART_ACC_ORDER_INFO'); ?></h1>
		<div class='spaceStyle'>
		<?php
		echo $this->loadTemplate('order');
		?>
		</div>

		<div class='spaceStyle'>
		<?php
		echo $this->loadTemplate('items');
		?>
		</div>
		<?php	echo $this->vendor->vendor_letter_footer_html; ?>
		</body>
		<?php
else:
?>
<div class="orderdetails">
	<div class="uk-grid">
		<div class="uk-width-1-1">
			<div class="uk-float-right icons">
			<?php if($this->order_list_link) : ?>
				<a href="<?php echo $this->order_list_link ?>" class="uk-button uk-button-small" rel="nofollow">
					<?php echo JText::_('COM_VIRTUEMART_ORDERS_VIEW_DEFAULT_TITLE'); ?>
				</a>
			<?php endif; ?>
				<?php
				/* Print view URL */
				$details_link = "<a href=\"javascript:void window.open('$this->details_url', 'win2', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no');\" class=\"uk-button uk-button-small\" title=\"".JText::_('COM_VIRTUEMART_PRINT')."\" >";
				$details_link  .=  '<i class="uk-icon-print"></i></a>';
				echo $details_link; ?>
			</div>
			<h1 class="uk-margin-top-remove"><?php echo JText::_('COM_VIRTUEMART_ACC_ORDER_INFO'); ?></h1>
		</div>
	</div>
	
	<?php echo $this->loadTemplate('order'); ?>

	<?php
	$tabarray = array();
	$tabarray['items'] = 'COM_VIRTUEMART_ORDER_ITEM';
	$tabarray['history'] = 'COM_VIRTUEMART_ORDER_HISTORY';
 ?>
 	<div class="uk-grid">
		<div class="uk-width-1-1">
		<?php shopFunctionsF::buildTabs ( $this, $tabarray); ?>
		</div>
	</div>

	 
</div>	 
<?php endif; ?>






