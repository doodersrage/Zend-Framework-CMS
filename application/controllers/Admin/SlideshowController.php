<?php

class Admin_SlideshowController extends Zend_Controller_Action
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
			$this->view->headTitle()->prepend('Slideshows');
			$slideshow = new Application_Model_Slideshow;
			$slideshowMapper = new Application_Model_SlideshowMapper;
			
			// check for delete action
			if($this->_request->getPost('delete')){
				if(is_array($this->_request->getPost('delete'))){
					foreach($this->_request->getPost('delete') as $curDel){
						$slideshowMapper->delete($curDel);
					}
				}
			}
			
			// display listing
			$results = $slideshowMapper->fetchAll();
			
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
			$this->view->headTitle()->prepend('Slideshow Edit');
			$form = new Zend_Form;
		 
			$slideshow = new Application_Model_Slideshow;
			$slideshowMapper = new Application_Model_SlideshowMapper;
			
			$form->setAction('/admin_slideshow/edit/')
				 ->setMethod('post')
				 ->setAttrib('name', 'inventEdit')
				 ->setAttrib('enctype', 'multipart/form-data');
	
			$form->addElement('text','name', array('required' => true,'label' => 'Name:','size'=>'80'));
			$form->addElement('text','height', array('required' => true,'label' => 'Height:','size'=>'10','limit'=>'11'));
			$form->addElement('text','width', array('required' => true,'label' => 'Width:','size'=>'10','limit'=>'11'));
			$form->addElement('checkbox','autoplay', array('label' => 'Auto-play:'));
			$form->addElement('checkbox','controlbar', array('label' => 'Display Control Bar:'));
			$form->addElement('checkbox','playlistvisual', array('label' => 'Display Playlist Selector:'));
						
			$form->addElement('hidden', 'id');
			
			$form->addElement('image', 'apply', array('src' => '/images/buttons/apply.png'));
			
			// if post data is valid upload and insert selected spreaksheet
			if ($form->isValid($_POST)) {
	
				$slideshow->setName($form->getValue('name'));
				$slideshow->setHeight($form->getValue('height'));
				$slideshow->setWidth($form->getValue('width'));
				$slideshow->setAutoplay($form->getValue('autoplay'));
				$slideshow->setControlbar($form->getValue('controlbar'));
				$slideshow->setPlaylistvisual($form->getValue('playlistvisual'));
				$slideshow->setId($form->getValue('id'));
				$slideshowMapper->save($slideshow);
				
				$this->view->form = '<strong>Slideshow has been updated!</strong>';
			// if form data is not valid print form
			} else {
				
				if($this->getRequest()->getParam('id') && $form->getValue('id') == ''){
					$slideshowMapper->find($this->getRequest()->getParam('id'),$slideshow);
					$data = array(
								'name'=>$slideshow->getName(),
								'height'=>$slideshow->getHeight(),
								'width'=>$slideshow->getWidth(),
								'autoplay'=>$slideshow->getAutoplay(),
								'controlbar'=>$slideshow->getControlbar(),
								'playlistvisual'=>$slideshow->getPlaylistvisual(),
								'id'=>$slideshow->getId(),
								);
		
					$form->setDefaults($data);				
					
				} else {
					$data = array(
								'height'=>243,
								'width'=>339,
								'autoplay'=>1,
								'controlbar'=>1,
								'playlistvisual'=>1,
								);
		
					$form->setDefaults($data);				
				}
				$this->view->form = $form->render();
				
			}
	}
	
    public function imgListAction()
    {
			$this->view->headTitle()->prepend('Slideshow List');
			$slideshowdata = new Application_Model_SlideshowData;
			$slideshowdataMapper = new Application_Model_SlideshowDataMapper;
			
			// check for delete action
			if($this->_request->getPost('delete')){
				if(is_array($this->_request->getPost('delete'))){
					foreach($this->_request->getPost('delete') as $curDel){
						$slideshowdataMapper->delete($this->_getParam('sid'),$curDel);
					}
				}
			}
			
			// display listing
			// build parent drop down
			$db = Zend_Registry::get('db');
			$select = $db->select()
						->from('slideshow_data')
						->where('pid = ?',$this->_getParam('sid'))
						->order('sort_order ASC')
						->order('img ASC');
			$results = $db->fetchAll($select);
			
			if(isset($results)) {
				$paginator = Zend_Paginator::factory($results);
				$paginator->setItemCountPerPage(10);
				$paginator->setCurrentPageNumber($this->_getParam('page'));
				$this->view->paginator = $paginator;
	 
				Zend_Paginator::setDefaultScrollingStyle('Sliding');
				Zend_View_Helper_PaginationControl::setDefaultViewPartial(
					'admin/slideshow-paginator.phtml'
				);
			}
    }
	
	public function imageEditAction()
    {
			$this->view->headTitle()->prepend('Slideshow Edit');
			$form = new Zend_Form;
		 
			$slideshowdata = new Application_Model_SlideshowData;
			$slideshowdataMapper = new Application_Model_SlideshowDataMapper;
			
			$form->setAction('')
				 ->setMethod('post')
				 ->setAttrib('name', 'inventEdit')
				 ->setAttrib('enctype', 'multipart/form-data');
	
			if($this->getRequest()->getParam('id') == ''){
				// add image upload fields
				$element = new Zend_Form_Element_File('uploadimg');
				$element->setLabel('Upload Image:')
						->setDestination(APP_BASE_PATH.'/upload/images/');
				// ensure only 1 file
				$element->addValidator('Count', false, 1);
				// limit to 100K
				//$element->addValidator('Size', false, 404800);
				// only JPEG, PNG, and GIFs
				$element->addValidator('Extension', true, 'jpg,png,gif');
				$form->addElement($element, 'uploadimg');
			}
			
			$form->addElement('hidden', 'currentImg');
			$form->addElement('text','desc', array('required' => true,'label' => 'Description:','size'=>'80'));
			$form->addElement('text','sort_order', array('required' => true,'label' => 'Sort Order:','size'=>'10','limit'=>'11'));
						
			$form->addElement('hidden', 'pid');
			
			$form->addElement('image', 'apply', array('src' => '/images/buttons/apply.png'));
			
			// if post data is valid upload and insert selected spreaksheet
			if ($form->isValid($_POST)) {
				$img = $form->getValue('currentImg');
	
				$slideshowdata->setImg($img);
				$slideshowdata->setDesc($form->getValue('desc'));
				$slideshowdata->setSort_order($form->getValue('sort_order'));
				$slideshowdata->setPid($form->getValue('pid'));
				
				if($this->getRequest()->getParam('id') != ''){
					$slideshowdataMapper->update($slideshowdata);
				} else {
					$slideshowdataMapper->save($slideshowdata);
				}
				$this->view->form = '<strong>Slideshow image has been updated!</strong>';
				$this->view->sid = $form->getValue('pid');				
			// if form data is not valid print form
			} else {
				
				if($this->getRequest()->getParam('id') && $form->getValue('id') == ''){
					$slideshowdata->setPid($this->getRequest()->getParam('sid'));
					$slideshowdata->setImg($this->getRequest()->getParam('id'));
					$slideshowdataMapper->find($slideshowdata);
					$data = array(
								'currentImg'=>$slideshowdata->getImg(),
								'desc'=>$slideshowdata->getDesc(),
								'sort_order'=>$slideshowdata->getSort_order(),
								'pid'=>$slideshowdata->getPid(),
								);
		
					$form->setDefaults($data);				
					$form->getElement('currentImg')->setLabel('Current: '.$slideshowdata->getImg());
					$this->view->sid = $slideshowdata->getPid();				
					
				} else {
					$data = array(
								'sort_order'=>0,
								'pid'=>$this->_getParam('sid'),
								);
		
					$form->setDefaults($data);
					
					$this->view->sid = $this->_getParam('sid');				
				}
				$this->view->form = $form->render();
				
			}
	}
	
}

