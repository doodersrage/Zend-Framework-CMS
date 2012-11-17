<?php

class Admin_VideosController extends Zend_Controller_Action
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
	
	// bof videos section actions
	
    public function indexAction()
    {
		if($this->rest[3] == 1) {
			$this->view->headTitle()->prepend('Videos');
			$videos = new Application_Model_Videos;
			$videosMapper = new Application_Model_VideosMapper;
			
			// check for delete action
			if($this->_request->getPost('delete')){
				if(is_array($this->_request->getPost('delete'))){
					foreach($this->_request->getPost('delete') as $curDel){
						$videosMapper->delete($curDel,$videos);
					}
				}
			}
			
			// display listing
			$results = $videosMapper->fetchAll();
			
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
		if($this->rest[3] == 1) {
			$this->view->headTitle()->prepend('Videos Edit');
			$form = new Zend_Form;
		 
			$videos = new Application_Model_Videos;
			$videosMapper = new Application_Model_VideosMapper;
			
			$form->setAction('/admin_videos/edit/')
				 ->setMethod('post')
				 ->setAttrib('name', 'inventEdit')
				 ->setAttrib('enctype', 'multipart/form-data');
	
			$form->addElement('text', 'name', array('required' => true,'label' => 'Name:','size'=>'80'));
			
			$form->addElement('textarea', 'description', array('rows'=>5,'cols'=>30,'label' => 'Description:'));
			
			// add image upload fields
			$element = new Zend_Form_Element_File('uploadimage');
			$element->setLabel('Upload Image:')
					->setDestination(APP_BASE_PATH.'/upload/video/images');
			// ensure only 1 file
			$element->addValidator('Count', false, 1);
			// limit to 100K
			//$element->addValidator('Size', false, 404800);
			// only JPEG, PNG, and GIFs
			$element->addValidator('Extension', false, 'jpg,psd,gif,png');
			$form->addElement($element, 'uploadimage');
			
			$form->addElement('hidden', 'currentImage');
			
			$form->addElement('text', 'remote', array('size'=>100,'label' => 'Remote Video URL: (EX: http://www.youtube.com/watch?v=8yoX4BBXwsw)','size'=>'80'));
			
			// add image upload fields
			$element = new Zend_Form_Element_File('uploadvideo');
			$element->setLabel('Upload Local Video:')
					->setDestination(APP_BASE_PATH.'/upload/video');
			// ensure only 1 file
			$element->addValidator('Count', false, 1);
			// limit to 100K
			//$element->addValidator('Size', false, 404800);
			// only JPEG, PNG, and GIFs
			$element->addValidator('Extension', false, 'jpg,psd,gif');
			$form->addElement($element, 'uploadvideo');
	
			$form->addElement('hidden', 'currentLocal');
			$form->addElement('hidden', 'id');
			
			$form->addElement('image', 'apply', array('src' => '/images/buttons/apply.png'));
			
			// if post data is valid upload and insert selected spreaksheet
			if ($form->isValid($_POST)) {
//				// upload new images if set
//				$form->uploadvideo->receive();
//				
				// manage existing image if exists
				if($form->uploadimage->getFileName()){
					$form->uploadimage->receive();
					$image = $this->cleanFile($form->uploadimage->getFileName());
				} else {
					$image = $form->getValue('currentImage');
				}
				
//				// manage existing video if exists
//				if($form->uploadvideo->getFileName()){
//					$video = $this->cleanFile($form->uploadvideo->getFileName());
//				} else {
					$video = $form->getValue('currentLocal');
//				}
	
				$videos->setName($form->getValue('name'));
				$videos->setDescription($form->getValue('description'));
				$videos->setImage($image);
				$videos->setRemote($form->getValue('remote'));
				$videos->setLocal($video);
				$videos->setId($form->getValue('id'));

				$videosMapper->save($videos);
				
				$this->view->form = '<strong>Page has been updated!</strong>';
			// if form data is not valid print form
			} else {
				
				if($this->getRequest()->getParam('id') && $form->getValue('id') == ''){
					$videosMapper->find($this->getRequest()->getParam('id'),$videos);
					$data = array(
								'name'=>$videos->getName(),
								'description'=>$videos->getDescription(),
								'currentImage'=>$videos->getImage(),
								'remote'=>$videos->getRemote(),
								'currentLocal'=>$videos->getLocal(),
								'id'=>$videos->getId(),
								);
					$form->getElement('currentImage')->setLabel('Current: '.$videos->getImage());
					$form->setDefaults($data);	
								
					$form->getElement('currentLocal')->setLabel('Current: '.$videos->getLocal());
					$form->setDefaults($data);				
					
				}
				$this->view->form = $form->render();
				
			}
		}
	}
	
	// cleans uploaded file for file name
	private function cleanFile($file){
		$file = explode('/',$file);
		$pcnt = count($file)-1;
		
	return $file[$pcnt];
	}
	
	// eof videos section actions
	
}

