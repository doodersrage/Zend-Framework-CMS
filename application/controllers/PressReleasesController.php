<?php

class PressReleasesController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
		$this->view->headTitle(Zend_Registry::get('Default Page Title'));
		$this->view->headTitle()->prepend('Press Releases');
    }

    public function indexAction()
    {
		
		$db = Zend_Registry::get('db');
		$select = $db->select()
					->from('press_releases')
					->order('date DESC');
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
					->from('press_releases')
					->order('date DESC');
		
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
					->from('press_releases')
					->order('date DESC');
		
		$results = $db->fetchAll($select);
		
		$feed = new Zend_Feed_Writer_Feed;
		$feed->setTitle('YNot Pizza & Italian Cuisine Press Releases');
		$feed->setLink('http://www.ynotpizza.com/');
		$feed->setFeedLink('http://www.ynotpizza.com/press-releases/rss/', 'atom');
		$feed->addAuthor(array(
			'name'  => 'YNot Pizza & Italian Cuisine',
			'email' => 'info@ynotpizza.com',
			'uri'   => 'http://www.ynotpizza.com/',
		));
		$feed->setDateModified(time());
		$feed->addHub('http://pubsubhubbub.appspot.com/');
		 
		/**
		* Add one or more entries. Note that entries must
		* be manually added once created.
		*/
		foreach($results as $item){
			if($item[filelnk] != ''){
				$pgLnk = str_replace(' ','+',$item[filelnk]);
			} else {
				$pgLnk = 'http://www.ynotpizza.com/press-releases/item/'.strtolower(preg_replace("/[^A-Za-z0-9\s\s+]/","-",$item[title])).'/'.$item[id].'/';
			}
			echo $pgLnk;
			$entry = $feed->createEntry();
			$entry->setTitle($item[title]);
			$entry->setLink($pgLnk);
			$entry->addAuthor(array(
				'name'  => 'YNot Pizza & Italian Cuisine',
				'email' => 'info@ynotpizza.com',
				'uri'   => 'http://www.ynotpizza.com/',
			));
			$entry->setDateModified(strtotime($item[modified]));
			$entry->setDateCreated(strtotime($item[modified]));
			$entry->setContent(
				$item[copy_text]
			);
			$feed->addEntry($entry);
		}
		 
		/**
		* Render the resulting feed to Atom 1.0 and assign to $out.
		* You can substitute "atom" with "rss" to generate an RSS 2.0 feed.
		*/
		$this->view->out = $feed->export('atom');	
	}

    public function itemAction()
    {
        $request_val = str_replace('-',' ',$this->_getParam('item_name'));
        $request_id = $this->_getParam('id');
		if(!empty($request_val)){
			$pressreleases = new Application_Model_PressReleases;
			$pressreleasesMapper = new Application_Model_PressReleasesMapper;
			$pressreleasesMapper->find($request_id, $pressreleases);
			// print error if page is not found
			if($pressreleases->getId() == ''){
				$this->view->headTitle()->prepend('Press Release Not Found');
				$this->view->copy_text = '<div class="errorPageMess"><p>Please check to see if the page has moved!</p></div>';
			// if page is found gather copy
			} else {
				$this->view->bc = '<a href="/press-releases/item/'.$this->_getParam('item_name').'/'.$this->_getParam('id').'/">'.$pressreleases->getTitle().'</a>';
				$this->view->title = $pressreleases->getTitle();
				$this->view->date = $pressreleases->getDate();
				$this->view->copy_text = str_replace(array('<div class="formpad">&nbsp;</div>','<div class="clear">&nbsp;</div>'),array('<div class="formpad"></div>','<div class="clear"></div>'),$pressreleases->getCopy_text());
				$this->view->headTitle()->prepend($pressreleases->getTitle());
				$this->view->headMeta()->appendName('description', $pressreleases->getTitle());	
				$this->view->headMeta()->appendName('keywords', $pressreleases->getTitle());
			}
		}
		
    }

}

