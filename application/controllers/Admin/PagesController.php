<?php

class Admin_PagesController extends Zend_Controller_Action
{
	private $rest;
	
    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->layout->setLayout('admin');
		$this->view->headTitle()->prepend('Admin');
		
		$storage = new Zend_Auth_Storage_Session();
        $data = $storage->read();
        if(empty($data)){
			$controller = $this->getRequest()->getControllerName();
			$action = $this->getRequest()->getActionName();
			if($action != 'login'){
				$this->_helper->redirector('index','admin_login');
			}
        } else {
			$usersGroups = new Application_Model_UsersGroups;
			$usersGroupsMapper = new Application_Model_UsersGroupsMapper;
			
			$usersGroupsMapper->find($data->group, $usersGroups);
			
			$rest = unserialize($usersGroups->getRestrictions());
			$this->rest = $rest;
			
		}
		
		$this->view->headScript()->appendFile('/js/admin-pages.js');
		
    }
	
	// bof pages section actions
	// rem sub-pages
	private function delSubs($id){
		$db = Zend_Registry::get('db');
		$select = $db->select()
					->from('pages')
					->where('parent_id = ?',$id);
		$results = $db->fetchAll($select);
		if($results){
			$db->delete('pages',array(
				'id = ?' => $results[id],
			));
			$db->delete('routes',array(
				'type = ?' => 'content',
				'seg_id = ?' => $results[id],
			));
			// recurse through possible child items
			$this->delSubs($results[id]);
		}
	}
	
    public function indexAction()
    {
		if($this->rest[4] == 1){
			$this->view->headTitle()->prepend('Pages');
			$pages = new Application_Model_Pages;
			$pagesMapper = new Application_Model_PagesMapper;
			$db = Zend_Registry::get('db');
			
			// check for delete action
			if($this->_request->getPost('delete')){
				if(is_array($this->_request->getPost('delete'))){
					foreach($this->_request->getPost('delete') as $curDel){
						$pagesMapper->delete($curDel,$pages);
						// clear out remaining sub-pages if any
						$this->delSubs($curDel);
					}
				}
			}
			
			// build parent drop down
			$select = $db->select()
						->from('pages')
						->where('parent_id = ?',0)
						->order('pgsort DESC')
						->order('title ASC');
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
    }
	
    public function pagesExpAction()
    {
		if($this->rest[4] == 1){			
			$this->_helper->layout()->disableLayout();
			// build parent drop down
			$db = Zend_Registry::get('db');
			$select = $db->select()
						->from('pages')
						->where('parent_id = ?',$this->_getParam('pid'))
						->order('pgsort DESC')
						->order('title ASC');
			$results = $db->fetchAll($select);
			$this->view->paginator = $results;
		}
    }
	
	private function childPages($list,$id,$space = '-'){
		// build parent drop down
		$db = Zend_Registry::get('db');
		$select = $db->select()
					->from('pages')
					->where('parent_id = ?',$id)
					->order('pgsort DESC')
					->order('title ASC');
		$results = $db->fetchAll($select);
		foreach($results as $cur){
			$list[$cur[id]] = $space.$cur[title];
			$list = $this->childPages($list,$cur[id],$space.'-');
		}
	return $list;
	}
	
    public function editAction()
    {
		if($this->rest[4] == 1){
			$this->view->headTitle()->prepend('Pages Edit');
			$form = new Zend_Form;
		 
			$pages = new Application_Model_Pages;
			$pagesMapper = new Application_Model_PagesMapper;
			
			// build parent drop down
			$db = Zend_Registry::get('db');
			$select = $db->select()
						->from('pages');
			$select->where('parent_id = ?',0);
			$results = $db->fetchAll($select);
			$ddVals = array();
			$ddVals[0] = '';
			foreach($results as $cur){
				$ddVals[$cur[id]] = $cur[title];
				$ddVals = $this->childPages($ddVals,$cur[id],'-');
			}
			$this->view->pagesdd = $ddVals;
			
			// build file link drop down
			$db = Zend_Registry::get('db');
			$select = $db->select()
						->from('docs')
						->order('filename ASC');
			$results = $db->fetchAll($select);
			$this->view->docs = $results;

			// gather forms data
			$forms = new Application_Model_Forms;
			$formsMapper = new Application_Model_FormsMapper;
			$formsOp = $formsMapper->fetchAll();
			$this->view->forms = $formsOp;
			
			// gather playlist data
			$playlist = new Application_Model_Playlist;
			$playlistMapper = new Application_Model_PlaylistMapper;
			$PlaylisLst = $playlistMapper->fetchAll();
			$this->view->playlists = $PlaylisLst;
			
			// gather slideshow data
			$slideshow = new Application_Model_Slideshow;
			$slideshowMapper = new Application_Model_SlideshowMapper;
			$slideShw = $slideshowMapper->fetchAll();
			$this->view->slideshows = $slideShw;
			
			// required fields
			$required = array();
			$required[] = 'title';
			//$required[] = 'pgsort';
			//$required[] = 'copy_text';
			
			// check for empty fields
			$errors = 0;
			if($_POST){
				foreach($required as $sel){
					if(empty($_POST[$sel])){
						$errors++;
					}
				}
			}
			
			// if post data is valid upload and insert selected spreaksheet
			if ($_POST && $errors == 0) {
				
				$menu = ($this->_request->getPost('menu') == '' ? 0 : $this->_request->getPost('menu'));
				
				// gather post link name
				if($this->_request->getPost('id')){
					$pagesMapper->find($this->getRequest()->getParam('id'),$pages);
					$uri = $pages->getLink_name();
				} else {
					$uri = $this->_request->getPost('link_name');
				}

				$pages->setTitle($this->_request->getPost('title'));
				$pages->setFilelnk($this->_request->getPost('filelnk'));
				$pages->setCopy_text($this->_request->getPost('copy_text'));
				$pages->setMobile_text($this->_request->getPost('mobile_text'));
				$pages->setSeo_text($this->_request->getPost('seo_text'));
				$pages->setParent_id($this->_request->getPost('parent_id'));
				$pages->setPlaylist($this->_request->getPost('playlist'));
				$pages->setSlideshow($this->_request->getPost('slideshow'));
				$pages->setForm_id($this->_request->getPost('form_id'));
				$pages->setRoute_id($this->_request->getPost('route_id'));
				$pages->setLink_name($uri);
				$pages->setTitle_tag($this->_request->getPost('title_tag'));
				$pages->setDesc_tag($this->_request->getPost('desc_tag'));
				$pages->setKeyword_tag($this->_request->getPost('keyword_tag'));
				$pages->setPgsort($this->_request->getPost('pgsort'));
				$pages->setCalendar($this->_request->getPost('calendar'));
				$pages->setMenu($menu);
				$pages->setNew_window($this->_request->getPost('new_window'));
				$pages->setId($this->_request->getPost('id'));
				$pagesMapper->save($pages);
			
				// update/add dynamic route
				$routes = new Application_Model_Routes;
				$routesMapper = new Application_Model_RoutesMapper;
				$redirects = new Application_Model_Redirects;
				$redirectsMapper = new Application_Model_RedirectsMapper;
				
				if($this->_request->getPost('route_id')){
					// load existing route data
					$routesMapper->find($this->_request->getPost('route_id'),$routes);
					
					// create redirect for old route
					if(($this->_request->getPost('link_name') != $routes->getUri()) && ($this->_request->getPost('link_name') != '')){
						$redirects->setOlduri('/'.$routes->getUri().'/');
						$redirects->setNewuri('/'.$this->_request->getPost('link_name').'/');
						$redirectsMapper->save($redirects);
					}
					
					// save new route
					$routes->setUri($this->_request->getPost('link_name'));
					$routesMapper->save($routes);
				} else {
					// gather new page id if page id is not set
					if($this->_request->getPost('id')){
						$page_id = $this->_request->getPost('id');
					} else {
						$page_id = $db->lastInsertId();
					}
					
					// save new route
					$routes->setType('content');
					$routes->setSeg_id($page_id);
					$routes->setUri($this->_request->getPost('link_name'));
					if($this->_request->getPost('route_id')) $routes->setId($this->_request->getPost('route_id'));
					$routesMapper->save($routes);
					
					// gather new route id then save to page
					if(!$this->_request->getPost('route_id')){
						$select = $db->select()
							->from('routes')
							->order('id DESC')
							->limit('1');
						$results = $db->fetchRow($select);
						
						$newRouteId = $results[id];
						
						$pagesMapper->find($page_id,$pages);
						$pages->setRoute_id($newRouteId);
						$pagesMapper->save($pages);
					}
				}
				
				$this->_helper->redirector('index','admin_pages');
			// if form data is not valid print form
			} else {
				
				if($this->getRequest()->getParam('id') && $form->getValue('id') == ''){
					$pagesMapper->find($this->getRequest()->getParam('id'),$pages);
					
					// gather route information if it exists
					$routes = new Application_Model_Routes;
					$routesMapper = new Application_Model_RoutesMapper;
					if($pages->getRoute_id()){
						$routesMapper->find($pages->getRoute_id(),$routes);
						$link_name = $routes->getUri();
					} else {
						$link_name = $pages->getLink_name();
					}
					
					$data = array(
								'title'=>$pages->getTitle(),
								'filelnk'=>$pages->getFilelnk(),
								'copy_text'=>$pages->getCopy_text(),
								'mobile_text'=>$pages->getMobile_text(),
								'seo_text'=>$pages->getSeo_text(),
								'parent_id'=>$pages->getParent_id(),
								'playlist'=>$pages->getPlaylist(),
								'slideshow'=>$pages->getSlideshow(),
								'form_id'=>$pages->getForm_id(),
								'route_id'=>$pages->getRoute_id(),
								'link_name'=>$link_name,
								'title_tag'=>$pages->getTitle_tag(),
								'desc_tag'=>$pages->getDesc_tag(),
								'keyword_tag'=>$pages->getKeyword_tag(),
								'pgsort'=>$pages->getPgsort(),
								'calendar'=>$pages->getCalendar(),
								'menu'=>$pages->getMenu(),
								'new_window'=>$pages->getNew_window(),
								'id'=>$pages->getId(),
								);
		
					$this->view->pages = $data;
				}
				
			}
		}
	}
	
	// eof pages section actions
	
}

