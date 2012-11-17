<?php

class Admin_Forms_IndexController extends Zend_Controller_Action
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
		if($this->rest[6] == 1){
			$this->view->headTitle()->prepend('Forms');
			$forms = new Application_Model_Forms;
			$formsMapper = new Application_Model_FormsMapper;
			
			// check for delete action
			if($this->_request->getPost('delete')){
				if(is_array($this->_request->getPost('delete'))){
					foreach($this->_request->getPost('delete') as $curDel){
						$formsMapper->delete($curDel,$forms);
						$condition = array('form_id = ?' => $curDel);
						$db->delete('form_fields', $condition);
					}
				}
			}
			
			// display listing
			$db = Zend_Registry::get('db');
			$select = $db->select()
						->from('forms');
			$select->order('name DESC');
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
	
    public function editAction()
    {
		$this->view->headTitle()->prepend('Form Edit');
		$form = new Zend_Form;
	 
		$forms = new Application_Model_Forms;
		$formsMapper = new Application_Model_FormsMapper;
		
		$form->setAction('/admin_forms_index/edit/')
			 ->setMethod('post')
			 ->setAttrib('name', 'inventEdit')
			 ->setAttrib('enctype', 'multipart/form-data');
		$form->addElement('text', 'name', array('required' => true,'label' => 'Name:','size'=>'80'));
		$form->addElement('checkbox','captcha', array('label' => 'Captcha:'));
		$form->addElement('textarea', 'description', array('rows'=>2,'columns'=>20,'required' => false,'label' => 'Description:'));
		$form->addElement('textarea', 'email', array('rows'=>2,'columns'=>20,'required' => false,'label' => 'Recipients:'));
		$form->addElement('textarea', 'message', array('rows'=>2,'columns'=>20,'required' => false,'label' => 'Message: (Displayed when user completes form.)'));
				
		$form->addElement('hidden', 'id');
		
		$form->addElement('image', 'apply', array('src' => '/images/buttons/apply.png'));
		
		// if post data is valid upload and insert selected spreaksheet
		if ($form->isValid($_POST)) {
			
			$forms->setName($form->getValue('name'));
			$forms->setDescription($form->getValue('description'));
			$forms->setEmail($form->getValue('email'));
			$forms->setMessage($form->getValue('message'));
			$forms->setCaptcha($form->getValue('captcha'));
			$forms->setId($form->getValue('id'));
			$formsMapper->save($forms);
			
			$this->view->form = '<strong>Form has been updated!</strong>';
		// if form data is not valid print form
		} else {
			
			if($this->getRequest()->getParam('id') && $form->getValue('id') == ''){
				$formsMapper->find($this->getRequest()->getParam('id'),$forms);
				$data = array(
							'name'=>$forms->getName(),
							'description'=>$forms->getDescription(),
							'email'=>$forms->getEmail(),
							'message'=>$forms->getMessage(),
							'captcha'=>$forms->getCaptcha(),
							'id'=>$forms->getId(),
							);
	
				$form->setDefaults($data);	
				
				$this->view->forms_id = $forms->getId();
								
			} else {
				$data = array();
	
				$form->setDefaults($data);	
			}
			$this->view->form = $form->render();
			
		}
	}
}