<?php

class Admin_PartnersController extends Zend_Controller_Action
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
		$this->view->headTitle()->prepend('Partners');
		$partners = new Application_Model_Partners;
		$partnersMapper = new Application_Model_PartnersMapper;
		
		// check for delete action
		if($this->_request->getPost('delete')){
			if(is_array($this->_request->getPost('delete'))){
				foreach($this->_request->getPost('delete') as $curDel){
					$partnersMapper->delete($curDel,$partners);
				}
			}
		}
		
		// build parent drop down
		$db = Zend_Registry::get('db');
		$select = $db->select()
					->from('partners');
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
		$this->view->headTitle()->prepend('Partner Edit');
		$form = new Zend_Form;
	 
		$partners = new Application_Model_Partners;
		$partnersMapper = new Application_Model_PartnersMapper;
		
		$form->setAction('/admin_partners/edit/')
			 ->setMethod('post')
			 ->setAttrib('name', 'inventEdit')
			 ->setAttrib('enctype', 'multipart/form-data');

		$form->addElement('text', 'name', array('required' => true,'label' => 'Name:','size'=>'80'));
		$form->addElement('text', 'link', array('required' => true,'label' => 'Link: (For linking to external site or file.)','size'=>'80'));
		
		// add image upload fields
		$element = new Zend_Form_Element_File('image');
		$element->setLabel('Upload Image:')
				->setDestination(APP_BASE_PATH.'/upload/partners');
		// ensure only 1 file
		$element->addValidator('Count', false, 1);
		// only JPEG, PNG, and GIFs
		$element->addValidator('Extension', false, 'jpg,psd,gif,png');
		$form->addElement($element, 'image');
		
		$form->addElement('hidden', 'currentImage');
				
		$form->addElement('textarea', 'description', array('rows'=>10,'columns'=>20,'required' => true,'label' => 'Description:'));
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
			
			$partners->setName($form->getValue('name'));
			$partners->setDescription($form->getValue('description'));
			$partners->setImage($image);
			$partners->setLink($form->getValue('link'));
			$partners->setId($form->getValue('id'));
			$partnersMapper->save($partners);
			
			$this->view->form = '<strong>Partner has been updated!</strong>';
		// if form data is not valid print form
		} else {
			
			if($this->getRequest()->getParam('id') && $form->getValue('id') == ''){
				$partnersMapper->find($this->getRequest()->getParam('id'),$partners);
				$data = array(
							'name'=>$partners->getName(),
							'description'=>$partners->getDescription(),
							'currentImage'=>$partners->getImage(),
							'link'=>$partners->getLink(),
							'id'=>$partners->getId(),
							);
	
				$form->setDefaults($data);	
				
				$form->getElement('currentImage')->setLabel('Current: '.$partners->getImage());			
				
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

