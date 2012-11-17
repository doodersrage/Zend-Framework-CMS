<?php

class Admin_News_Item_IndexController extends Zend_Controller_Action
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
		
    }
		
    public function indexAction()
    {
		$this->view->headTitle()->prepend('News Items');
		$newsitem = new Application_Model_NewsItem;
		$newsitemMapper = new Application_Model_NewsItemMapper;
		$db = Zend_Registry::get('db');
		
		// check for delete action
		if($this->_request->getPost('delete')){
			if(is_array($this->_request->getPost('delete'))){
				foreach($this->_request->getPost('delete') as $curDel){
					$newsitemMapper->delete($curDel,$newsitem);
					$db->delete('routes',array(
						'type = ?' => 'news_item',
						'seg_id = ?' => $curDel,
					));
				}
			}
		}
		
		// build parent drop down
		$db = Zend_Registry::get('db');
		$select = $db->select()
					->from('news_item')
					->where('nc_id = ?',$this->_getParam('cid'))
					->order('date_added DESC');
		$results = $db->fetchAll($select);
		
		if(isset($results)) {
			$paginator = Zend_Paginator::factory($results);
			$paginator->setItemCountPerPage(10);
			$paginator->setCurrentPageNumber($this->_getParam('page'));
			$this->view->paginator = $paginator;
 
			Zend_Paginator::setDefaultScrollingStyle('Sliding');
			Zend_View_Helper_PaginationControl::setDefaultViewPartial(
				'admin/news-paginator.phtml'
			);
		}
    }
	
    public function editAction()
    {
		$this->view->headTitle()->prepend('News Item Edit');
		$db = Zend_Registry::get('db');
		$form = new Zend_Form;
	 
		$newsitem = new Application_Model_NewsItem;
		$newsitemMapper = new Application_Model_NewsItemMapper;
		
		$form->setAction('')
			 ->setMethod('post')
			 ->setAttrib('name', 'inventEdit')
			 ->setAttrib('enctype', 'multipart/form-data');

		$form->addElement('text', 'name', array('required' => true,'label' => 'Name:','size'=>'80'));
		$form->addElement('text', 'link_name', array('label' => 'Link Name: (Used to request selected page. EX: http://www.domain.com/my-page/)','size'=>'100'));
		$form->addElement('text', 'date_added', array('required' => true,'label' => 'Date:','size'=>'80'));
		$form->addElement('text', 'remote_link', array('label' => 'Remote Link:','size'=>'90'));
		
		// add image upload fields
		$element = new Zend_Form_Element_File('uploadnewsimg');
		$element->setLabel('Upload Image:')
				->setDestination(APP_BASE_PATH.'/upload/news');
		// ensure only 1 file
		$element->addValidator('Count', false, 1);
		// limit to 100K
		//$element->addValidator('Size', false, 404800);
		// only JPEG, PNG, and GIFs
		$element->addValidator('Extension', false, 'jpg,psd,gif,png');
		$form->addElement($element, 'uploadnewsimg');

		$form->addElement('hidden', 'currentnewsImg');
				
		$form->addElement('textarea', 'description', array('rows'=>10,'columns'=>20,'required' => true,'label' => 'Description:'));
		
		$form->addElement('hidden', 'route_id');
		$form->addElement('hidden', 'nc_id');
		$form->addElement('hidden', 'id');
		
		$form->addElement('image', 'apply', array('src' => '/images/buttons/apply.png'));
		
		// if post data is valid upload and insert selected spreaksheet
		if ($form->isValid($_POST)) {
			$img = $form->getValue('currentnewsImg');
						
			$ncid = ($this->_getParam('cid') != '' ? $this->_getParam('cid') : $form->getValue('nc_id'));
			
			$newsitem->setName($form->getValue('name'));
			$newsitem->setDate_added(trim($form->getValue('date_added')));
			$newsitem->setImage($img);
			$newsitem->setDescription($form->getValue('description'));
			$newsitem->setRemote_link($form->getValue('remote_link'));
			$newsitem->setRoute_id($form->getValue('route_id'));
			$newsitem->setNc_id($ncid);
			$newsitem->setId($form->getValue('id'));
			$newsitemMapper->save($newsitem);
		
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
				$routes->setType('news_item');
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
					
					$newsitemMapper->find($page_id,$newsitem);
					$newsitem->setRoute_id($newRouteId);
					$newsitemMapper->save($newsitem);
				}
			}
		
			$this->view->form = '<strong>News Item has been updated!</strong>';
			$this->view->ncid = $ncid;
		// if form data is not valid print form
		} else {
			
			if($this->getRequest()->getParam('id') && $form->getValue('id') == ''){
				$newsitemMapper->find($this->getRequest()->getParam('id'),$newsitem);
			
				// gather route information if it exists
				$routes = new Application_Model_Routes;
				$routesMapper = new Application_Model_RoutesMapper;
				if($newsitem->getRoute_id()){
					$routesMapper->find($newsitem->getRoute_id(),$routes);
					$link_name = $routes->getUri();
				} else {
					$link_name = '';
				}

				$data = array(
							'name'=>$newsitem->getName(),
							'date_added'=>date("n/j/Y",$newsitem->getDate_added()),
							'currentnewsImg'=>$newsitem->getImage(),
							'description'=>$newsitem->getDescription(),
							'remote_link'=>$newsitem->getRemote_link(),
							'link_name'=>$link_name,
							'route_id'=>$newsitem->getRoute_id(),
							'nc_id'=>$newsitem->getNc_id(),
							'id'=>$newsitem->getId(),
							);
	
				$form->setDefaults($data);	
				$form->getElement('currentnewsImg')->setLabel('Current: '.$newsitem->getImage());
				
				$this->view->ncid = $newsitem->getNc_id('nc_id');
								
			} else {
				$this->view->ncid = $this->_getParam('cid');
			}
			$this->view->form = $form->render();
			
		}
	}
	
}

