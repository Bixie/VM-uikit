<?php
	defined ('_JEXEC') or  die('Direct Access to ' . basename (__FILE__) . ' is not allowed.');
	/*
 * Module Helper
 *
 * @package VirtueMart
 * @copyright (C) 2010 - Patrick Kohl
 * @ Email: cyber__fr|at|hotmail.com
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * VirtueMart is Free Software.
 * VirtueMart comes with absolute no warranty.
 *
 * www.virtuemart.net
 */

if (!class_exists ('VmConfig')) {
	require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart' . DS . 'helpers' . DS . 'config.php');
}
VmConfig::loadConfig ();

// Load the language file of com_virtuemart.
JFactory::getLanguage ()->load ('com_virtuemart');
if (!class_exists ('calculationHelper')) {
	require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart' . DS . 'helpers' . DS . 'calculationh.php');
}
	if (!class_exists ('CurrencyDisplay')) {
		require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart' . DS . 'helpers' . DS . 'currencydisplay.php');
	}
	if (!class_exists ('VirtueMartModelVendor')) {
		require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart' . DS . 'models' . DS . 'vendor.php');
	}
	if (!class_exists ('VmImage')) {
		require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart' . DS . 'helpers' . DS . 'image.php');
	}
	if (!class_exists ('shopFunctionsF')) {
		require(JPATH_SITE . DS . 'components' . DS . 'com_virtuemart' . DS . 'helpers' . DS . 'shopfunctionsf.php');
	}
	if (!class_exists ('calculationHelper')) {
		require(JPATH_COMPONENT_SITE . DS . 'helpers' . DS . 'cart.php');
	}
	if (!class_exists ('VirtueMartModelProduct')) {
		JLoader::import ('product', JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart' . DS . 'models');
	}


class mod_virtuemart_product {

	static function addtocart ($product) {

		if (!VmConfig::get ('use_as_catalog', 0)) {
			$stockhandle = VmConfig::get ('stockhandle', 'none');
			if (($stockhandle == 'disableit' or $stockhandle == 'disableadd') and ($product->product_in_stock - $product->product_ordered) < 1) {
				$button_lbl = JText::_ ('COM_VIRTUEMART_CART_NOTIFY');
				$button_cls = 'notify-button';
				$button_name = 'notifycustomer';
				?>
				<div style="display:inline-block;">
			<a href="<?php echo JRoute::_ ('index.php?option=com_virtuemart&view=productdetails&layout=notify&virtuemart_product_id=' . $product->virtuemart_product_id); ?>" class="notify"><?php echo JText::_ ('COM_VIRTUEMART_CART_NOTIFY') ?></a>
				</div>
			<?php
			} else {
				?>
			<div class="addtocart-area">

				<form method="post" class="product" action="index.php">
					<?php
					// Product custom_fields
					if (!empty($product->customfieldsCart)) {
						?>
						<div class="product-fields">
							<?php foreach ($product->customfieldsCart as $field) { ?>

							<div style="display:inline-block;" class="product-field product-field-type-<?php echo $field->field_type ?>">
								<?php if($field->show_title == 1) { ?>
									<span class="product-fields-title"><b><?php echo $field->custom_title ?></b></span>
									<?php echo JHTML::tooltip ($field->custom_tip, $field->custom_title, 'tooltip.png'); ?>
								<?php } ?>
								<span class="product-field-display"><?php echo $field->display ?></span>
								<span class="product-field-desc"><?php echo $field->custom_field_desc ?></span>
							</div>

							<?php } ?>
						</div>
						<?php } ?>

					<div class="addtocart-bar">
						<div class="uk-grid">

						<?php
						// Display the quantity box
						?>
						<!-- <label for="quantity<?php echo $product->virtuemart_product_id;?>" class="quantity_box"><?php echo JText::_ ('COM_VIRTUEMART_CART_QUANTITY'); ?>: </label> -->
						
							<div class="uk-width-medium-1-3">
								<div class="uk-clearfix">
									<div class="uk-float-left quantity-box">
										<input type="text" class="quantity-input js-recalculate uk-form-width-mini uk-form-large uk-text-right" name="quantity[]" onblur="check(this);"
											   value="<?php if (isset($product->step_order_level) && (int)$product->step_order_level > 0) {
													echo $product->step_order_level;
												} else if(!empty($product->min_order_level)){
													echo $product->min_order_level;
												}else {
													echo '1';
												} ?>"/>
									</div>
									<div class="uk-float-left uk-margin-small-left quantity-controls js-recalculate">
										<button type="button" class="quantity-plus uk-button uk-button-mini uk-button uk-margin-remove"><i class="uk-icon-plus"></i></button><br/>
										<button type="button" class="quantity-minus uk-button uk-button-mini uk-button"><i class="uk-icon-minus"></i></button>
									</div>
								</div>
							</div>
						
							<div class="uk-width-medium-2-3">

								<?php
									// Add the button
									$button_lbl = JText::_ ('COM_VIRTUEMART_CART_ADD_TO');
									$button_cls = ''; //$button_cls = 'addtocart_button';
								?>
								<?php // Display the add to cart button ?>
								<?php echo shopFunctionsF::getAddToCartButton($product->orderable); ?>
							</div>
						</div>
					</div>

					<input type="hidden" class="pname" value="<?php echo $product->product_name ?>"/>
					<input type="hidden" name="option" value="com_virtuemart"/>
					<input type="hidden" name="view" value="cart"/>
					<noscript><input type="hidden" name="task" value="add"/></noscript>
					<input type="hidden" name="virtuemart_product_id[]" value="<?php echo $product->virtuemart_product_id ?>"/>
					<input type="hidden" name="virtuemart_category_id[]" value="<?php echo $product->virtuemart_category_id ?>"/>
				</form>
			</div>
			<?php
			}
		}
	}
}
