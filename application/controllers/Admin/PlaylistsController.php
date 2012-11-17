<?php

class Admin_PlaylistsController extends Zend_Controller_Action
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
	
	// bof playlists section actions
	
    public function indexAction()
    {
		if($this->rest[7] == 1) {
			$this->view->headTitle()->prepend('Playlists');
			$playlist = new Application_Model_Playlist;
			$playlistMapper = new Application_Model_PlaylistMapper;
			
			// check for delete action
			if($this->_request->getPost('delete')){
				if(is_array($this->_request->getPost('delete'))){
					foreach($this->_request->getPost('delete') as $curDel){
						$playlistMapper->delete($curDel);
					}
				}
			}
			
			// display listing
			$results = $playlistMapper->fetchAll();
			
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
		if($this->rest[7] == 1){
			$this->view->headTitle()->prepend('Playlist Edit');
			$form = new Zend_Form;
		 
			$playlist = new Application_Model_Playlist;
			$playlistMapper = new Application_Model_PlaylistMapper;
			
			$form->setAction('/admin_playlists/edit/')
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
	
				$playlist->setName($form->getValue('name'));
				$playlist->setHeight($form->getValue('height'));
				$playlist->setWidth($form->getValue('width'));
				$playlist->setAutoplay($form->getValue('autoplay'));
				$playlist->setControlbar($form->getValue('controlbar'));
				$playlist->setPlaylistvisual($form->getValue('playlistvisual'));
				$playlist->setId($form->getValue('id'));
				$playlistMapper->save($playlist);
				
				$this->view->form = '<strong>Playlist has been updated!</strong>';
			// if form data is not valid print form
			} else {
				
				if($this->getRequest()->getParam('id') && $form->getValue('id') == ''){
					$playlistMapper->find($this->getRequest()->getParam('id'),$playlist);
					$data = array(
								'name'=>$playlist->getName(),
								'height'=>$playlist->getHeight(),
								'width'=>$playlist->getWidth(),
								'autoplay'=>$playlist->getAutoplay(),
								'controlbar'=>$playlist->getControlbar(),
								'playlistvisual'=>$playlist->getPlaylistvisual(),
								'id'=>$playlist->getId(),
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
	}
	
	public function itemsEditAction()
    {
		if($this->rest[7] == 1){
			$this->view->headScript()->appendFile('/js/playlist.js');
			$this->view->headTitle()->prepend('Playlist Items Edit');
			$form = new Zend_Form;
		 
			$PlaylistData = new Application_Model_PlaylistData;
			$PlaylistDataMapper = new Application_Model_PlaylistDataMapper;
			
			$form->setAction('/admin_playlists/edit/')
				 ->setMethod('post')
				 ->setAttrib('name', 'inventEdit')
				 ->setAttrib('enctype', 'multipart/form-data');
	
			$form->addElement('text','vidsearch', array('label' => 'Search:'));
						
			$form->addElement('hidden', 'videos');
			$form->addElement('hidden', 'pid');
			
			
			// if post data is valid upload and insert selected spreaksheet
			if ($this->getRequest()->getParam('id')) {
								
				$data = array(
							'pid'=>$this->getRequest()->getParam('id'),
							);
	
				$form->setDefaults($data);				
				
				$this->view->form = $form->render();
				
			}
		}
	}
	// eof playlists section actions
	
}

