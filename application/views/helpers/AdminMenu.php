<?php
class Zend_View_Helper_AdminMenu  
{
	function adminMenu(){
		$storage = new Zend_Auth_Storage_Session();
        $data = $storage->read();
        if(!empty($data)){
			$usersGroups = new Application_Model_UsersGroups;
			$usersGroupsMapper = new Application_Model_UsersGroupsMapper;
			$usersGroupsMapper->find($data->group, $usersGroups);

			$rest = unserialize($usersGroups->getRestrictions());
			$menu = '<div class="admin-menu" style="display:none;">';
			if($rest[4] == 1) $menu .= '<fieldset>
										<legend><img src="/images/admin/page.png" width="30" height="30" alt="icon" /> Pages</legend>
										<a href="/admin_pages/">Listing</a>
										<a href="/admin_pages/edit/">New Page</a>
										<a href="/admin_pages/ping/">Ping Google</a>
										</fieldset>';
			if($rest[5] == 1) $menu .= '<fieldset>
										<legend><img src="/images/admin/diagram_v2_17.png" width="30" height="30" alt="icon" /> Inventory</legend><a href="/admin_inventory/">Listing</a></fieldset>';
			if($rest[15] == 1) $menu .= '<fieldset><legend><img src="/images/admin/tomato_battle.png" width="30" height="30" alt="icon" /> Recipes</legend><a href="/admin_recipes/">Listing</a></fieldset>';
//			if($rest[9] == 1) $menu .= '<fieldset><legend><img src="/images/admin/report_check.png" width="30" height="30" alt="icon" /> Reviews</legend><a href="/admin_reviews/">Listing</a></fieldset>';
			if($rest[10] == 1) $menu .= '<fieldset>
										<legend><img src="/images/admin/date.png" width="30" height="30" alt="icon" /> Events</legend><a href="/admin_events/">Listing</a>
						<a href="/admin_events/edit/">New Event</a></fieldset>';
if($rest[17] == 1) $menu .= '<fieldset>
						<legend>Press Releases</legend><a href="/admin_pressreleases/">Listing</a>
						<a href="/admin_pressreleases/edit/">New Press Release</a></fieldset>';
if($rest[18] == 1) $menu .= '<fieldset>
						<legend>Partners</legend><a href="/admin_partners/">Partners</a>
						<a href="/admin_partners/edit/">New Partner</a></fieldset>';
			if($rest[11] == 1) $menu .= '<fieldset>
										<legend><img src="/images/admin/newspaper.png" width="30" height="30" alt="icon" /> News</legend><a href="/admin_news_category_index/">Listing</a></fieldset>';
			if($rest[3] == 1 || $rest[7] == 1) {
				$menu .= '<fieldset><legend><img src="/images/admin/movie.png" width="30" height="30" alt="icon" /> Videos</legend><a href="/admin_videos/">Listing</a>';
				if($rest[3] == 1) {
					$menu .= '<a href="/admin_videos/edit/">New Video</a>
								<a href="/admin_videos/">List Videos</a>';
				}
				if($rest[7] == 1) {
					$menu .= '<a href="/admin_playlists/edit/">New Playlists</a>
							  <a href="/admin_playlists/">List Playlists</a>';
				}
				$menu .= '</fieldset>';
			}
if($rest[19] == 1) $menu .= '<fieldset><legend>Reference</legend><a href="/admin_reference/">Listing</a>
						<a href="/admin_reference/edit/">New Reference Material</a></fieldset>';
			if($rest[12] == 1) $menu .= '<fieldset><legend><img src="/images/admin/slide_show.png" width="30" height="30" alt="icon" /> Slideshows</legend><a href="/admin_slideshow/">Listing</a>
						<a href="/admin_slideshow/edit/">New Slideshow</a></fieldset>';
			if($rest[6] == 1) {
				$menu .= '<fieldset><legend><img src="/images/admin/form_input_textarea.png" width="30" height="30" alt="icon" /> Forms</legend><a href="/admin_forms_index/">Listing</a>
							<a href="/admin_forms_reports/reports/">Reports</a>
							<a href="/admin_forms_reports/">Form Submissions</a>
							</fieldset>';
			}
//			if($rest[13] == 1) $menu .= '<fieldset><legend><img src="/images/admin/acces_file.png" width="30" height="30" alt="icon" /> Docs</legend><a href="/admin_documents/">Listing</a>
//						<a href="/admin_documents/edit/">New Document</a></fieldset>';
			if($rest[21] == 1) $menu .= '<fieldset><legend>Newsletter</legend><a href="/admin_newsletter/">Listing</a></fieldset>';
			if($rest[8] == 1) $menu .= '<fieldset><legend><img src="/images/admin/needleleftyellow.png" width="30" height="30" alt="icon" /> Locations</legend><a href="/admin_locations/">Listing</a> <a href="/admin_locations/edit/">New Location</a></fieldset>';
			if($rest[16] == 1) $menu .= '<fieldset><legend><img src="/images/admin/shopping_trolley.png" width="30" height="30" alt="cart" /> Shop</legend>
										<a href="/admin_shop_categories/">Categories</a>
										<a href="/admin_shop_items/">Items</a>
										<a href="/admin_shop_items/feed/">Generate Items Feed</a>
										<a href="/admin_shop_customers/">Customers</a>
										<a href="/admin_shop_coupons/">Coupons</a>
										<a href="/admin_shop_orders/">Orders</a>
										</fieldset>';
if($rest[20] == 1) $menu .= '<fieldset><legend>RSS</legend><a href="/admin_rss/">RSS</a>
						<a href="/admin_rss/edit/">New RSS Feed</a></fieldset>';
			$menu .= '<div class="clear"></div>
			</div>';

		}

		echo $menu;
	}
	  
}