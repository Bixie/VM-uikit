<?php
/**
 *
 * Show the product details page
 *
 * @package	VirtueMart
 * @subpackage
 * @author Max Milbers, Valerie Isaksen

 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: default_relatedproducts.php 6431 2012-09-12 12:31:31Z alatak $
 */

// Check to ensure this file is included in Joomla!
defined ( '_JEXEC' ) or die ( 'Restricted access' );
?>
	<h4><?php echo JText::_('COM_VIRTUEMART_RELATED_PRODUCTS'); ?></h4>
	<div class="uk-grid related-products">
    <?php
	$count = count($this->product->customfieldsRelatedProducts);
    foreach ($this->product->customfieldsRelatedProducts as $field) {
	    if(!empty($field->display)) {
			?>
		<div class="uk-width-medium-1-<?php echo $count; ?> product-field-type-<?php echo $field->field_type ?> uk-text-center">
			<div class="uk-panel uk-panel-box uk-panel-box-gray">
				<?php echo $field->display ?>
			</div>
		</div>
	<?php }
	} ?>
	</div>
