<?php

class Admin_EventsController extends Zend_Controller_Action
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
		$this->view->headTitle()->prepend('Events');
		$events = new Application_Model_EventsCal;
		$eventsMapper = new Application_Model_EventsCalMapper;
		$db = Zend_Registry::get('db');
		
		// check for delete action
		if($this->_request->getPost('delete')){
			if(is_array($this->_request->getPost('delete'))){
				foreach($this->_request->getPost('delete') as $curDel){
					$eventsMapper->delete($curDel,$events);
					$db->delete('routes',array(
						'type = ?' => 'event',
						'seg_id = ?' => $curDel,
					));
				}
			}
		}
		
		// build parent drop down
		$select = $db->select()
					->from('events_cal')
					->order('start DESC');
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
	
    public function editAction()
    {
		$this->view->headTitle()->prepend('Events Edit');
		$db = Zend_Registry::get('db');
		$form = new Zend_Form;
	 
		$events = new Application_Model_EventsCal;
		$eventsMapper = new Application_Model_EventsCalMapper;
		
		$form->setAction('/admin_events/edit/')
			 ->setMethod('post')
			 ->setAttrib('name', 'inventEdit')
			 ->setAttrib('enctype', 'multipart/form-data');

		$form->addElement('text', 'start', array('required' => true,'label' => 'Start:','size'=>'80'));
		$form->addElement('text', 'finish', array('required' => true,'label' => 'Finish:','size'=>'80'));
		$form->addElement('checkbox','neighborhood', array('label' => 'Neighborhood Notes'));
		
		$form->addElement('text', 'name', array('required' => true,'label' => 'Name:','size'=>'80'));
		$form->addElement('text', 'link_name', array('label' => 'Link Name: (Used to request selected page. EX: http://www.domain.com/my-page/)','size'=>'100'));
				
		$form->addElement('textarea', 'short_desc', array('rows'=>2,'columns'=>20,'required' => true,'label' => 'Short Description:'));
		$form->addElement('textarea', 'description', array('rows'=>10,'columns'=>20,'required' => true,'label' => 'Description:'));
		
		// add image upload fields
		$element = new Zend_Form_Element_File('image');
		$element->setLabel('Upload Image:')
				->setDestination(APP_BASE_PATH.'/upload/events');
		// ensure only 1 file
		$element->addValidator('Count', false, 1);
		// only JPEG, PNG, and GIFs
		$element->addValidator('Extension', false, 'jpg,psd,gif,png');
		$form->addElement($element, 'image');
		
		$form->addElement('hidden', 'route_id');
		$form->addElement('hidden', 'currentImage');
		$form->addElement('hidden', 'id');
		
		$form->addElement('image', 'apply', array('src' => '/images/buttons/apply.png'));
		
		// if post data is valid upload and insert selected spreaksheet
		if ($form->isValid($_POST)) {
			
			// manage existing image if exists
			if($form->image->getFileName()){
			  $form->image->receive();
			  $image = $this->cleanFile($form->image->getFileName());
			} else {
			  $image = $form->getValue('currentImage');
			}

			$events->setStart(strtotime(trim($form->getValue('start'))));
			$events->setFinish(strtotime(trim($form->getValue('finish'))));
			$events->setNeighborhood($form->getValue('neighborhood'));
			$events->setName($form->getValue('name'));
			$events->setShort_desc($form->getValue('short_desc'));
			$events->setDescription($form->getValue('description'));
			$events->setImage($image);
			$events->setRoute_id($form->getValue('route_id'));
			$events->setId($form->getValue('id'));
			$eventsMapper->save($events);
			
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
				$routes->setType('event');
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
					
					$eventsMapper->find($page_id,$events);
					$events->setRoute_id($newRouteId);
					$eventsMapper->save($events);
				}
			}
			
			$this->view->form = '<strong>Event has been updated!</strong>';
		// if form data is not valid print form
		} else {
			
			if($this->getRequest()->getParam('id') && $form->getValue('id') == ''){
				$eventsMapper->find($this->getRequest()->getParam('id'),$events);
			
				// gather route information if it exists
				$routes = new Application_Model_Routes;
				$routesMapper = new Application_Model_RoutesMapper;
				if($events->getRoute_id()){
					$routesMapper->find($events->getRoute_id(),$routes);
					$link_name = $routes->getUri();
				} else {
					$link_name = '';
				}
				
				$data = array(
							'start'=>date("n/j/Y",$events->getStart()),
							'finish'=>date("n/j/Y",$events->getFinish()),
							'name'=>$events->getName(),
							'neighborhood'=>$events->getNeighborhood(),
							'short_desc'=>$events->getShort_desc(),
							'description'=>$events->getDescription(),
							'currentImage'=>$events->getImage(),
							'route_id'=>$events->getRoute_id(),
							'link_name'=>$link_name,
							'id'=>$events->getId(),
							);
	
				$form->setDefaults($data);	
				
				$form->getElement('currentImage')->setLabel('Current: '.$events->getImage());			
				
			}
			$this->view->form = $form->render();
			
		}
	}
	
	// cleans uploaded file for file name
	private function cleanFile($file){
		$file = explode('/',$file);
		$pcnt = count($file)-1;
		
	return $file[$pcnt];
	}
	
}

