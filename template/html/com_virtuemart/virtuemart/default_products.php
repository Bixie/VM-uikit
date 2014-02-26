<?php defined('_JEXEC') or die('Restricted access');


// Separator
$verticalseparator = " vertical-separator";
$types = array_keys($this->products);

//tabs
?>
<ul class="uk-tab" data-uk-tab="{connect:'#vm-producttabs'}">
<?php
foreach ($this->products as $type => $productList ) :
	$productTitle = JText::_('COM_VIRTUEMART_'.$type.'_PRODUCT')
?>
	<li>
		<a href="#"><?php echo $productTitle ?></a>
	</li>
<?php endforeach; ?>
</ul>
<ul id="vm-producttabs" class="uk-switcher uk-margin">
<?php
foreach ($this->products as $type => $productList ) :
// Calculating Products Per Row
$products_per_row = VmConfig::get ( 'homepage_products_per_row', 3 ) ;
$cellwidth = ' width-1'.$products_per_row;

?>

	<li class="<?php echo $type ?>-view">


		<ul class="uk-grid">
	<?php // Start the Output

	foreach ( $productList as $product ) :

			// Show Products ?>
			<li class="product <?php echo $cellwidth?>">
				<div class="spacer">
						<h3>
						<?php // Product Name
						echo JHTML::link ( JRoute::_ ( 'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' . $product->virtuemart_category_id, FALSE ), $product->product_name, array ('title' => $product->product_name ) ); ?>
						</h3>

						<div>
						<?php // Product Image
						if ($product->images) {
							echo JHTML::_ ( 'link', JRoute::_ ( 'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' . $product->virtuemart_category_id, FALSE ), $product->images[0]->displayMediaThumb( 'class="featuredProductImage" border="0"',true,'class="modal"' ) );
						}
						?>
						</div>


						<div class="product-price">
						<?php
						if (VmConfig::get ( 'show_prices' ) == '1') {
						//				if( $featProduct->product_unit && VmConfig::get('vm_price_show_packaging_pricelabel')) {
						//						echo "<strong>". JText::_('COM_VIRTUEMART_CART_PRICE_PER_UNIT').' ('.$featProduct->product_unit."):</strong>";
						//					} else echo "<strong>". JText::_('COM_VIRTUEMART_CART_PRICE'). ": </strong>";

						if ($this->showBasePrice) {
							echo $this->currency->createPriceDiv( 'basePrice', 'COM_VIRTUEMART_PRODUCT_BASEPRICE', $product->prices );
							echo $this->currency->createPriceDiv( 'basePriceVariant', 'COM_VIRTUEMART_PRODUCT_BASEPRICE_VARIANT', $product->prices );
						}
						echo $this->currency->createPriceDiv( 'variantModification', 'COM_VIRTUEMART_PRODUCT_VARIANT_MOD', $product->prices );
						if (round($product->prices['basePriceWithTax'],$this->currency->_priceConfig['salesPrice'][1]) != $product->prices['salesPrice']) {
							echo '<div class="price-crossed">' . $this->currency->createPriceDiv( 'basePriceWithTax', 'COM_VIRTUEMART_PRODUCT_BASEPRICE_WITHTAX', $product->prices ) . "</div>";
						}
						if (round($product->prices['salesPriceWithDiscount'],$this->currency->_priceConfig['salesPrice'][1]) != $product->prices['salesPrice']) {
							echo $this->currency->createPriceDiv( 'salesPriceWithDiscount', 'COM_VIRTUEMART_PRODUCT_SALESPRICE_WITH_DISCOUNT', $product->prices );
						}
						echo $this->currency->createPriceDiv( 'salesPrice', 'COM_VIRTUEMART_PRODUCT_SALESPRICE', $product->prices );
						if ($product->prices['discountedPriceWithoutTax'] != $product->prices['priceWithoutTax']) {
							echo $this->currency->createPriceDiv( 'discountedPriceWithoutTax', 'COM_VIRTUEMART_PRODUCT_SALESPRICE_WITHOUT_TAX', $product->prices );
						} else {
							echo $this->currency->createPriceDiv( 'priceWithoutTax', 'COM_VIRTUEMART_PRODUCT_SALESPRICE_WITHOUT_TAX', $product->prices );
						}
						echo $this->currency->createPriceDiv( 'discountAmount', 'COM_VIRTUEMART_PRODUCT_DISCOUNT_AMOUNT', $product->prices );
						echo $this->currency->createPriceDiv( 'taxAmount', 'COM_VIRTUEMART_PRODUCT_TAX_AMOUNT', $product->prices );
						} ?>
						</div>

						<div>
						<?php // Product Details Button
						echo JHTML::link ( JRoute::_ ( 'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' . $product->virtuemart_category_id , FALSE), JText::_ ( 'COM_VIRTUEMART_PRODUCT_DETAILS' ), array ('title' => $product->product_name, 'class' => 'product-details' ) );
						?>
						</div>
				</div>
			</li>
		
	<?php endforeach; ?>
		</ul>


	</li>
<?php endforeach; ?>
</ul>
