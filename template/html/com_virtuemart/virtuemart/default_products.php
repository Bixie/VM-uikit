<?php defined('_JEXEC') or die('Restricted access');


// Separator
$verticalseparator = " vertical-separator";
$types = array_keys($this->products);

//tabs
?>
<ul class="uk-tab uk-margin-top" data-uk-tab="{connect:'#vm-producttabs'}">
<?php
foreach ($this->products as $type => $productList ) :
	$productTitle = JText::_('COM_VIRTUEMART_'.$type.'_PRODUCT');
	$activeClass = !isset($activeClass)?'class="uk-active"':'';
?>
	<li <?php echo $activeClass; ?>>
		<a href="#"><?php echo $productTitle ?></a>
	</li>
<?php endforeach; ?>
</ul>
<ul id="vm-producttabs" class="uk-switcher uk-margin">
<?php
foreach ($this->products as $type => $productList ) :
// Calculating Products Per Row
$products_per_row = VmConfig::get ( 'homepage_products_per_row', 3 ) ;
$cellwidth = ' uk-width-medium-1-'.$products_per_row;
?>

	<li class="<?php echo $type ?>-view">


		<ul class="uk-grid" data-uk-grid-match="{target:'.uk-panel'}" data-uk-grid-margin>
	<?php // Start the Output

	foreach ( $productList as $product ) :

			// Show Products ?>
			<li class="product <?php echo $cellwidth?>">
				<div class="uk-panel uk-panel-box">
					<div class="uk-text-center">

						<div class="uk-thumbnail uk-width-1-1">
						<?php // Product Image
						if ($product->images) {
							?>
							<a href="<?php echo JRoute::_ ( 'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' . $product->virtuemart_category_id, FALSE ); ?>" class="uk-margin-top uk-margin"  data-spotlight="effect:bottom;">
								<img src="<?php echo $product->images[0]->file_url_thumb; ?>" alt="<?php echo $product->product_name; ?>" />
								<div class="overlay"><?php echo $product->product_name; ?></div>
							</a>
							<?php
						}
						?>
						</div>


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

					<a class="uk-button uk-button-primary uk-float-right" 
						href="<?php echo JRoute::_ ( 'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' . $product->virtuemart_category_id , FALSE); ?>" 
						title="<?php echo $product->product_name; ?>">
						<?php echo JText::_ ( 'COM_VIRTUEMART_PRODUCT_DETAILS' ); ?>
						&nbsp;<i class="uk-icon-angle-right"></i>
					</a>
				</div>
			</li>
		
	<?php endforeach; ?>
		</ul>


	</li>
<?php endforeach; ?>
</ul>
