<?php
/**
 *
 * Show the product details page
 *
 * @package	VirtueMart
 * @subpackage
 * @author Max Milbers, Valerie Isaksen

 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: default_images.php 6188 2012-06-29 09:38:30Z Milbo $
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// echo '<pre>';
// print_r($this->product->images);
// echo '</pre>';
if (!empty($this->product->images)) :
	$count = count($this->product->images);
	$slidesetSettings = array(
		'style'=>'showcase_buttons',
		'autoplay'=>0,
		'interval'=>5000,
		'width'=>'auto',
		'height'=>'400px',
		'index'=>0,
		'order'=>'default',
		'buttons'=>1,
		'slices'=>20,
		'animated'=>'fade',
		'caption_animation_duration'=>500,
		'effect'=>'slide',
		'slideset_buttons'=>1,
		'items_per_set'=>3,
		'slideset_effect_duration'=>300
	);
	$widget_id = uniqid();
	$settings  = $slidesetSettings;
	if ($count <= $settings['items_per_set']) $settings['slideset_buttons'] = 0;
	if ($count == 1) $settings['buttons'] = 0;
	$sets      = array_chunk($this->product->images, $settings['items_per_set']);

	foreach (array_keys($sets) as $s) {
		$nav[] = '<li><span></span></li>';
	}

	$i = 0;
 ?>

<div id="showcase-<?php echo $widget_id; ?>" class="wk-slideshow-showcasebuttons" data-widgetkit="showcase" data-options='<?php echo json_encode($settings); ?>'>

	<div id="slideshow-<?php echo $widget_id; ?>" class="wk-slideshow">
		<div class="slides-container">
			<ul class="slides">
				<?php foreach ($this->product->images as $key => $item) : ?>
				<?php  
					/* Lazy Loading */
					// $item["content"] = ($i==$settings['index']) ? $item["content"] : $this['image']->prepareLazyload($item["content"]);
				?>
				<li>
					<article class="wk-content clearfix uk-text-center">
						<img src="<?php echo $item->file_url; ?>" alt="<?php echo $item->file_description; ?>" style="max-height:<?php echo $settings['height']; ?>;"/>
					</article>
				</li>
				<?php $i=$i+1;?>
				<?php endforeach; ?>
			</ul>
			<?php if ($settings['buttons']): ?><div class="next"></div><div class="prev"></div><?php endif; ?>
		</div>
	</div>

	<div id="slideset-<?php echo $widget_id;?>" class="wk-slideset <?php if (!$settings['slideset_buttons']) echo 'no-buttons'; ?>">
		<div>
			<div class="sets">
			<?php if ($settings['buttons']): ?>
				<?php foreach ($sets as $set => $items) : ?>
				<ul class="set">
					<?php foreach ($items as $item) : ?>
					<li>
						<div><div><img src="<?php echo $item->file_url_thumb ?>" alt=""/></div></div>
					</li>
					<?php endforeach; ?>
				</ul>
				<?php endforeach; ?>
			<?php endif; ?>
			</div>
			<?php if ($settings['slideset_buttons']): ?><div class="next"></div><div class="prev"></div><?php endif; ?>
		</div>
	</div>
	
</div>
<?php endif; ?>

