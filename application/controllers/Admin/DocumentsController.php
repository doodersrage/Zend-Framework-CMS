<?php

class Admin_DocumentsController extends Zend_Controller_Action
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
	
	// bof documents section actions

    public function indexAction()
    {
		$this->view->headTitle()->prepend('Documents');
		$docs = new Application_Model_Docs;
		$docsMapper = new Application_Model_DocsMapper;
		
		// check for delete action
		if($this->_request->getPost('delete')){
			if(is_array($this->_request->getPost('delete'))){
				foreach($this->_request->getPost('delete') as $curDel){
					$docsMapper->delete($curDel,$docs);
				}
			}
		}
		
		// display listing
		$results = $docsMapper->fetchAll();
		
		if(isset($results)) {
			$paginator = Zend_Paginator::factory($results);
			$paginator->setItemCountPerPage(20);
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
		$this->view->headTitle()->prepend('Documents Edit');
		$form = new Zend_Form;
	 
		$docs = new Application_Model_Docs;
		$docsMapper = new Application_Model_DocsMapper;
		
		$form->setAction('/admin_documents/edit/')
			 ->setMethod('post')
			 ->setAttrib('name', 'inventEdit')
			 ->setAttrib('enctype', 'multipart/form-data');

							
		// add image upload fields
		$element = new Zend_Form_Element_File('uploaddoc');
		$element->setLabel('Upload Document:')
				->setDestination(APP_BASE_PATH.'/upload/docs');
		// ensure only 1 file
		$element->addValidator('Count', false, 1);
		// limit to 100K
		//$element->addValidator('Size', false, 404800);
		// only JPEG, PNG, and GIFs
		$element->addValidator('Extension', false, 'jpg,psd,gif');
		$form->addElement($element, 'uploaddoc');

		$form->addElement('hidden', 'currentDoc');
		$form->addElement('hidden', 'id');
		
		$form->addElement('image', 'apply', array('src' => '/images/buttons/apply.png'));
		
		// if post data is valid upload and insert selected spreaksheet
		if ($form->isValid($_POST)) {
			$doc = $form->getValue('currentDoc');

			$docs->setFilename($doc);
			$docs->setId($form->getValue('id'));
			$docsMapper->save($docs);
			
			$this->view->form = '<strong>Page has been updated!</strong>';
		// if form data is not valid print form
		} else {
			
			if($this->getRequest()->getParam('id') && $form->getValue('id') == ''){
				$docsMapper->find($this->getRequest()->getParam('id'),$docs);
				$data = array(
							'currentDoc'=>$docs->getFilename(),
							'id'=>$docs->getId(),
							);
				$form->getElement('currentDoc')->setLabel('Current: '.$docs->getFilename());
				$form->setDefaults($data);				
				
			}
			$this->view->form = $form->render();
			
		}
	}
	
	// eof documents section actions
	
}

