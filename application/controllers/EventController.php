<?php

class EventController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
		$this->view->headTitle()->prepend('Events');
		if(Zend_Registry::get('mobile') == false){
			$this->view->minifyHeadLink()->appendStylesheet('/css/content.css');
			$this->view->minifyHeadLink()->appendStylesheet('/js/fancybox/jquery.fancybox-1.3.4.css');
			$this->view->minifyHeadScript()->appendFile('/js/fancybox/jquery.fancybox-1.3.4.pack.js');
		}
    }

    public function indexAction()
    {
		if(Zend_Registry::get('mobile') == true){
			$this->_helper->layout->setLayout('mobile');
			$this->_helper->viewRenderer->setRender('mobile');
		}
		
		// build parent drop down
		$db = Zend_Registry::get('db');
		$select = $db->select()
					->from('events_cal')
					->order('finish DESC');
		$results = $db->fetchAll($select);
		
		$this->_helper->calendar();
		
		if(isset($results)) {
			$paginator = Zend_Paginator::factory($results);
			$paginator->setItemCountPerPage(10);
			$paginator->setCurrentPageNumber($this->_getParam('page'));
			$this->view->paginator = $paginator;
 
			Zend_Paginator::setDefaultScrollingStyle('Sliding');
			Zend_View_Helper_PaginationControl::setDefaultViewPartial(
				'admin/user-paginator.phtml'
			);
		}
		
    }
	
	public function xmlAction(){
        $this->_helper->layout->setLayout('xml');
		// list all available vehicles with paginator
		$db = Zend_Registry::get('db');
				
		// gather pages
		// gather listing
		$select = $db->select()
					->from('events_cal')
					->order('modified DESC');
		
		$results = $db->fetchAll($select);
		$this->view->pages = $results;
	}
	
	function cleanDups($val){
		if(strpos($val,'--')){
			$val = str_replace('--','-',$val);
		}
		if(strpos($val,'--')){
			$val = $this->cleanDups($val);
		}
	return $val;
	}
	
	public function rssAction(){
        $this->_helper->layout->setLayout('rss');
		// list all available vehicles with paginator
		$db = Zend_Registry::get('db');
				
		// gather pages
		// gather listing
		$select = $db->select()
					->from('events_cal')
					->order('finish DESC');
		
		$results = $db->fetchAll($select);
		
		$feed = new Zend_Feed_Writer_Feed;
		$feed->setTitle(Zend_Registry::get('Site Name'));
		$feed->setLink('http://www.ynotpizza.com/');
		$feed->setFeedLink('http://www.ynotpizza.com/events/rss/', 'atom');
		$feed->addAuthor(array(
			'name'  => Zend_Registry::get('Site Name'),
			'email' => Zend_Registry::get('Contact Email'),
			'uri'   => 'http://www.ynotpizza.com/',
		));
		$feed->setDateModified(time());
		$feed->addHub('http://pubsubhubbub.appspot.com/');
		 
		/**
		* Add one or more entries. Note that entries must
		* be manually added once created.
		*/
		foreach($results as $item){
			echo $pgLnk;
			$entry = $feed->createEntry();
			$entry->setTitle($item[name]);
			
			// grab dynamic route if assigned
			$routes = new Application_Model_Routes;
			$routesMapper = new Application_Model_RoutesMapper;
		
			if($item[route_id]){
				$routesMapper->find($item[route_id],$routes);
				if($routes->getUri()){
					$lnkFnl = '/'.$routes->getUri().'/';
				} else {
					$lnkFnl = '/event/name/'.$this->cleanDups(strtolower(preg_replace("/[^A-Za-z0-9\s\s+]/","-",$item[name]))).'/'.$item[id].'/';
				}
			} else {
				$lnkFnl = '/event/name/'.$this->cleanDups(strtolower(preg_replace("/[^A-Za-z0-9\s\s+]/","-",$item[name]))).'/'.$item[id].'/';
			}

			$entry->setLink('http://www.ynotpizza.com'.$lnkFnl);
			$entry->addAuthor(array(
			'name'  => Zend_Registry::get('Site Name'),
			'email' => Zend_Registry::get('Contact Email'),
			'uri'   => 'http://www.ynotpizza.com/',
			));
			$dateSet = strtotime($item[modified]);
			if($dateSet == -62169958800) $dateSet = time();
			$entry->setDateModified($dateSet);
			$entry->setDateCreated($dateSet);
			$entry->setContent(
				str_replace(array('&nbsp;','&copy;','&rsquo;','&ldquo;','&rdquo;','&ndash','&eacute;','&mdash;','&reg;','&bull;','&cent'),'',$item[description])
			);
			$feed->addEntry($entry);
		}
		 
		/**
		* Render the resulting feed to Atom 1.0 and assign to $out.
		* You can substitute "atom" with "rss" to generate an RSS 2.0 feed.
		*/
		$this->view->out = $feed->export('atom');	
	}

    public function nameAction()
    {
		if(Zend_Registry::get('mobile') == true){
			$this->_helper->layout->setLayout('mobile');
			$this->_helper->viewRenderer->setRender('mobile-name');
		}
		
        $request_val = str_replace('-',' ',$this->_getParam('event_name'));
        $request_id = $this->_getParam('id');
		if(!empty($request_id)){
			$eventscal = new Application_Model_EventsCal;
			$eventscalMapper = new Application_Model_EventsCalMapper;
			$eventscalMapper->find($request_id, $eventscal);
			// if request val is set check for dynamic route then redirect if found
			if($request_val){
				$routes = new Application_Model_Routes;
				$routesMapper = new Application_Model_RoutesMapper;
			
				if($eventscal->getRoute_id()){
					$routesMapper->find($eventscal->getRoute_id(),$routes);
					if($routes->getUri()){
						$lnkFnl = '/'.$routes->getUri().'/';
						$this->_redirect($lnkFnl, array('code'=>301));
					}
				}
			}
			// print error if page is not found
			$this->_helper->calendar();
			if($eventscal->getId() == ''){
				$this->view->headTitle()->prepend('Event Not Found');
				$this->view->copy_text = '<div class="errorPageMess"><p>Please check to see if the page has moved!</p></div>';
			// if page is found gather copy
			} else {
				
				// grab dynamic route if assigned
				$routes = new Application_Model_Routes;
				$routesMapper = new Application_Model_RoutesMapper;
			
				if($eventscal->getRoute_id()){
					$routesMapper->find($eventscal->getRoute_id(),$routes);
					if($routes->getUri()){
						$lnkFnl = '/'.$routes->getUri().'/';
					} else {
						$lnkFnl = '/event/name/'.$this->cleanDups(strtolower(preg_replace("/[^A-Za-z0-9\s\s+]/","-",$eventscal->getName()))).'/'.$this->_getParam('id').'/';
					}
				} else {
					$lnkFnl = '/event/name/'.$this->cleanDups(strtolower(preg_replace("/[^A-Za-z0-9\s\s+]/","-",$eventscal->getName()))).'/'.$this->_getParam('id').'/';
				}
					
				$this->view->bc = '<a href="'.$lnkFnl.'">'.$eventscal->getName().'</a>';
				
				$this->view->title = $eventscal->getName();
				$this->view->image = $eventscal->getImage();
				$this->view->start = date("F j, Y",$eventscal->getStart());
				$this->view->finish = date("F j, Y",$eventscal->getFinish());
				$this->view->copy_text = str_replace(array('<div class="formpad">&nbsp;</div>','<div class="clear">&nbsp;</div>'),array('<div class="formpad"></div>','<div class="clear"></div>'),$eventscal->getDescription());
				$this->view->headTitle()->prepend($eventscal->getName());
				$this->view->headMeta()->appendName('description', $eventscal->getName());	
				$this->view->headMeta()->appendName('keywords', $eventscal->getName());
			}
		}
		
    }

}

