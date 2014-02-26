<?php
// Access
defined('_JEXEC') or die('Restricted access');

// Category and Columns Counter
$iCol = 1;
$iCategory = 1;

// Calculating Categories Per Row
$categories_per_row = VmConfig::get('homepage_categories_per_row', 3);
$category_cellwidth = 'uk-width-medium-1-' . $categories_per_row;
?>

	<h4><?php echo JText::_('COM_VIRTUEMART_CATEGORIES') ?></h4>

	<ul class="uk-grid" data-uk-grid-match="{target:'.uk-panel'}" data-uk-grid-margin>
		<?php
		// Start the Output
		foreach ($this->categories as $category) :

			// Category Link
			$caturl = JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id=' . $category->virtuemart_category_id, FALSE);

			// Show Category
			?>
			<li class="<?php echo $category_cellwidth ?>">
				<div class="uk-panel uk-text-center">
					<h2 class="uk-panel-title">
						<a href="<?php echo $caturl ?>" title="<?php echo $category->category_name ?>">
						<?php echo $category->category_name ?>
						<br />
						<?php
						if (!empty($category->images)) {
							echo $category->images[0]->displayMediaThumb("", false);
						}
						?>
						</a>
					</h2>
				</div>
			</li>
		<?php endforeach; ?>
	</ul>
