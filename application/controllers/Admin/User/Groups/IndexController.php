<?php

class Admin_User_Groups_IndexController extends Zend_Controller_Action
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
	
	// bof user groups section actions
	
    public function indexAction()
    {
		if($this->rest[1] == 1) {
			$this->view->headTitle()->prepend('User Groups');
			$usersGroups = new Application_Model_UsersGroups;
			$usersGroupsMapper = new Application_Model_UsersGroupsMapper;
			
			// check for delete action
			if($this->_request->getPost('delete')){
				if(is_array($this->_request->getPost('delete'))){
					foreach($this->_request->getPost('delete') as $curDel){
						$usersGroupsMapper->delete($curDel,$users);
					}
				}
			}
			
			// display listing
			$results = $usersGroupsMapper->fetchAll();
			
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
	
	public function editAction(){
		if($this->rest[1] == 1) {
			$this->view->headTitle()->prepend('User Groups Edit');
			$usersGroups = new Application_Model_UsersGroups;
			$usersGroupsMapper = new Application_Model_UsersGroupsMapper;
	
			$form = new Zend_Form;
			
			$form->setAction('/admin_user_groups_index/edit/')
				 ->setMethod('post')
				 ->setAttrib('name', 'inventEdit')
				 ->setAttrib('enctype', 'multipart/form-data');
	
			$form->addElement('text','name', array('required' => true,'label' => 'Name:','size'=>'80'));
			$form->addElement('hidden', 'id', array('label' => 'Restrictions'));
			$form->addElement('checkbox','restrictions1', array('label' => 'Groups'));
			$form->addElement('checkbox','restrictions2', array('label' => 'Users'));
			$form->addElement('checkbox','restrictions3', array('label' => 'Videos'));
			$form->addElement('checkbox','restrictions7', array('label' => 'Playlists'));
			$form->addElement('checkbox','restrictions4', array('label' => 'Pages'));
			$form->addElement('checkbox','restrictions5', array('label' => 'Inventory'));
			$form->addElement('checkbox','restrictions6', array('label' => 'Form Submissions'));
			$form->addElement('checkbox','restrictions8', array('label' => 'Locations'));
			$form->addElement('checkbox','restrictions9', array('label' => 'Inventory'));
			$form->addElement('checkbox','restrictions10', array('label' => 'Events'));
			$form->addElement('checkbox','restrictions11', array('label' => 'News'));
			$form->addElement('checkbox','restrictions12', array('label' => 'Slideshows'));
			$form->addElement('checkbox','restrictions13', array('label' => 'Docs'));
			$form->addElement('checkbox','restrictions14', array('label' => 'Site Config'));
			
			$form->addElement('image', 'apply', array('src' => '/images/buttons/apply.png'));
			
			// if post data is valid upload and insert selected spreaksheet
			if ($form->isValid($_POST)) {
	
				$this->rest = array();
				$this->rest[1] = $form->getValue('restrictions1');
				$this->rest[2] = $form->getValue('restrictions2');
				$this->rest[3] = $form->getValue('restrictions3');
				$this->rest[4] = $form->getValue('restrictions4');
				$this->rest[5] = $form->getValue('restrictions5');
				$this->rest[6] = $form->getValue('restrictions6');
				$this->rest[7] = $form->getValue('restrictions7');
				$this->rest[8] = $form->getValue('restrictions8');
				$this->rest[9] = $form->getValue('restrictions9');
				$this->rest[10] = $form->getValue('restrictions10');
				$this->rest[11] = $form->getValue('restrictions11');
				$this->rest[12] = $form->getValue('restrictions12');
				$this->rest[13] = $form->getValue('restrictions13');
				$this->rest[14] = $form->getValue('restrictions14');
	
				$usersGroups->setName($form->getValue('name'));
				$usersGroups->setRestrictions(serialize($this->rest));
				$usersGroups->setId($form->getValue('id'));
				$usersGroupsMapper->save($usersGroups);
				
				$this->view->form = '<strong>User Group has been updated!</strong>';
			// if form data is not valid print form
			} else {
				
				if($this->getRequest()->getParam('id') && $form->getValue('id') == ''){
					$usersGroupsMapper->find($this->getRequest()->getParam('id'),$usersGroups);
					$this->rest = unserialize($usersGroups->getRestrictions());
					$data = array(
								'name'=>$usersGroups->getName(),
								'restrictions1'=>$this->rest[1],
								'restrictions2'=>$this->rest[2],
								'restrictions3'=>$this->rest[3],
								'restrictions4'=>$this->rest[4],
								'restrictions5'=>$this->rest[5],
								'restrictions6'=>$this->rest[6],
								'restrictions7'=>$this->rest[7],
								'restrictions8'=>$this->rest[8],
								'restrictions9'=>$this->rest[9],
								'restrictions9'=>$this->rest[10],
								'restrictions9'=>$this->rest[11],
								'restrictions9'=>$this->rest[12],
								'restrictions9'=>$this->rest[13],
								'restrictions9'=>$this->rest[14],
								'id'=>$usersGroups->getId(),
								);
		
					$form->setDefaults($data);				
					
				}
				$this->view->form = $form->render();
				
			}
		}
	}
	
	// eof user groups section actions
	
}

