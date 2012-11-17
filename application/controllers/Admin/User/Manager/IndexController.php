<?php

class Admin_User_Manager_IndexController extends Zend_Controller_Action
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
	
	// bof user manager section actions
	
    public function indexAction()
    {
		if($this->rest[2] == 1){
			$this->view->headTitle()->prepend('User Manager');
			$users = new Application_Model_Users;
			$usersMapper = new Application_Model_UsersMapper;
			
			// check for delete action
			if($this->_request->getPost('delete')){
				if(is_array($this->_request->getPost('delete'))){
					foreach($this->_request->getPost('delete') as $curDel){
						$usersMapper->delete($curDel,$users);
					}
				}
			}
			
			// display listing
			$results = $usersMapper->fetchAll();
			
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
	
    public function editAction()
    {
		if($this->rest[2] == 1){
			$this->view->headTitle()->prepend('User Edit');
			$form = new Zend_Form;
		 
			$users = new Application_Model_Users;
			$usersMapper = new Application_Model_UsersMapper;
			$usersGroups = new Application_Model_UsersGroups;
			$usersGroupsMapper = new Application_Model_UsersGroupsMapper;
			
			$form->setAction('/admin_user_manager_index/edit/')
				 ->setMethod('post')
				 ->setAttrib('name', 'inventEdit')
				 ->setAttrib('enctype', 'multipart/form-data');
	
			$form->addElement('text','username', array('required' => true,'label' => 'Username:'));
			$form->addElement('password','password', array('required' => true,'label' => 'Password:'));
			
			$userGroupsLst = $usersGroupsMapper->fetchAll();
			$levels = $form->createElement('select','group');
			$levels->setLabel('User Group:');
			foreach($userGroupsLst as $cur){
				$levels->addMultiOptions(array($cur->id => $cur->name));
			}
			$form->addElement($levels);
			
			$form->addElement('hidden', 'id');
			
			$form->addElement('image', 'apply', array('src' => '/images/buttons/apply.png'));
			
			// if post data is valid upload and insert selected spreaksheet
			if ($form->isValid($_POST)) {
	
				$users->setUsername($form->getValue('username'));
				$users->setPassword($this->_helper->pass($form->getValue('password')));
				$users->setGroup($form->getValue('group'));
				$users->setId($form->getValue('id'));
				$usersMapper->save($users);
				
				$this->view->form = '<strong>User has been updated!</strong>';
			// if form data is not valid print form
			} else {
				
				if($this->getRequest()->getParam('id') && $form->getValue('id') == ''){
					$usersMapper->find($this->getRequest()->getParam('id'),$users);
					$data = array(
								'username'=>$users->getUsername(),
								'password'=>'',
								'group'=>$users->getGroup(),
								'id'=>$users->getId(),
								);
		
					$form->setDefaults($data);				
					
				}
				$this->view->form = $form->render();
				
			}
		}
	}
	
	// eof user manager section actions
	
}

