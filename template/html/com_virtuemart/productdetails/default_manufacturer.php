<?php
/**
 * Show the product details page
 * @package    VirtueMart
 * @subpackage
 * @author     Max Milbers, Valerie Isaksen
 * @link       http://www.virtuemart.net
 * @copyright  Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version    $Id: default_manufacturer.php 5409 2012-02-09 13:52:54Z alatak $
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
?>
<?php
$link = JRoute::_('index.php?option=com_virtuemart&view=manufacturer&virtuemart_manufacturer_id=' . $this->product->virtuemart_manufacturer_id . '&tmpl=component', FALSE);
$text = $this->product->mf_name;

/* Avoid JavaScript on PDF Output */
if (strtolower(JRequest::getWord('output')) == "pdf") {
	echo JHTML::_('link', $link, $text);
} else {
	?>

	<script>
		jQuery(function ($) {
			"use strict";
			(function () {
				$('#manuf-link').click(function () {
					$('#manuf-content').load('<?php echo $link ?>', {}, function () {
						var modal = new $.UIkit.modal.Modal("#manuf-details");
						if (modal.isActive()) {
							modal.hide();
						} else {
							modal.show();
						}
					});
				});
			})();
		});
	</script>

	<div id="manuf-details" class="uk-modal">
		<div class="uk-modal-dialog uk-text-left">
			<a class="uk-modal-close uk-close"></a>

			<div id="manuf-content"></div>
		</div>
	</div>

	<button id="manuf-link" class="uk-button uk-button-small uk-margin-small-top"
			type="button">
		<i class="uk-icon-info"></i>&nbsp;&nbsp;<?php echo $text ?>
	</button><br/>
<?php } ?>
