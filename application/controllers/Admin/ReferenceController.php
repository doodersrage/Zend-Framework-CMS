<?php

class Admin_ReferenceController extends Zend_Controller_Action
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
		$this->view->headTitle()->prepend('Reference Materials');
		$referencematerials = new Application_Model_ReferenceMaterials;
		$referencematerialsMapper = new Application_Model_ReferenceMaterialsMapper;
		
		// check for delete action
		if($this->_request->getPost('delete')){
			if(is_array($this->_request->getPost('delete'))){
				foreach($this->_request->getPost('delete') as $curDel){
					$referencematerialsMapper->delete($curDel,$referencematerials);
				}
			}
		}
		
		// build parent drop down
		$db = Zend_Registry::get('db');
		$select = $db->select()
					->from('reference_materials');
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
	 
		$referencematerials = new Application_Model_ReferenceMaterials;
		$referencematerialsMapper = new Application_Model_ReferenceMaterialsMapper;
		
		$form->setAction('/admin_reference/edit/')
			 ->setMethod('post')
			 ->setAttrib('name', 'inventEdit')
			 ->setAttrib('enctype', 'multipart/form-data');

		$form->addElement('text', 'name', array('required' => true,'label' => 'Name:','size'=>'80'));
	
		// build file link drop down
		$db = Zend_Registry::get('db');
		$select = $db->select()
					->from('docs')
					->order('filename ASC');
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
		
		$form->addElement('text', 'filelnk', array('label' => 'Link: (For linking to external site or file.)','size'=>'80'));
		$form->addElement('checkbox', 'new_window', array('label' => 'Open Link in New Window:'));
		
		// add image upload fields
		$element = new Zend_Form_Element_File('refimage');
		$element->setLabel('Upload Image:')
				->setDestination(APP_BASE_PATH.'/upload/reference');
		// ensure only 1 file
		$element->addValidator('Count', false, 1);
		// only JPEG, PNG, and GIFs
		$element->addValidator('Extension', false, 'jpg,psd,gif,png');
		$form->addElement($element, 'refimage');
		
		$form->addElement('hidden', 'currentRefImage');
		$form->addElement('hidden', 'id');
		
		$form->addElement('textarea', 'description', array('rows'=>10,'columns'=>20,'required' => true,'label' => 'Description:'));
		
		$form->addElement('image', 'apply', array('src' => '/images/buttons/apply.png'));
		
		// if post data is valid upload and insert selected spreaksheet
		if ($form->isValid($_POST)) {
			
			// manage existing image if exists
			if($form->refimage->getFileName()){
			  $form->refimage->receive();
			  $image = $this->cleanFile($form->refimage->getFileName());
			} else {
			  $image = $form->getValue('currentRefImage');
			}
			
			$referencematerials->setName($form->getValue('name'));
			$referencematerials->setDescription($form->getValue('description'));
			$referencematerials->setImage($image);
			$referencematerials->setFilelnk($form->getValue('filelnk'));
			$referencematerials->setNew_window($form->getValue('new_window'));
			$referencematerials->setId($form->getValue('id'));
			$referencematerialsMapper->save($referencematerials);
			
			$this->view->form = '<strong>Reference material has been updated!</strong>';
		// if form data is not valid print form
		} else {
			
			if($this->getRequest()->getParam('id') && $form->getValue('id') == ''){
				$referencematerialsMapper->find($this->getRequest()->getParam('id'),$referencematerials);
				$data = array(
							'name'=>$referencematerials->getName(),
							'description'=>$referencematerials->getDescription(),
							'currentRefImage'=>$referencematerials->getImage(),
							'new_window'=>$referencematerials->getNew_window(),
							'filelnk'=>$referencematerials->getFilelnk(),
							'id'=>$referencematerials->getId(),
							);
	
				$form->setDefaults($data);	
				
				$form->getElement('currentRefImage')->setLabel('Current: '.$referencematerials->getImage());			
				
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

