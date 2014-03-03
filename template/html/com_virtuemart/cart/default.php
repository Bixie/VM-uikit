<?php
/**
 *
 * Layout for the shopping cart
 *
 * @package    VirtueMart
 * @subpackage Cart
 * @author Max Milbers
 *
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: cart.php 2551 2010-09-30 18:52:40Z milbo $
 */

// Check to ensure this file is included in Joomla!
defined ('_JEXEC') or die('Restricted access');

JHtml::_ ('behavior.formvalidation');
$document = JFactory::getDocument ();
$document->addScriptDeclaration ("

//<![CDATA[
	jQuery(document).ready(function($) {
	if ( $('#STsameAsBTjs').is(':checked') ) {
				$('#output-shipto-display').hide();
			} else {
				$('#output-shipto-display').show();
			}
		$('#STsameAsBTjs').click(function(event) {
			if($(this).is(':checked')){
				$('#STsameAsBT').val('1') ;
				$('#output-shipto-display').hide();
			} else {
				$('#STsameAsBT').val('0') ;
				$('#output-shipto-display').show();
			}
		});
	});

//]]>

");
$document->addStyleDeclaration ('#facebox .content {display: block !important; height: 480px !important; overflow: auto; width: 560px !important; }');

?>

<div class="cart-view">
	<div class="uk-grid">
		<div class="uk-width-2-3">
			<h1><?php echo JText::_ ('COM_VIRTUEMART_CART_TITLE'); ?></h1>
		</div>
		<?php if (VmConfig::get ('oncheckout_show_steps', 1) && $this->checkout_task === 'confirm') {
		vmdebug ('checkout_task', $this->checkout_task);
		echo '<div class="checkoutStep" id="checkoutStep4">' . JText::_ ('COM_VIRTUEMART_USER_FORM_CART_STEP4') . '</div>';
	} ?>
		<div class="uk-width-1-3 uk-text-right">
			<?php // Continue Shopping Button
			if (!empty($this->continue_link_html)) {
				echo $this->continue_link_html;
			} ?>
		</div>
	</div>

	<div class="uk-grid">
		<div class="uk-width-1-1">
			<?php echo shopFunctionsF::getLoginForm($this->cart, false); ?>
		</div>
	</div>
	<?php
	// This displays the form to change the current shopper
	$adminID = JFactory::getSession()->get('vmAdminID');
	if ((JFactory::getUser()->authorise('core.admin', 'com_virtuemart') || JFactory::getUser($adminID)->authorise('core.admin', 'com_virtuemart')) && (VmConfig::get ('oncheckout_change_shopper', 0))) { 
		echo $this->loadTemplate ('shopperform');
	}
	?>

<?php
	// This displays the pricelist MUST be done with tables, because it is also used for the emails
	echo $this->loadTemplate ('pricelist');

	// added in 2.0.8
	?>

	<div id="checkout-advertise-box">
		<?php
		if (!empty($this->checkoutAdvertise)) {
			foreach ($this->checkoutAdvertise as $checkoutAdvertise) {
				?>
				<div class="checkout-advertise">
					<?php echo $checkoutAdvertise; ?>
				</div>
				<?php
			}
		}
		?>
	</div>
	<?php
	if (!VmConfig::get('oncheckout_opc', 1)) {
		if ($this->checkout_task) {
			$taskRoute = '&task=' . $this->checkout_task;
		}
		else {
			$taskRoute = '';
		}
	?>
	<form method="post" class="uk-form"id="checkoutForm" name="checkoutForm" action="<?php echo JRoute::_ ('index.php?option=com_virtuemart&view=cart' . $taskRoute, $this->useXHTML, $this->useSSL); ?>">
	<?php } ?>
		<div class="uk-grid uk-margin-top" data-uk-grid-match="{target:'.uk-panel'}">
			<div class="uk-width-medium-1-2">
				<div class="uk-panel uk-panel-box">
					<fieldset>
						<legend><?php echo JText::_ ('COM_VIRTUEMART_COMMENT_CART'); ?></legend>
						<?php // Leave A Comment Field ?>
						<textarea class="customer-comment uk-width-1-1" name="customer_comment" cols="30" rows="3"><?php echo $this->cart->customer_comment; ?></textarea>
					</fieldset>
				</div>
			</div>
			<div class="uk-width-medium-1-2">
				<div class="uk-panel uk-panel-box">
					<fieldset>
						<legend><?php echo JText::_ ('Bevestigen'); ?></legend>
						<?php // Continue and Checkout Button ?>
						<div class="checkout-button-top">

							<?php // Terms Of Service Checkbox
							if (!class_exists ('VirtueMartModelUserfields')) {
								require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'userfields.php');
							}
							$userFieldsModel = VmModel::getModel ('userfields');
							if ($userFieldsModel->getIfRequired ('agreed')) {
									if (!class_exists ('VmHtml')) {
										require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'html.php');
									}
									?>
										<div class="uk-grid">
											<div class="uk-width-1-10 terms-of-service">
											<?php echo VmHtml::checkbox ('tosAccepted', $this->cart->tosAccepted, 1, 0, 'class="terms-of-service"'); ?>
											</div>
									<?php
									if (VmConfig::get ('oncheckout_show_legal_info', 1)) {
										?>
											<div class="uk-width-9-10 terms-of-service">

												<label for="tosAccepted">
													<a href="#full-tos" class="terms-of-service" id="terms-of-service" data-lightbox="type:inline;width:800px;height:90%"
													 target="_blank">
														<span class="vmicon vm2-termsofservice-icon"></span>
														<?php echo JText::_ ('COM_VIRTUEMART_CART_TOS_READ_AND_ACCEPTED'); ?>
													</a>
												</label>
												<div class="uk-hidden">
													<div id="full-tos">
														<h2><?php echo JText::_ ('COM_VIRTUEMART_CART_TOS'); ?></h2>
														<?php echo $this->cart->vendor->vendor_terms_of_service; ?>
													</div>
												</div>
											</div>
										</div><?php //echo JRoute::_ ('index.php?option=com_virtuemart&view=vendor&layout=tos&virtuemart_vendor_id=1', FALSE) ?>
										<?php
									} // VmConfig::get('oncheckout_show_legal_info',1)
									//echo '<span class="tos">'. JText::_('COM_VIRTUEMART_CART_TOS_READ_AND_ACCEPTED').'</span>';
							}
							echo $this->checkout_link_html;
							?>
						</div>
					</fieldset>
				</div>
			</div>
		</div>

		<?php // Continue and Checkout Button END ?>
		<input type='hidden' name='order_language' value='<?php echo $this->order_language; ?>'/>
		<input type='hidden' id='STsameAsBT' name='STsameAsBT' value='<?php echo $this->cart->STsameAsBT; ?>'/>
		<input type='hidden' name='task' value='<?php echo $this->checkout_task; ?>'/>
		<input type='hidden' name='option' value='com_virtuemart'/>
		<input type='hidden' name='view' value='cart'/>
	</form>
</div>
