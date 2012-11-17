<?php

class Admin_LocationsController extends Zend_Controller_Action
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
		$this->view->headTitle()->prepend('Locations');
		$locations = new Application_Model_Locations;
		$locationsMapper = new Application_Model_LocationsMapper;
		
		// check for delete action
		if($this->_request->getPost('delete')){
			if(is_array($this->_request->getPost('delete'))){
				foreach($this->_request->getPost('delete') as $curDel){
					$locationsMapper->delete($curDel,$partners);
				}
			}
		}
		
		// build parent drop down
		$db = Zend_Registry::get('db');
		$select = $db->select()
					->from('locations');
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
		$this->view->headTitle()->prepend('Locations Edit');
		$form = new Zend_Form;
	 
		$locations = new Application_Model_Locations;
		$locationsMapper = new Application_Model_LocationsMapper;
		
		$form->setAction('/admin_locations/edit/')
			 ->setMethod('post')
			 ->setAttrib('name', 'inventEdit')
			 ->setAttrib('enctype', 'multipart/form-data');

		$form->addElement('text', 'name', array('required' => true,'label' => 'Name:','size'=>'80'));
		$form->addElement('textarea', 'specials', array('rows'=>10,'columns'=>20,'label' => 'Specials:'));
										
		$form->addElement('hidden', 'id');
		
		$form->addElement('image', 'apply', array('src' => '/images/buttons/apply.png'));
		
		// if post data is valid upload and insert selected spreaksheet
		if ($form->isValid($_POST)) {
						
			$locations->setName($form->getValue('name'));
			$locations->setSpecials($form->getValue('specials'));
			$locations->setId($form->getValue('id'));
			$locationsMapper->save($locations);
			
			$this->view->form = '<strong>Location has been updated!</strong>';
		// if form data is not valid print form
		} else {
			if($this->getRequest()->getParam('id') && $form->getValue('id') == ''){
				$locationsMapper->find($this->getRequest()->getParam('id'),$locations);
				$data = array(
							'name'=>$locations->getName(),
							'specials'=>$locations->getSpecials(),
							'id'=>$locations->getId(),
							);
				$form->setDefaults($data);	
			}
			
			$this->view->form = $form->render();
			
		}
	}
	
}

