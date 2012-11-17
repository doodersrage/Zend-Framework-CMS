<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
		/* Initialize action controller here */
//		$front = Zend_Controller_Front::getInstance();
//		$front->setParam('disableOutputBuffering', true);
		//$this->_helper->cache(array('index','page'), array('indexaction'));
		$this->getResponse()->setHeader('Expires', '', true);
		$this->getResponse()->setHeader('Cache-Control', 'public', true);
		$this->getResponse()->setHeader('Cache-Control', 'max-age=3800');
		$this->getResponse()->setHeader('Pragma', '', true);
		$this->view->headTitle(Zend_Registry::get('Default Page Title'));
		$this->view->headMeta()->appendName('description', Zend_Registry::get('Index Description Meta'));	
		$this->view->headMeta()->appendName('keywords', Zend_Registry::get('Index Keyword Meta'));
				
    }

    public function indexAction()
    {
		if(Zend_Registry::get('mobile') == false){
			//$this->view->headScript()->appendFile('/js/cloud-carousel.1.0.5.min.js');
			$this->view->minifyHeadLink()->appendStylesheet('/js/fancybox/jquery.fancybox-1.3.4.css');
			$this->view->minifyHeadLink()->appendStylesheet('/css/index.css');
			$this->view->minifyHeadLink()->appendStylesheet('/css/playlist-man.css');
			$this->view->minifyHeadScript()->appendFile('/js/fancybox/jquery.fancybox-1.3.4.pack.js');
			$this->view->minifyHeadScript()->appendFile('/js/jquery.jcarousel.min.js');
			$this->view->headScript()->appendFile('/js/index.js');
			$this->view->headScript()->appendFile('/js/playlist-man.js');
			$this->view->minifyHeadScript()->appendFile('/js/nivo-slider/jquery.nivo.slider.pack.js');
			$this->view->minifyHeadLink()->appendStylesheet('/js/nivo-slider/nivo-slider.css');
			// load and display home page video playlist
			$playlistRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('PlaylistManual');
			$this->view->videoPlylst = $playlistRenderer->direct(1);
		
			$cache = Zend_Cache::factory
			(
				'Core' // frontend caching method
				, 'File'
				, array('lifetime'=>3600,'automatic_serialization' => TRUE)
				, array('cache_dir'=>$_SERVER['DOCUMENT_ROOT'] . '/cache')
			);
			
			$db = Zend_Registry::get('db');
			
			// gather events for neighborhood community block
			$cache_id = 'neighborhoodcommunityblock';
//			if(!$neighborEvents = $cache->load($cache_id)){
				$dateLimit = strtotime('+ 7 days');
				$select = $db->select()
							->from('events_cal');
				$select->where('image != NULL');
				$select->orWhere('image != \'\'');
				$select->order('start DESC');
				$select->order('finish DESC');
				//$select->having('start <= ?',(int)$dateLimit);
				$select->having('neighborhood = 1');
				$select->limit('2');
				$neighborEvents = $db->fetchAll($select);
				
				//$cache->save($neighborEvents, $cache_id);
//			}
			//echo $dateLimit;
			//$eventsArr = array();
			foreach($neighborEvents as $id => $events){
			
			// grab dynamic route if assigned
			$routes = new Application_Model_Routes;
			$routesMapper = new Application_Model_RoutesMapper;
			
				if($events[route_id]){
					$routesMapper->find($events[route_id],$routes);
					if($routes->getUri()){
						$lnkFnl = '/'.$routes->getUri().'/';
					} else {
						$lnkFnl = '/event/name/'.strtolower(preg_replace("/[^A-Za-z0-9\s\s+]/","-",$events[name])).'/'.$events[id].'/';
					}
				} else {
					$lnkFnl = '/event/name/'.strtolower(preg_replace("/[^A-Za-z0-9\s\s+]/","-",$events[name])).'/'.$events[id].'/';
				}
	
				$eventsArr[] = '<div class="communityItem"><a href="'.$lnkFnl.'"><img src="/image/?image=/upload/events/'.urlencode($events[image]).'&amp;width=208&amp;height=145"  alt="'.htmlentities($events[name]).'" /></a></div>';
			}
			$eventsFin = implode('<div class="communityDiv"></div>',$eventsArr);
			
			$this->view->neighborEvents = $eventsFin;
			
			// ten images for carousel
			$imglist='';
			//$img_folder is the variable that holds the path to the banner images. Mine is images/tutorials/
			// see that you don't forget about the "/" at the end 
			$img_folder = APP_BASE_PATH."/images/layout/slideshow/";
		  		  
			//use the directory class
			$imgs = dir($img_folder);
		  
			//read all files from the  directory, checks if are images and ads them to a list (see below how to display flash banners)
			while ($file = $imgs->read()) {
			if (preg_match("/gif/", $file) || preg_match("/jpg/", $file) || preg_match("/png/", $file))
			 $imglist .= "$file ";
			} closedir($imgs->handle);
		  
			//put all images into an array
			$imglist = explode(" ", $imglist);
			shuffle($imglist);
			
			$imgCnt = 0;
			$newImglist = array();
			foreach($imglist as $curImage){
				if($imgCnt < 10) $newImglist[] = $curImage;
				$imgCnt++;
			}

			$this->view->tenItems = $newImglist;
						
			// gather specials locations info
			$cache_id = 'homelocations';
			//if(!$locations = $cache->load($cache_id)){
				$select = $db->select()
							->from('locations');
				$locations = $db->fetchAll($select);
				$cache->save($locations, $cache_id);
			//}
			$this->view->locations = $locations;
			
		// retailer news feed
		$blogRSS = 'http://www.ynotpizza.com/blog/feed/';
        if($this->_helper->urlcheck($blogRSS) == 200){
			$rssArr = $this->_helper->rss($blogRSS);
			
			$curBlock = 1;
			$curItem = 0;
			$maxBlocks = 5;
			
			$announce_item = array();
			foreach($rssArr['entries'] as $newsItem){
					
					$announce_item[] = '<div class="announcementItemBlock">
											<div class="announcementItemTitle"> <a target="_blank" href="'.$newsItem['link'].'">'.substr($newsItem['title'],0,150).'</a></div>
											<div class="announcementDesc"> '.substr(strip_tags($newsItem['description']),0,200).'... <a target="_blank" href="'.$newsItem['link'].'">Read More!</a></div>
										</div>';
					if($curItem == $itemsPerBlock) {
						$curBlock++;
					}
					if($curBlock > $maxBlocks) break;
				}
				$announcements = implode('<hr/>',$announce_item);
			} else {
					$announcements = '<div class="announcementItemBlock">
										<div class="announcementItemTitle"> <a target="_blank" href="javascript:;">Unable to connect to feed!</a></div>
										<div class="announcementDesc">We were not able to connect to the provided feed.</div>
									  </div>';
			}
			
			$this->view->announcements = $announcements;

		} else {
			$this->view->minifyHeadLink()->appendStylesheet('/css/index-mobile.css');
			$this->_helper->layout->setLayout('mobile');
			$this->_helper->viewRenderer->setRender('mobile');
		}
	}

}

