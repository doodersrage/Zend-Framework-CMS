<?php

class SitemapController extends Zend_Controller_Action
{
    protected $lnkList = array();

    public function init()
    {
        /* Initialize action controller here */
 		$this->view->headTitle(Zend_Registry::get('Default Page Title'));
		$this->view->headTitle()->prepend('Sitemap');

		if(Zend_Registry::get('mobile') == false){
			$this->view->minifyHeadLink()->appendStylesheet('/js/fancybox/jquery.fancybox-1.3.4.css');
			$this->view->minifyHeadScript()->appendFile('/js/fancybox/jquery.fancybox-1.3.4.pack.js');
			$this->view->minifyHeadLink()->appendStylesheet('/css/content.css');
		}
    }
	
	// build menuing system
	function buildList($id = 0,$parent = ''){
		
		$routes = new Application_Model_Routes;
		$routesMapper = new Application_Model_RoutesMapper;
		
		$db = Zend_Registry::get('db');
		$select = $db->select()
					->from('pages')
					->where('parent_id = ?',$id)
					->where('menu = ?',0)
					->order('modified DESC')
					->order('pgsort DESC')
					->order('title ASC');
		$results = $db->fetchAll($select);
		
		foreach($results as $id => $item){
			$title = strtolower($item[title]);
			$copy_text = strtolower($item[copy_text]);
			if($item[filelnk] != ''){
					$lnkFnl = $item[filelnk];
					$item[pglink] = $lnkFnl;
					$this->lnkList[] = $item;
			} else {
					if($item[route_id]){
						$routesMapper->find($item[route_id],$routes);
						$lnkFnl = 'http://www.ynotpizza.com/'.$routes->getUri().'/';
					}elseif($item[link_name] == '' || $item[link_name] == NULL){
						$lnkFnl = 'http://www.ynotpizza.com/content/?id='.$item[id].'/';
					} else {
						$lnkFnl = 'http://www.ynotpizza.com/content/'.$item[link_name].'/';
					}
					$item[pglink] = $lnkFnl;
					$this->lnkList[] = $item;
			}
			$this->buildList($item[id],$newLnk);
		}
	}

    public function indexAction()
    {
		
		if(Zend_Registry::get('mobile') == true){
			$this->_helper->layout->setLayout('mobile');
			$this->_helper->viewRenderer->setRender('mobile');
		}
		// list all available vehicles with paginator
		$db = Zend_Registry::get('db');
		
		// first search vehicles then search pages
		// gather listing
		$select = $db->select()
					->from('inventory');
		$select->order('stockNum ASC');
		
		$results = $db->fetchAll($select);
		$this->view->vehicles = $results;
		
		// gather pages
		// gather listing
		$this->buildList();
		$results = $this->lnkList;
		$this->view->pages = $results;
		
		$select = $db->select()
					->from('press_releases')
					->order('date DESC');
		
		$results = $db->fetchAll($select);
		$this->view->press_releases = $results;
		
		$select = $db->select()
			->from('news_item')
			->order('modified DESC');
		
		$results = $db->fetchAll($select);
		$this->view->news = $results;
		
		$select = $db->select()
					->from('events_cal')
					->order('modified DESC');
		
		$results = $db->fetchAll($select);
		$this->view->events = $results;

		$select = $db->select()
			->from('partners')
			->order('modified DESC');
		
		$results = $db->fetchAll($select);
		$this->view->partners = $results;

		$select = $db->select()
			->from('reference_materials')
			->order('modified DESC');
		
		$results = $db->fetchAll($select);
		$this->view->reference_materials = $results;


	}

}