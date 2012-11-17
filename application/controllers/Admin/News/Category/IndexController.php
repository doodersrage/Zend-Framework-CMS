<?php

class Admin_News_Category_IndexController extends Zend_Controller_Action
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
		$this->view->headTitle()->prepend('News Category');
		$newscategory = new Application_Model_NewsCategory;
		$newscategoryMapper = new Application_Model_NewsCategoryMapper;
		
		// check for delete action
		if($this->_request->getPost('delete')){
			if(is_array($this->_request->getPost('delete'))){
				foreach($this->_request->getPost('delete') as $curDel){
					$newscategoryMapper->delete($curDel,$newscategory);
				}
			}
		}
		
		// build parent drop down
		$db = Zend_Registry::get('db');
		$select = $db->select()
					->from('news_category');
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
		$this->view->headTitle()->prepend('News Category Edit');
		$form = new Zend_Form;
	 
		$newscategory = new Application_Model_NewsCategory;
		$newscategoryMapper = new Application_Model_NewsCategoryMapper;
		
		$form->setAction('/admin_news_category_index/edit/')
			 ->setMethod('post')
			 ->setAttrib('name', 'inventEdit')
			 ->setAttrib('enctype', 'multipart/form-data');

		$form->addElement('text', 'name', array('required' => true,'label' => 'Name:','size'=>'80'));
				
		$form->addElement('textarea', 'description', array('rows'=>10,'columns'=>20,'label' => 'Description:'));
		
		$form->addElement('hidden', 'id');
		
		$form->addElement('image', 'apply', array('src' => '/images/buttons/apply.png'));
		
		// if post data is valid upload and insert selected spreaksheet
		if ($form->isValid($_POST)) {
						
			$newscategory->setName($form->getValue('name'));
			$newscategory->setDescription($form->getValue('description'));
			$newscategory->setId($form->getValue('id'));
			$newscategoryMapper->save($newscategory);
			
			$this->view->form = '<strong>News Category has been updated!</strong>';
		// if form data is not valid print form
		} else {
			
			if($this->getRequest()->getParam('id') && $form->getValue('id') == ''){
				$newscategoryMapper->find($this->getRequest()->getParam('id'),$newscategory);
				$data = array(
							'name'=>$newscategory->getName(),
							'description'=>$newscategory->getDescription(),
							'id'=>$newscategory->getId(),
							);
	
				$form->setDefaults($data);	
								
			}
			$this->view->form = $form->render();
			
		}
	}
	
}

