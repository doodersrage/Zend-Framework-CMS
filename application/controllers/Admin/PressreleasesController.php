<?php

class Admin_PressreleasesController extends Zend_Controller_Action
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
		$this->view->headTitle()->prepend('Press Releases');
		$pressreleases = new Application_Model_PressReleases;
		$pressreleasesMapper = new Application_Model_PressReleasesMapper;
		
		// check for delete action
		if($this->_request->getPost('delete')){
			if(is_array($this->_request->getPost('delete'))){
				foreach($this->_request->getPost('delete') as $curDel){
					$pressreleasesMapper->delete($curDel,$events);
				}
			}
		}
		
		// build parent drop down
		$db = Zend_Registry::get('db');
		$select = $db->select()
					->from('press_releases')
					->order('date DESC');
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
		$this->view->headTitle()->prepend('Press Releases Edit');
		$form = new Zend_Form;
	 
		$pressreleases = new Application_Model_PressReleases;
		$pressreleasesMapper = new Application_Model_PressReleasesMapper;
		
		$form->setAction('/admin_pressreleases/edit/')
			 ->setMethod('post')
			 ->setAttrib('name', 'inventEdit')
			 ->setAttrib('enctype', 'multipart/form-data');

		$form->addElement('text', 'title', array('required' => true,'label' => 'Title:','size'=>'80'));
		$form->addElement('text', 'date', array('required' => true,'label' => 'Date:','size'=>'80'));
		
		// build file link drop down
		$db = Zend_Registry::get('db');
		$select = $db->select()
					->from('docs');
		$results = $db->fetchAll($select);
		
		$levels = $form->createElement('select','docLnk');
		$levels->setLabel('Link Document: (Populates File Link on selection.)');
		$ddVals = array();
		$ddVals[0] = '';
		foreach($results as $cur){
			$ddVals[$cur[filename]] = $cur[filename];
		}
		$levels->addMultiOptions($ddVals);
		$form->addElement($levels);

		$form->addElement('text', 'filelnk', array('label' => 'File Link:','size'=>'120'));
		$form->addElement('textarea', 'copy_text', array('rows'=>2,'columns'=>20,'required' => true,'label' => 'Description:'));
		
		$form->addElement('hidden', 'id');
		
		$form->addElement('image', 'apply', array('src' => '/images/buttons/apply.png'));
		
		// if post data is valid upload and insert selected spreaksheet
		if ($form->isValid($_POST)) {
			
			$pressreleases->setTitle($form->getValue('title'));
			$pressreleases->setDate(date("Y-n-j",strtotime($form->getValue('date'))));
			$pressreleases->setFilelnk($form->getValue('filelnk'));
			$pressreleases->setCopy_text($form->getValue('copy_text'));
			$pressreleases->setId($form->getValue('id'));
			$pressreleasesMapper->save($pressreleases);
			
			$this->view->form = '<strong>Event has been updated!</strong>';
		// if form data is not valid print form
		} else {
			
			if($this->getRequest()->getParam('id') && $form->getValue('id') == ''){
				$pressreleasesMapper->find($this->getRequest()->getParam('id'),$pressreleases);
				$data = array(
							'title'=>$pressreleases->getTitle(),
							'date'=>date("n/j/Y",strtotime($pressreleases->getDate())),
							'filelnk'=>$pressreleases->getFilelnk(),
							'copy_text'=>$pressreleases->getCopy_text(),
							'id'=>$pressreleases->getId(),
							);
	
				$form->setDefaults($data);	
								
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

