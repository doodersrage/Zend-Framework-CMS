<?php

class NewsController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
		$this->view->headTitle(Zend_Registry::get('Default Page Title'));
		$this->view->headTitle()->prepend('News');
		if(Zend_Registry::get('mobile') == false){
			$this->view->minifyHeadLink()->appendStylesheet('/css/content.css');
			$this->view->minifyHeadLink()->appendStylesheet('/css/news.css');
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
					->from('news_category')
					->order('name DESC');
		$results = $db->fetchAll($select);
		
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
					->from('news_item')
					->order('modified DESC');
		
		$results = $db->fetchAll($select);
		$this->view->pages = $results;
	}
	
	public function rssAction(){
        $this->_helper->layout->setLayout('rss');
		// list all available vehicles with paginator
		$db = Zend_Registry::get('db');
				
		// gather pages
		// gather listing
		$select = $db->select()
					->from('news_item')
					->order('modified DESC');
		
		$results = $db->fetchAll($select);
		
		$feed = new Zend_Feed_Writer_Feed;
		$feed->setTitle(Zend_Registry::get('Site Name'));
		$feed->setLink('http://www.ynotpizza.com/');
		$feed->setFeedLink('http://www.ynotpizza.com/news/rss/', 'atom');
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
					$lnkFnl = '/news/item/'.$this->cleanDups(strtolower(preg_replace("/[^A-Za-z0-9\s\s+]/","-",$item[name]))).'/'.$item[id].'/';
				}
			} else {
				$lnkFnl = '/news/item/'.$this->cleanDups(strtolower(preg_replace("/[^A-Za-z0-9\s\s+]/","-",$item[name]))).'/'.$item[id].'/';
			}

			$entry->setLink('http://www.ynotpizza.com'.$lnkFnl);
			$entry->addAuthor(array(
			'name'  => Zend_Registry::get('Site Name'),
			'email' => Zend_Registry::get('Contact Email'),
			'uri'   => 'http://www.ynotpizza.com/',
			));
			$entry->setDateModified(strtotime($item[modified]));
			$entry->setDateCreated(strtotime($item[modified]));
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

    public function categoryAction()
    {
 		if(Zend_Registry::get('mobile') == true){
			$this->_helper->layout->setLayout('mobile');
			$this->_helper->viewRenderer->setRender('mobile-category');
		}
       $request_val = str_replace('-',' ',$this->_getParam('category_name'));
        $request_id = str_replace('-',' ',$this->_getParam('id'));
		if(!empty($request_val)){
			$newscategory = new Application_Model_NewsCategory;
			$newscategoryMapper = new Application_Model_NewsCategoryMapper;
			$newscategoryMapper->find($request_id, $newscategory);
			// print error if page is not found
			if($newscategory->getId() == ''){
				$this->view->headTitle()->prepend('News Category Not Found');
				$this->view->copy_text = '<div class="errorPageMess"><p>Please check to see if the page has moved!</p></div>';
			// if page is found gather copy
			} else {
				
				$this->view->bc = '<a href="/news/category/'.$this->cleanDups($this->_getParam('category_name')).'/'.$this->_getParam('id').'/">'.$newscategory->getName().'</a>';
				
				$this->view->title = $newscategory->getName();
				$this->view->copy_text = str_replace(array('<div class="formpad">&nbsp;</div>','<div class="clear">&nbsp;</div>'),array('<div class="formpad"></div>','<div class="clear"></div>'),$newscategory->getDescription());
				$this->view->headTitle()->prepend($newscategory->getName());
				$this->view->headMeta()->appendName('description', $newscategory->getName());	
				$this->view->headMeta()->appendName('keywords', $newscategory->getName());
			}
		}
		
		// build parent drop down
		$db = Zend_Registry::get('db');
		$select = $db->select()
					->from('news_item')
					->where('nc_id = ?',$request_id)
					->order('date_added DESC')
					->order('modified DESC');
		$results = $db->fetchAll($select);
		
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
	
	function cleanDups($val){
		if(strpos($val,'--')){
			$val = str_replace('--','-',$val);
		}
		if(strpos($val,'--')){
			$val = $this->cleanDups($val);
		}
	return $val;
	}

    public function itemAction()
    {
		if(Zend_Registry::get('mobile') == true){
			$this->_helper->layout->setLayout('mobile');
			$this->_helper->viewRenderer->setRender('mobile-item');
		}
        $request_val = str_replace('-',' ',$this->_getParam('item_name'));
        $request_id = str_replace('-',' ',$this->_getParam('id'));
		if(!empty($request_id)){
			$newsitem = new Application_Model_NewsItem;
			$newsitemMapper = new Application_Model_NewsItemMapper;
			$newsitemMapper->find($request_id, $newsitem);
			// if request val is set check for dynamic route then redirect if found
			if($request_val){
				$routes = new Application_Model_Routes;
				$routesMapper = new Application_Model_RoutesMapper;
			
				if($newsitem->getRoute_id()){
					$routesMapper->find($newsitem->getRoute_id(),$routes);
					if($routes->getUri()){
						$lnkFnl = '/'.$routes->getUri().'/';
						$this->_redirect($lnkFnl, array('code'=>301));
					}
				}
			}
			// print error if page is not found
			if($newsitem->getId() == ''){
				$this->view->headTitle()->prepend('News Item Not Found');
				$this->view->copy_text = '<div class="errorPageMess"><p>Please check to see if the page has moved!</p></div>';
			// if page is found gather copy
			} else {
				$newscategory = new Application_Model_NewsCategory;
				$newscategoryMapper = new Application_Model_NewsCategoryMapper;
				$newscategoryMapper->find($newsitem->getNc_id(), $newscategory);
				
				$this->view->bc = '<a href="/news/category/'.$this->cleanDups(strtolower(preg_replace("/[^A-Za-z0-9\s\s+]/","-",$newscategory->getName()))).'/'.$newscategory->id.'/">'.$newscategory->getName().'</a> &raquo; <a href="/news/item/'.$this->cleanDups($this->_getParam('item_name')).'/'.$this->_getParam('id').'/">'.$newsitem->getName().'</a>';
				
				$this->view->title = $newsitem->getName();
				$this->view->copy_text = str_replace(array('<div class="formpad">&nbsp;</div>','<div class="clear">&nbsp;</div>'),array('<div class="formpad"></div>','<div class="clear"></div>'),$newsitem->getDescription());
				$this->view->headTitle()->prepend($newsitem->getName());
				$this->view->headMeta()->appendName('description', $newsitem->getName());	
				$this->view->headMeta()->appendName('keywords', $newsitem->getName());
			}
		}
		
    }

}

