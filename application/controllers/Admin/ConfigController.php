<?php

class Admin_ConfigController extends Zend_Controller_Action
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
		
		$this->view->headScript()->appendFile('/js/admin-config.js');
    }
		
    public function indexAction()
    {
		$this->view->headTitle()->prepend('Configuration');
		$config = new Application_Model_Config;
		$configMapper = new Application_Model_ConfigMapper;
		
		// check for delete action
		if($this->_request->getPost('delete')){
			if(is_array($this->_request->getPost('delete'))){
				foreach($this->_request->getPost('delete') as $curDel){
					$configMapper->delete($curDel,$partners);
				}
			}
		}
		
		// build parent drop down
		$db = Zend_Registry::get('db');
		$select = $db->select()
					->from('config_cats');
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
		
    public function confExpAction()
    {
		$this->_helper->layout()->disableLayout();
		
		// build parent drop down
		$db = Zend_Registry::get('db');
		$select = $db->select()
					->from('config')
					->where('cat_id = ?',$this->_getParam('pid'));
		$results = $db->fetchAll($select);
		$this->view->paginator = $results;
    }
	
    public function editAction()
    {
		$this->view->headTitle()->prepend('Partner Edit');
		$form = new Zend_Form;
	 
		$config = new Application_Model_Config;
		$configMapper = new Application_Model_ConfigMapper;
		
		$form->setAction('/admin_config/edit/')
			 ->setMethod('post')
			 ->setAttrib('name', 'inventEdit')
			 ->setAttrib('enctype', 'multipart/form-data');

		
		$configMapper->find($this->getRequest()->getParam('id'),$config);
		$data = array(
					'name'=>$config->getName(),
					'type'=>$config->getType(),
					'defin'=>$config->getDefin(),
					'cat_id'=>$config->getCat_id(),
					'funct'=>$config->getFunct(),
					'id'=>$config->getId(),
					);
		$this->view->fldName = $config->getName();

		
		switch($config->getType()){
			case 'text':
				$form->addElement('text', 'defin', array('required' => true,'label' => 'Definition:','size'=>'80'));
			break;
			case 'textarea':
				$form->addElement('textarea', 'defin', array('rows'=>10,'columns'=>20,'label' => 'desciption:'));
			break;
			case 'wysiwyg':
				$this->view->wysiwyg = '<script src="/lib/ckeditor/ckeditor.js" type="text/javascript"></script>
				<script type="text/javascript">
				$(function(){
					var filemanager = \'/lib/ckeditor/filemanager/\';
					var browser = filemanager + \'browser/default/browser.html\';
					var connector = filemanager + \'connectors/php/connector.php\';
					var upload = filemanager + \'connectors/php/upload.php\';
					if($(\'#defin\').length) {
					  CKEDITOR.replace( \'defin\',
					  {    
						customConfig : this.config,
						width:"750",
						filebrowserBrowseUrl : browser +\'?Connector=\' + connector,
						filebrowserImageBrowseUrl : browser + \'?Type=Image&Connector=\' + connector,
						filebrowserFlashBrowseUrl : browser + \'?Type=Flash&Connector=\' + connector,
						filebrowserUploadUrl : upload + \'?type=Files\',
						filebrowserImageUploadUrl : upload + \'?type=Images\',
						filebrowserFlashUploadUrl : upload + \'?type=Flash\'
					  });
					}
				});
				</script>';
				$form->addElement('textarea', 'defin', array('rows'=>10,'columns'=>20,'label' => 'desciption:'));
			break;
			case 'upload':
				// add image upload fields
				$element = new Zend_Form_Element_File('configimage');
				$element->setLabel('Upload Image:')
						->setDestination(APP_BASE_PATH.'/upload/config');
				// ensure only 1 file
				$element->addValidator('Count', false, 1);
				// only JPEG, PNG, and GIFs
				$element->addValidator('Extension', false, 'jpg,psd,gif,png');
				$form->addElement($element, 'configimage');
				$this->view->fldName .= '<br /><strong>Current:</strong> '.$form->getValue('defin');
				$data['curconfigimage'] = $form->getValue('defin');
				$form->addElement('hidden', 'curconfigimage');
			break;
		}
								
		$form->addElement('hidden', 'name');
		$form->addElement('hidden', 'type');
		$form->addElement('hidden', 'cat_id');
		$form->addElement('hidden', 'funct');
		$form->addElement('hidden', 'id');
		
		$form->addElement('image', 'apply', array('src' => '/images/buttons/apply.png'));

		// if post data is valid upload and insert selected spreaksheet
		if ($form->isValid($_POST) && $_POST) {
			
			switch($form->getValue('type')){
				case 'upload':
				// manage existing image if exists
				if($form->configimage->getFileName()){
				  $form->configimage->receive();
				  $define = '/upload/config/'.$this->cleanFile($form->configimage->getFileName());
				} else {
				  $define = '/upload/config/'.$form->getValue('curconfigimage');
				}
				break;
				default:
				  $define = $form->getValue('defin');
				break;
			}
						
			$config->setName($form->getValue('name'));
			$config->setType($form->getValue('type'));
			$config->setDefin($define);
			$config->setCat_id($form->getValue('cat_id'));
			$config->setFunct($form->getValue('funct'));
			$config->setId($form->getValue('id'));
			$configMapper->save($config);
			
			$this->view->form = '<strong>Configuration value has been updated!</strong>';
		// if form data is not valid print form
		} else {
			
			$form->setDefaults($data);	
			
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

