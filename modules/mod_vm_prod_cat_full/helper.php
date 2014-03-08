<?php
/**
* VirtueMart Full Category List Module for Joomla! and Virtuemart
* Helper class
* @author		Andrew Patton
* @version		1.2.0
* @copyright	(C) 2012 Andrew Patton. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* This module is free software.
* See COPYRIGHT.php for copyright notices and details.
*/

class modVMFullCategoryList
{
	public static $cache;
	public static $category_model;
	
	/**
	 * Gets all children of a parent category
	 *
	 * @access	public
	 * @param int $catPId (the category id of desired parent category)
	 * @return array (object list with category id, name, if published, order, and parent id)
	 **/
	function getCatChildren($catPId = 0) {
		return modVMFullCategoryList::$cache->call( array( 'VirtueMartModelCategory', 'getChildCategoryList' ), 1, $catPId ); // the 1 is for vendor_id
	}

	/**
	 * Returns an array of the ids of all parent categories of specified category
	 *
	 * @access	public
	 * @param int $catid (the category id to check)
	 * @return array (list of parents, with current category as the last item and top-level parent category as the first)
	 **/
	function getCatParentIds($catid = 0) {
		// can't do this with cache object
		$parents = array( $catid );
		$cat_relations = modVMFullCategoryList::$category_model->getRelationInfo( $catid );
		$cat = new stdClass;
		$cat->virtuemart_category_id = $catid;
		while ( isset( $cat->virtuemart_category_id ) && !empty($cat_relations->category_parent_id)) {
			$cat = modVMFullCategoryList::$category_model->getParentCategory( $cat->virtuemart_category_id );
			array_unshift( $parents, $cat->virtuemart_category_id );
			$cat_relations = modVMFullCategoryList::$category_model->getRelationInfo( $cat->virtuemart_category_id );
			if ($cat_relations->category_parent_id) {
				error_log(print_r($cat_relations, true));
			}
		}
		return $parents;
	}

	/**
	 * Gets the Itemid for main virtuemart link in the menu
	 *
	 * @access	public
	 * @return int (Itemid for VirtueMart menu item)
	 **/
	function getVMItemId($catid = false) {
		$menu = &JSite::getMenu(); // use getMenu() function and totally avoid DB queries
		$items = $menu->getItems('access', 1); // get all menu items with access: public

		// If generic VM itemid hasn't yet been set by this function:
		if (JRequest::getInt('vmGenericItemid', -1) == -1) {
			$vmItemid = 0;
			$vmItemid1 = 0;
			$vmItemid2 = 0;
			$vmItemid3 = 0;
			foreach($items as $item) {
				$itemid = $item->id;
				if (strpos($item->link, 'com_virtuemart') !== false) { // first virtuemart menu item
					// let's get the sure bet out of the way:
					if (isset($item->query['view']) && $item->query['view'] == 'virtuemart') { // then it's definitely the VM home menu item
						$vmItemid = $itemid;
						break;
					}
					elseif (!$item->parent_id) { // if it's a top-level menu item (more likely to be important)
						$vmItemid1 = ($vmItemid1 ? $vmItemid1 : $itemid );
					}
					else { // for any other kind of VM link (will only be used if ALL other options fail)
						$vmItemid2 = ($vmItemid2 ? $vmItemid2 : $itemid );
					}
				}
			}
			$vmItemid = ($vmItemid ? $vmItemid : ($vmItemid1 ? $vmItemid1 : $vmItemid2));
			JRequest::setVar('vmGenericItemid', $vmItemid);
		}
		// Now check for specific category menu item if a catid was passed:
		if ($catid) {
			foreach($items as $item) {
				$itemid = $item->id;
				// if it's a virtuemart menu item and is the category page:
				if (strpos($item->link, 'com_virtuemart') !== false && isset($item->query['virtuemart_category_id']) && $item->query['virtuemart_category_id'] == $catid) {
					return $itemid;
				}
			}
		}
		return JRequest::getInt('vmGenericItemid');
	}

}
// initialize public static properties
modVMFullCategoryList::$cache = JFactory::getCache('com_virtuemart','callback');
modVMFullCategoryList::$category_model = new VirtueMartModelCategory();
?>