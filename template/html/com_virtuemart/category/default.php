<?php
/**
 *
 * Show the products in a category
 *
 * @package    VirtueMart
 * @subpackage
 * @author RolandD
 * @author Max Milbers
 * @todo add pagination
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: default.php 6556 2012-10-17 18:15:30Z kkmediaproduction $
 */

//vmdebug('$this->category',$this->category);
//vmdebug ('$this->category ' . $this->category->category_name);
// Check to ensure this file is included in Joomla!
defined ('_JEXEC') or die('Restricted access');
JHTML::_ ('behavior.modal');

if (empty($this->keyword) and !empty($this->category)) {
	?>
<div class="category_description">
	<?php echo $this->category->category_description; ?>
</div>
<?php
}

/* Show child categories */

if (VmConfig::get ('showCategory', 1) and empty($this->keyword)) {
	if (!empty($this->category->haschildren)) {

		// Calculating Categories Per Row
		$categories_per_row = VmConfig::get ('categories_per_row', 3);
		$category_cellwidth = ' uk-width-1-' . $categories_per_row;
		?>

		<ul class="uk-grid" data-uk-grid-match="{target:'.uk-panel'}" data-uk-grid-margin>

		<?php // Start the Output
		if (!empty($this->category->children)) {

			foreach ($this->category->children as $category) {
				// Category Link
				$caturl = JRoute::_ ('index.php?option=com_virtuemart&view=category&virtuemart_category_id=' . $category->virtuemart_category_id, FALSE);

				// Show Category
				?>
				<li class="<?php echo $category_cellwidth ?>">
					<div class="uk-panel uk-text-center">
						<h2 class="uk-panel-title">
							<a href="<?php echo $caturl ?>" title="<?php echo $category->category_name ?>">
								<?php echo $category->category_name ?>
								<br/>
								<?php // if ($category->ids) {
								echo $category->images[0]->displayMediaThumb ("", FALSE);
								//} ?>
							</a>
						</h2>
					</div>
				</li>
			<?php
			}
		}
?>
	</ul>

	<?php
	}
}
?>
<div class="browse-view">
	<?php

	if (!empty($this->keyword)) {
		?>
	<h3><?php echo $this->keyword; ?></h3>
		<?php
	} ?>
	<?php if ($this->search !== NULL) {

		$category_id  = JRequest::getInt ('virtuemart_category_id', 0); ?>
	<form action="<?php echo JRoute::_ ('index.php?option=com_virtuemart&view=category&limitstart=0', FALSE); ?>" method="get" class="uk-form">

		<!--BEGIN Search Box -->
		<div class="virtuemart_search">
			<?php echo $this->searchcustom ?>
			<br/>
			<?php echo $this->searchcustomvalues ?>
			<input name="keyword" class="inputbox" type="text" size="20" value="<?php echo $this->keyword ?>"/>
			<input type="submit" value="<?php echo JText::_ ('COM_VIRTUEMART_SEARCH') ?>" class="button" onclick="this.form.keyword.focus();"/>
		</div>
		<input type="hidden" name="search" value="true"/>
		<input type="hidden" name="view" value="category"/>
		<input type="hidden" name="option" value="com_virtuemart"/>
		<input type="hidden" name="virtuemart_category_id" value="<?php echo $category_id; ?>"/>

	</form>
	<!-- End Search Box -->
		<?php } ?>

	<?php // Show products
	if (!empty($this->products)) {
		?>
	<div class="uk-grid">
		<div class="uk-width-7-10">
			<?php echo $this->orderByList['orderby']; ?>
			<?php echo $this->orderByList['manufacturer']; ?>
		</div>
		<div class="uk-width-3-10">
			<?php echo $this->vmPagination->getResultsCounter ();?><br/>
			<?php echo $this->vmPagination->getLimitBox ($this->category->limit_list_step); ?>
			<div class="uk-form">
				<?php echo $this->vmPagination->getPagesLinks (); ?>
				<div class="uk-float-right"><?php echo $this->vmPagination->getPagesCounter (); ?></div>
			</div>
		</div>
	</div> <!-- end of orderby-displaynumber -->
	<hr/>
	<h1 class="uk-h3"><?php echo $this->category->category_name; ?></h1>
	<ul class="uk-grid" data-uk-grid-match="{target:'.uk-panel'}" data-uk-grid-margin>
		<?php

		// Calculating Products Per Row
		$BrowseProducts_per_row = $this->perRow;
		$Browsecellwidth = 'uk-width-medium-1-' . $BrowseProducts_per_row;

		// Start the Output
		foreach ($this->products as $product) {

			// Show Products
			?>
			<li class="<?php echo $Browsecellwidth ?>">
				<div class="uk-panel uk-text-center">
					<div class="uk-grid">
						<div class="uk-width-large-4-10 uk-width-medium-1-2">
							<div class="uk-thumbnail uk-width-1-1">
								<a title="<?php echo $product->product_name ?>" rel="vm-additional-images" href="<?php echo $product->link; ?>">
									<?php
										echo $product->images[0]->displayMediaThumb('class="browseProductImage"', false);
									?>
								</a>
							</div>

							<!-- The "Average Customer Rating" Part -->
							<?php // Output: Average Product Rating
							if ($this->showRating) {
								$maxrating = VmConfig::get('vm_maximum_rating_scale', 5);

								if (empty($product->rating)) {
									?>
									<span class="vote"><?php echo JText::_('COM_VIRTUEMART_RATING') . ' ' . JText::_('COM_VIRTUEMART_UNRATED') ?></span>
								<?php
								} else {
									$ratingwidth = $product->rating * 12; //I don't use round as percetntage with works perfect, as for me
									?>
									<span class="vote">
										<?php echo JText::_('COM_VIRTUEMART_RATING') . ' ' . round($product->rating) . '/' . $maxrating; ?><br/>
										<span title=" <?php echo (JText::_("COM_VIRTUEMART_RATING_TITLE") . round($product->rating) . '/' . $maxrating) ?>" class="category-ratingbox" style="display:inline-block;">
											<span class="stars-orange" style="width:<?php echo $ratingwidth.'px'; ?>">
											</span>
										</span>
									</span>
								<?php
								}
							}
							if ( VmConfig::get ('display_stock', 1)) { 
								$progressWidth = 80;
								$progressClass = 'success';
								if ($product->stock->stock_level == 'lowstock') {
									$progressWidth = 40;
									$progressClass = 'warning';
								}
								if ($product->stock->stock_level == 'nostock') {
									$progressWidth = 10;
									$progressClass = 'danger';
								}
							?>
								<!-- 						if (!VmConfig::get('use_as_catalog') and !(VmConfig::get('stockhandle','none')=='none')){?> -->
								
								<div class="uk-margin-top">
									<div class="uk-progress uk-progress-small uk-progress-<?php echo $progressClass ?> uk-margin-remove" title="<?php echo $product->stock->stock_tip ?>">
										<div class="uk-progress-bar" style="width: <?php echo $progressWidth ?>%;"></div>
									</div>
									<small><?php echo JText::_ ('COM_VIRTUEMART_STOCK_LEVEL_DISPLAY_TITLE_TIP') ?></small>
								</div>
							<?php } ?>
						</div>

						<div class="uk-width-large-6-10 uk-width-medium-1-2">

							<h2 class="uk-h4 uk-margin-remove"><?php echo JHTML::link ($product->link, $product->product_name); ?></h2>

							<?php // Product Short Description
							if (!empty($product->product_s_desc)) {
								?>
								<p class="product_s_desc">
									<?php echo shopFunctionsF::limitStringByWord ($product->product_s_desc, 40, '...') ?>
								</p>
								<?php } ?>

							<div class="product-price marginbottom12" id="productPrice<?php echo $product->virtuemart_product_id ?>">
								<?php
								if ($this->show_prices == '1') {
									if ($product->prices['salesPrice']<=0 and VmConfig::get ('askprice', 1) and  !$product->images[0]->file_is_downloadable) {
										echo JText::_ ('COM_VIRTUEMART_PRODUCT_ASKPRICE');
									}
									//todo add config settings
									if ($this->showBasePrice) {
										echo $this->currency->createPriceDiv ('basePrice', 'COM_VIRTUEMART_PRODUCT_BASEPRICE', $product->prices);
										echo $this->currency->createPriceDiv ('basePriceVariant', 'COM_VIRTUEMART_PRODUCT_BASEPRICE_VARIANT', $product->prices);
									}
									echo $this->currency->createPriceDiv ('variantModification', 'COM_VIRTUEMART_PRODUCT_VARIANT_MOD', $product->prices);
									if (round($product->prices['basePriceWithTax'],$this->currency->_priceConfig['salesPrice'][1]) != $product->prices['salesPrice']) {
										echo '<div class="price-crossed" >' . $this->currency->createPriceDiv ('basePriceWithTax', 'COM_VIRTUEMART_PRODUCT_BASEPRICE_WITHTAX', $product->prices) . "</div>";
									}
									if (round($product->prices['salesPriceWithDiscount'],$this->currency->_priceConfig['salesPrice'][1]) != $product->prices['salesPrice']) {
										echo $this->currency->createPriceDiv ('salesPriceWithDiscount', 'COM_VIRTUEMART_PRODUCT_SALESPRICE_WITH_DISCOUNT', $product->prices);
									}
									echo $this->currency->createPriceDiv ('salesPrice', 'COM_VIRTUEMART_PRODUCT_SALESPRICE', $product->prices);
									if ($product->prices['discountedPriceWithoutTax'] != $product->prices['priceWithoutTax']) {
										echo $this->currency->createPriceDiv ('discountedPriceWithoutTax', 'COM_VIRTUEMART_PRODUCT_SALESPRICE_WITHOUT_TAX', $product->prices);
									} else {
										echo $this->currency->createPriceDiv ('priceWithoutTax', 'COM_VIRTUEMART_PRODUCT_SALESPRICE_WITHOUT_TAX', $product->prices);
									}
									echo $this->currency->createPriceDiv ('discountAmount', 'COM_VIRTUEMART_PRODUCT_DISCOUNT_AMOUNT', $product->prices);
									echo $this->currency->createPriceDiv ('taxAmount', 'COM_VIRTUEMART_PRODUCT_TAX_AMOUNT', $product->prices);
									$unitPriceDescription = JText::sprintf ('COM_VIRTUEMART_PRODUCT_UNITPRICE', $product->product_unit);
									echo $this->currency->createPriceDiv ('unitPrice', $unitPriceDescription, $product->prices);
								} ?>

							</div>
						</div>
						<div class="uk-width-1-1">
							<a class="uk-button uk-button-primary uk-float-right" 
								href="<?php echo $product->link; ?>" 
								title="<?php echo $product->product_name; ?>">
								<?php echo JText::_ ( 'COM_VIRTUEMART_PRODUCT_DETAILS' ); ?>
								&nbsp;<i class="uk-icon-angle-right"></i>
							</a>
						</div>
					</div>
				</div>
				<!-- end of spacer -->
			</li> <!-- end of product -->
			<?php

		} // end of foreach ( $this->products as $product )
		?>
	</ul>
	<div class="vm-pagination"><?php echo $this->vmPagination->getPagesLinks (); ?><span class="uk-float-right"><?php echo $this->vmPagination->getPagesCounter (); ?></span></div>

		<?php
	} elseif ($this->search !== NULL) {
		echo JText::_ ('COM_VIRTUEMART_NO_RESULT') . ($this->keyword ? ' : (' . $this->keyword . ')' : '');
	}
	?>
</div><!-- end browse-view -->