<?php
/**
*
* Show the products in a category
*
* @package	VirtueMart
* @subpackage
* @author RolandD
* @author Max Milbers
* @todo add pagination
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2012 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
 * @version $Id: default.php 6104 2012-06-13 14:15:29Z alatak $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

if ($this->category->haschildren) {

// Calculating Categories Per Row
$categories_per_row = VmConfig::get ( 'categories_per_row', 3 );
$category_cellwidth = ' uk-width-1-'.$categories_per_row;
?>

<ul class="uk-grid category-list" data-uk-grid-match="{target:'.uk-panel'}" data-uk-grid-margin>

<?php // Start the Output
if ($this->category->children ) {
    foreach ( $this->category->children as $category ) {

	    // Category Link
	    $caturl = JRoute::_ ( 'index.php?option=com_virtuemart&view=category&virtuemart_category_id=' . $category->virtuemart_category_id , FALSE);

		    // Show Category ?>
			<li class="<?php echo $category_cellwidth ?>">
				<div class="uk-panel uk-text-center">
					<h3 class="uk-panel-title">
					    <a href="<?php echo $caturl ?>" title="<?php echo $category->category_name ?>">
					    <?php echo $category->category_name ?>
					    <br />
					    <?php // if ($category->ids) {
						    echo $category->images[0]->displayMediaThumb("",false);
					    //} ?>
					    </a>
				    </h3>
			    </div>
		    </li>
	    <?php
    }
}
?>
</ul>
<?php } ?>