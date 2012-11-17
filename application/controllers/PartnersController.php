<?php

class PartnersController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
		$this->view->headTitle(Zend_Registry::get('Default Page Title'));
		$this->view->headTitle()->prepend('Partners');
    }

    public function indexAction()
    {
		
		// build parent drop down
		$db = Zend_Registry::get('db');
		$select = $db->select()
					->from('partners')
					->order('name ASC');
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
					->from('partners')
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
					->from('partners')
					->order('modified DESC');
		
		$results = $db->fetchAll($select);
		
		$feed = new Zend_Feed_Writer_Feed;
		$feed->setTitle('YNot Pizza & Italian Cuisine Partners');
		$feed->setLink('http://www.ynotpizza.com/');
		$feed->setFeedLink('http://www.ynotpizza.com/partners/rss/', 'atom');
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
			if($item[link] != ''){
				$pgLnk = str_replace(' ','+',$item[link]);
			} else {
				$pgLnk = 'http://www.ynotpizza.com/partners/partner/'.strtolower(preg_replace("/[^A-Za-z0-9\s\s+]/","-",$item[name])).'/'.$item[id].'/';
			}
			echo $pgLnk;
			$entry = $feed->createEntry();
			$entry->setTitle($item[name]);
			$entry->setLink($pgLnk);
			$entry->addAuthor(array(
				'name'  => 'YNot Pizza & Italian Cuisine',
				'email' => 'info@ynotpizza.com',
				'uri'   => 'http://www.ynotpizza.com/',
			));
			$entry->setDateModified(strtotime($item[modified]));
			$entry->setDateCreated(strtotime($item[modified]));
			$entry->setContent(
				$item[description]
			);
			$feed->addEntry($entry);
		}
		 
		/**
		* Render the resulting feed to Atom 1.0 and assign to $out.
		* You can substitute "atom" with "rss" to generate an RSS 2.0 feed.
		*/
		$this->view->out = $feed->export('atom');	
	}

    public function partnerAction()
    {
        $request_val = str_replace('-',' ',$this->_getParam('partner_name'));
        $request_id = str_replace('-',' ',$this->_getParam('id'));
		if(!empty($request_val)){
			$partners = new Application_Model_Partners;
			$partnersMapper = new Application_Model_PartnersMapper;
			$partnersMapper->find($request_id, $partners);
			// print error if page is not found
			if($partners->getId() == ''){
				$this->view->headTitle()->prepend('Partner Not Found');
				$this->view->copy_text = '<div class="errorPageMess"><p>Please check to see if the page has moved!</p></div>';
			// if page is found gather copy
			} else {
				$this->view->bc = '<a href="/partners/partner/'.$this->_getParam('partner_name').'/'.$this->_getParam('id').'/">'.$partners->getName().'</a>';
				
				$this->view->title = $partners->getName();
				$this->view->image = $partners->getImage();
				$this->view->copy_text = str_replace(array('<div class="formpad">&nbsp;</div>','<div class="clear">&nbsp;</div>'),array('<div class="formpad"></div>','<div class="clear"></div>'),$partners->getDescription());
				$this->view->headTitle()->prepend($partners->getName());
				$this->view->headMeta()->appendName('description', $partners->getName());	
				$this->view->headMeta()->appendName('keywords', $partners->getName());
			}
		}
		
    }

}

