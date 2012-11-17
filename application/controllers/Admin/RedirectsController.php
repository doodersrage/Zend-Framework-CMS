<?php

class Admin_RedirectsController extends Zend_Controller_Action
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
		$this->view->headTitle()->prepend('Redirects');
		$redirects = new Application_Model_Redirects;
		$redirectsMapper = new Application_Model_RedirectsMapper;
		
		// check for delete action
		if($this->_request->getPost('delete')){
			if(is_array($this->_request->getPost('delete'))){
				foreach($this->_request->getPost('delete') as $curDel){
					$redirectsMapper->delete($curDel,$docs);
				}
			}
		}
		
		// display listing
		$results = $redirectsMapper->fetchAll();
		
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
	 
		$redirects = new Application_Model_Redirects;
		$redirectsMapper = new Application_Model_RedirectsMapper;
		
		$form->setMethod('post')
			 ->setAttrib('name', 'inventEdit')
			 ->setAttrib('enctype', 'multipart/form-data');
							
		$form->addElement('text','olduri', array('required' => true,'label' => 'Olduri: (EX: /old-page/)','size'=>'100'));
		$form->addElement('text','newuri', array('required' => true,'label' => 'Newuri: (EX: /new-page/)','size'=>'100'));

		$form->addElement('hidden', 'id');
		
		$form->addElement('image', 'apply', array('src' => '/images/buttons/apply.png'));
		
		// if post data is valid upload and insert selected spreaksheet
		if ($form->isValid($_POST)) {

			$redirects->setOlduri($form->getValue('olduri'));
			$redirects->setNewuri($form->getValue('newuri'));
			$redirects->setId($form->getValue('id'));
			$redirectsMapper->save($redirects);
			
			$this->view->form = '<strong>Redirect has been updated!</strong>';
		// if form data is not valid print form
		} else {
			
			if($this->getRequest()->getParam('id') && $form->getValue('id') == ''){
				$redirectsMapper->find($this->getRequest()->getParam('id'),$redirects);
				$data = array(
							'olduri'=>$redirects->getOlduri(),
							'newuri'=>$redirects->getNewuri(),
							'id'=>$redirects->getId(),
							);
				$form->setDefaults($data);				
				
			}
			$this->view->form = $form->render();
			
		}
	}
	
	// eof documents section actions
	
}

