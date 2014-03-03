<?php // no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Outputs one level of categories and calls itself for any subcategories
 *
 * @access	public
 * @param int $catPId (the category_id of current parent category)
 * @param int $level (the current category level [main cats are 0, 1st subcats are 1])
 * @param object $params (the params object containing all params for this module)
 * @param int $current_cat (category_id from the request array, if it exists)
 * @return nothing - echos html directly
 **/
// Because this function is declared in the view, need to make sure it hasn't already been declared:
if ( ! function_exists( 'vmFCLBuildMenu' ) ) {
	function vmFCLBuildMenu($catPId = 0, $level = 1, $settings, $current_cat = 0, $active = array()) {
		if ( (!$settings['level_end'] || $level < $settings['level_end']) && $rows = modVMFullCategoryList::getCatChildren($catPId) ) :
			if ( $level >= $settings['level_start'] ) : 
				$classname = $level==1?'uk-nav uk-nav-parent-icon uk-nav-side ':'uk-nav-sub ';
				$accordion = $level==1?'data-uk-nav="{multiple:true}"':'';?>
			<ul class="<?php echo $classname ?>level<?php echo $level . $settings['menu_class'] ?>" <?php echo $accordion ?>>
			<?php endif;
			foreach( $rows as $row ) :
				$cat_active = in_array( $row->virtuemart_category_id, $active );
				if ( $settings['current_filter'] && $level < count( $active ) && ! $cat_active )
					continue;
				
				if ( $level >= $settings['level_start'] ) :
					$itemid = modVMFullCategoryList::getVMItemId($row->virtuemart_category_id);
					$itemid = ($itemid ? '&Itemid='.$itemid : '');
					$link =	JFilterOutput::ampReplace( JRoute::_( 'index.php?option=com_virtuemart' . '&view=category&virtuemart_category_id=' . $row->virtuemart_category_id . $itemid ) );
					$classnameLi = $level>1?'':'uk-parent ';
					?>
					<li <?php echo ($current_cat == $row->virtuemart_category_id ? 'id="current"' : '');?>class="<?php echo $classnameLi ?><?php if ( $cat_active ) echo ' uk-active'; ?>">
						<?php if ($level == 1) : //this is getting thicky... ?>
						<a class="level<?php echo $level . $settings['menu_class'] ?> cat-id-<?php echo $row->virtuemart_category_id ?>" href="#">
						<?php echo htmlspecialchars(stripslashes($row->category_name), ENT_COMPAT, 'UTF-8') ?></a>
						<?php else: ?>
						<a class="level<?php echo $level . $settings['menu_class'] ?> cat-id-<?php echo $row->virtuemart_category_id ?>" href="<?php echo $link ?>" target="_self">
						<?php echo htmlspecialchars(stripslashes($row->category_name), ENT_COMPAT, 'UTF-8') ?></a>
						<?php endif; ?>
				<?php endif;
				// Check for sub categories
				vmFCLBuildMenu( $row->virtuemart_category_id, $level + 1, $settings, $current_cat, $active );
				if ($level >= $settings['level_start']) : ?>
				</li>
			<?php endif;
			endforeach;
			if ($level >= $settings['level_start']) : ?>
			</ul>
			<?php endif;
		endif;
	}
}

// With what category, if any, do we start?
// Default to cat filter param:
$catid = $cat_filter;
$level = 1;
// Set up current category array (for displaying '.active' class and for current category filter, if applicable)
$active = array();
if ( $current_cat ) {
	$active = modVMFullCategoryList::getCatParentIds( $current_cat );
	if ( $settings['current_filter'] ) {
		$catid = $current_cat;
		$level = count( $active );
		if ( $settings['level_start'] ) {
			// Adjust the starting point
			array_unshift( $active, 0 );
			$catid = $active[$settings['level_start']-1];
			$level = $settings['level_start'];
		}
	}
}
if ( $cat_filter && ! $settings['current_filter'] ) {
	$parents = modVMFullCategoryList::getCatParentIds( $cat_filter );
	$level = count( $parents );
}
// Call the display function for the first menu item:
vmFCLBuildMenu( $catid, $level, $settings, $current_cat, $active );
?>

<ul class="uk-nav uk-nav-parent-icon uk-nav-side uk-margin-top" data-uk-nav="{multiple:true}">
	<li class="uk-parent <?php echo $virtuemart_manufacturer_id>0?'uk-active':'';?>">
		<a href="#"><?php echo JText::_('COM_VIRTUEMART_MANUFACTURERS_LBL'); ?></a>
		<ul class="uk-nav-sub">
		<?php foreach ($manufacturers as $manufacturer) :
			$link = JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_manufacturer_id=' . $manufacturer->virtuemart_manufacturer_id);
			?>
			<li>
				<a class="<?php echo $virtuemart_manufacturer_id==$manufacturer->virtuemart_manufacturer_id?'uk-active':'';?>" href="<?php echo $link; ?>">
				<?php echo $manufacturer->mf_name; ?></a>
			</li>
		<?php endforeach; ?>
		</ul>
	</li>
</ul>
<?php
// Are there any better ways to make this follow joomla's MVC pattern
// (by outputting a tree structure returned by helper class, for ex)? like:
// while ($item) {
	// output
	// $item = $item->child;
//}
// Probably way out of the scope of this module...
// see mod_mainmenu if you don't believe it

