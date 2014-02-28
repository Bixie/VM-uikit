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
?>

<div class="manufacturer-details-view">
	<div class="uk-grid">
		<div class="uk-width-1-1">
			<h1><?php echo $this->manufacturer->mf_name; ?></h1>
		</div>
	</div>


	<div class="uk-grid">
		<div class="uk-width-1-4">
			<div class="uk-panel uk-panel-box">

			<?php // Manufacturer Image
			if (!empty($this->manufacturerImage)) { ?>
				<?php echo $this->manufacturerImage; ?>
			<?php } ?>
			<?php // Manufacturer Email
			if(!empty($this->manufacturer->mf_email)) { ?>
				<a class="uk-display-block" target="_self" href="mailto:<?php echo $this->manufacturer->mf_email; ?>">
					<i class="uk-icon-envelope-o uk-margin-small-right"></i>
					<?php echo JText::_('COM_VIRTUEMART_EMAIL'); ?>
				</a>
			<?php } ?>

			<?php // Manufacturer URL
			if(!empty($this->manufacturer->mf_url)) { ?>
				<a class="uk-display-block" target="_blank" href="<?php echo $this->manufacturer->mf_url; ?>">
					<i class="uk-icon-home uk-margin-small-right"></i>
					<?php echo JText::_('COM_VIRTUEMART_MANUFACTURER_PAGE'); ?>
				</a>
			<?php } ?>

			</div>
		</div>
		<div class="uk-width-3-4">
		
		<?php // Manufacturer Description
		if(!empty($this->manufacturer->mf_desc)) { ?>
				<?php echo $this->manufacturer->mf_desc ?>
		<?php } ?>

		<?php // Manufacturer Product Link
		$manufacturerProductsURL = JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_manufacturer_id=' . $this->manufacturer->virtuemart_manufacturer_id, FALSE);

		if(!empty($this->manufacturer->virtuemart_manufacturer_id)) { ?>
			<div class="uk-clearfix">
				<a class="uk-button uk-float-right" target="_self" href="<?php echo $manufacturerProductsURL; ?>">
					<i class="uk-icon-search uk-margin-small-right"></i>
					<?php echo JText::sprintf('COM_VIRTUEMART_PRODUCT_FROM_MF',$this->manufacturer->mf_name); ?>
				</a>
			</div>
		<?php } ?>
		
		</div>

	</div>
</div>