<?php
/**
*
* Description
*
* @package	VirtueMart
* @subpackage Manufacturer
* @author Kohl Patrick, Eugen Stranz
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: default.php 2701 2011-02-11 15:16:49Z impleri $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Calculating Categories Per Row
$manufacturerPerRow = VmConfig::get('manufacturer_per_row', 3);
$manufacturerCellWidth = ' uk-width-1-'.$manufacturerPerRow;


// Lets output the categories, if there are some
if (!empty($this->manufacturers)) { ?>

<div class="manufacturer-view-default">

	<ul class="uk-grid manufacturer-list" data-uk-grid-match="{target:'.uk-panel'}" data-uk-grid-margin>
	<?php // Start the Output
	foreach ($this->manufacturers as $manufacturer) :

		// Manufacturer Elements
		$manufacturerURL = JRoute::_('index.php?option=com_virtuemart&view=manufacturer&virtuemart_manufacturer_id=' . $manufacturer->virtuemart_manufacturer_id, FALSE);
		$manufacturerIncludedProductsURL = JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_manufacturer_id=' . $manufacturer->virtuemart_manufacturer_id, FALSE);
		$manufacturerImage = $manufacturer->images[0]->displayMediaThumb("",false);

		// Show Category ?>
		<li class="<?php echo $manufacturerCellWidth ?>">
			<div class="uk-panel uk-panel-box uk-text-center">
				<h3 class="uk-panel-title">
					<a title="<?php echo $manufacturer->mf_name; ?>" href="<?php echo $manufacturerURL; ?>"><?php echo $manufacturerImage;?></a><br/>
					<a title="<?php echo $manufacturer->mf_name; ?>" href="<?php echo $manufacturerURL; ?>"><?php echo $manufacturer->mf_name; ?></a>
				</h3>
			</div>
		</li>
	<?php endforeach; ?>
	</ul>
</div>
<?php
}
?>