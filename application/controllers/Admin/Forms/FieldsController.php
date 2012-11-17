<?php

class Admin_Forms_FieldsController extends Zend_Controller_Action
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
		
		$this->view->fid = $this->_getParam('fid');
		
    }
	
    public function indexAction()
    {
		if($this->rest[6] == 1){
			$this->view->headTitle()->prepend('Form Fields');
			$formfields = new Application_Model_FormFields;
			$formfieldsMapper = new Application_Model_FormFieldsMapper;
			
			// check for delete action
			if($this->_request->getPost('delete')){
				if(is_array($this->_request->getPost('delete'))){
					foreach($this->_request->getPost('delete') as $curDel){
						$formfieldsMapper->delete($curDel,$formfields);
					}
				}
			}
			
			// display listing
			$db = Zend_Registry::get('db');
			$select = $db->select()
						->from('form_fields')
						->where('form_id = ?',$this->_getParam('fid'));
			$select->order('order_val DESC');
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
	 
		$formfields = new Application_Model_FormFields;
		$formfieldsMapper = new Application_Model_FormFieldsMapper;
		
		$form->setAction('')
			 ->setMethod('post')
			 ->setAttrib('name', 'inventEdit')
			 ->setAttrib('enctype', 'multipart/form-data');
			 
		$form->addElement('text', 'name', array('required' => true,'label' => 'Name:','size'=>'80'));
		$form->addElement('textarea', 'description', array('rows'=>4,'columns'=>20,'required' => false,'label' => 'Description:'));
		$form->addElement('checkbox','required', array('label' => 'Required:'));
		$form->addElement('text', 'order_val', array('required' => true,'label' => 'Order:','size'=>'10'));
		
		$levels = $form->createElement('select','type');
		$levels->setLabel('Field Type:');
		$ddVals = array();
		$ddVals['textbox'] = 'text box';
		$ddVals['textarea'] = 'text area';
		$ddVals['radiobutton'] = 'radio button';
		$ddVals['checkbox'] = 'check box';
		$ddVals['selectbox'] = 'select box';
		$ddVals['fileupload'] = 'file upload';
		$levels->addMultiOptions($ddVals);
		$form->addElement($levels);
		
		$form->addElement('text', 'height', array('required' => false,'label' => 'Rows:','size'=>'10'));
		$form->addElement('text', 'width', array('required' => false,'label' => 'Columns:','size'=>'10'));
		$form->addElement('textarea', 'default_val', array('rows'=>4,'columns'=>20,'required' => false,'label' => 'Default:'));
				
		$form->addElement('hidden', 'form_id');
		$form->addElement('hidden', 'id');
		
		$form->addElement('image', 'apply', array('src' => '/images/buttons/apply.png'));
		
		// if post data is valid upload and insert selected spreaksheet
		if ($form->isValid($_POST)) {
			
			$formfields->setName($form->getValue('name'));
			$formfields->setDescription($form->getValue('description'));
			$formfields->setOrder_val($form->getValue('order_val'));
			$formfields->setType($form->getValue('type'));
			$formfields->setWidth($form->getValue('width'));
			$formfields->setHeight($form->getValue('height'));
			$formfields->setDefault_val($form->getValue('default_val'));
			$formfields->setRequired($form->getValue('required'));
			$formfields->setForm_id($form->getValue('form_id'));
			$formfields->setId($form->getValue('id'));
			$formfieldsMapper->save($formfields);
			
			$this->view->form = '<strong>Form field has been updated!</strong>';
		// if form data is not valid print form
		} else {
			
			if($this->getRequest()->getParam('id') && $form->getValue('id') == ''){
				$formfieldsMapper->find($this->getRequest()->getParam('id'),$formfields);
				$data = array(
							'name'=>$formfields->getName(),
							'description'=>$formfields->getDescription(),
							'order_val'=>$formfields->getOrder_val(),
							'type'=>$formfields->getType(),
							'width'=>$formfields->getWidth(),
							'height'=>$formfields->getHeight(),
							'default_val'=>$formfields->getDefault_val(),
							'required'=>$formfields->getRequired(),
							'form_id'=>$formfields->getForm_id(),
							'id'=>$formfields->getId(),
							);
	
				$form->setDefaults($data);	
				
				$this->view->forms_id = $formfields->getId();
								
			} else {
				$data = array(
							'form_id'=>$this->_getParam('fid'),
							'required'=>1,
							'width'=>10,
							'height'=>4,
							'order_val'=>0,
							);
	
				$form->setDefaults($data);	
			}
			$this->view->form = $form->render();
			
		}
	}
}