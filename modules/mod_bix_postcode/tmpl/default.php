<?php
/**
 *	com_bix_printshop - Online-PrintStore for Joomla
 *  Copyright (C) 2010-2012 Matthijs Alles
 *	Bixie.nl
 *
 */

// no direct access
defined('_JEXEC') or die;

?>
<div class="uk-panel uk-panel-box uk-panel-box-primary zipcheck uk-animation-slide-left">
	<h3 class="uk-panel-title uk-margin-small-top"><?php echo $params->get('title','')?></h3>
	<div class="uk-grid uk-margin-small-top">
		<div class="uk-width-medium-2-3" style="min-height:120px;">
			<p><?php echo $params->get('text','')?></p>
		</div>
		<div class="uk-width-medium-1-3">
			<div class="uk-margin-small-top">
				<input class="uk-form-large uk-width-5-10 uk-text-center uk-float-left" id="postcode" type="text" placeholder="Postcode" />
				<button class="uk-button uk-button-large uk-width-4-10 uk-text-center uk-float-right" id="check">
					<i class="uk-icon-sign-in uk-margin-small-right"></i>Check!
				</button>
			</div>
			<a href="/account" class="uk-button uk-button-large uk-button-primary uk-width-1-1 uk-text-center uk-margin-top uk-hidden" id="register">
				<i class="uk-icon-user uk-icon-small uk-margin-right"></i>Maak een account aan
			</a>
		</div>
	</div>
</div>