<?php

class Admin_InventoryController extends Zend_Controller_Action
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
	
	// bof inventory section actions
    public function indexAction()
    {
		if($this->rest[9] == 1){
		$this->view->headTitle()->prepend('Inventory');
		$inventory = new Application_Model_Inventory;
		$inventoryMapper = new Application_Model_InventoryMapper;
		
		// check for delete action
		if($this->_request->getPost('delete')){
			if(is_array($this->_request->getPost('delete'))){
				foreach($this->_request->getPost('delete') as $curDel){
					$inventoryMapper->delete($curDel,$inventory);
				}
			}
		}
		
		// display listing
		if($this->getRequest()->getParam('clearFilter') == 1){
			unset($_SESSION['filterOption']);
			unset($_SESSION['filterText']);
		}
		if($this->_request->getPost('filterOption')){
			// set session vals for multi page results
			$_SESSION['filterOption'] = $this->_request->getPost('filterOption');
			$_SESSION['filterText'] = $this->_request->getPost('filterText');
		}
		if($_SESSION['filterOption']){
			
			$db = Zend_Registry::get('db');
			// Create the Zend_Db_Select object
			$select = $db->select()
						->from('inventory');
			$select->order('stockNum ASC');
			
			switch($_SESSION['filterOption']){
				case 'stock #':
					$select->where('stockNum like ?', '%'.$_SESSION['filterText'].'%');
				break;
				case 'year':
					$select->where('year like ?', '%'.$_SESSION['filterText'].'%');
				break;
				case 'make':
					$select->where('make like ?', '%'.$_SESSION['filterText'].'%');
				break;
				case 'model':
					$select->where('model like ?', '%'.$_SESSION['filterText'].'%');
				break;
				case 'style':
					$select->where('style like ?', '%'.$_SESSION['filterText'].'%');
				break;
				case 'color':
					$select->where('color like ?', '%'.$_SESSION['filterText'].'%');
				break;
				case 'vin':
					$select->where('vin like ?', '%'.$_SESSION['filterText'].'%');
				break;
				case 'name':
					$select->where('name like ?', '%'.$_SESSION['filterText'].'%');
				break;
				case 'price':
					$select->where('price like ?', '%'.$_SESSION['filterText'].'%');
				break;
			}

			$results = $db->fetchAll($select);
		} else {
			$db = Zend_Registry::get('db');
			$select = $db->select()
						->from('inventory');
			$select->order('stockNum ASC');
			$results = $db->fetchAll($select);
		}
		
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
		if($this->rest[9] == 1){
			$this->view->headTitle()->prepend('Edit Inventory');
			$form = new Zend_Form;
		 
			$inventory = new Application_Model_Inventory;
			$inventoryMapper = new Application_Model_InventoryMapper;
			
			$form->setAction('/admin_inventory/edit/')
				 ->setMethod('post')
				 ->setAttrib('name', 'inventEdit')
				 ->setAttrib('enctype', 'multipart/form-data');
	
			$form->addElement('text', 'stockNum', array('label' => 'Stock #:'));
//			$form->addElement('text', 'year', array('required' => true,'label' => 'Year:'));
//			$form->addElement('text', 'make', array('required' => true,'label' => 'Make:'));
//			$form->addElement('text', 'model', array('required' => true,'label' => 'Model:'));
//			$form->addElement('text', 'bodyStyle', array('required' => true,'label' => 'Body Style:','size'=>'80'));
//			$form->addElement('text', 'color', array('required' => true,'label' => 'Color:'));
//			$form->addElement('text', 'vin', array('required' => true,'label' => 'VIN:'));
			$form->addElement('text', 'name', array('required' => true,'label' => 'Name:'));
			$form->addElement('text', 'price', array('required' => true,'label' => 'Price:'));
			$form->addElement('textarea', 'desciption', array('rows'=>10,'columns'=>20,'label' => 'desciption:'));
			
			$form->addElement('hidden', 'currentImages');
	
			// add image upload fields
			$element = new Zend_Form_Element_File('upload1');
			$element->setLabel('Upload Image 1:')
					->setDestination(APP_BASE_PATH.'/upload/invent/images');
			// ensure only 1 file
			$element->addValidator('Count', false, 1);
			// limit to 100K
			//$element->addValidator('Size', false, 404800);
			// only JPEG, PNG, and GIFs
			$element->addValidator('Extension', false, 'jpg,psd,gif,png');
			$form->addElement($element, 'upload1');
			
//			$element = new Zend_Form_Element_File('upload2');
//			$element->setLabel('Upload Image 2:')
//					->setDestination(APP_BASE_PATH.'/upload/invent/images');
//			// ensure only 1 file
//			$element->addValidator('Count', false, 1);
//			// limit to 100K
//			//$element->addValidator('Size', false, 404800);
//			// only JPEG, PNG, and GIFs
//			$element->addValidator('Extension', false, 'jpg,psd,gif');
//			$form->addElement($element, 'upload2');
//			
//			$element = new Zend_Form_Element_File('upload3');
//			$element->setLabel('Upload Image 3:')
//					->setDestination(APP_BASE_PATH.'/upload/invent/images');
//			// ensure only 1 file
//			$element->addValidator('Count', false, 1);
//			// limit to 100K
//			//$element->addValidator('Size', false, 404800);
//			// only JPEG, PNG, and GIFs
//			$element->addValidator('Extension', false, 'jpg,psd,gif');
//			$form->addElement($element, 'upload3');
//			
//			$element = new Zend_Form_Element_File('upload4');
//			$element->setLabel('Upload Image 4:')
//					->setDestination(APP_BASE_PATH.'/upload/invent/images');
//			// ensure only 1 file
//			$element->addValidator('Count', false, 1);
//			// limit to 100K
//			//$element->addValidator('Size', false, 404800);
//			// only JPEG, PNG, and GIFs
//			$element->addValidator('Extension', false, 'jpg,psd,gif');
//			$form->addElement($element, 'upload4');
			
			$form->addElement('image', 'apply', array('src' => '/images/buttons/apply.png'));
			
			// if post data is valid upload and insert selected spreaksheet
			if ($form->isValid($_POST)) {
				// upload new images if set
				$form->upload1->receive();
//				$form->upload2->receive();
//				$form->upload3->receive();
//				$form->upload4->receive();
				
				// manage existing image list if exists
				if($form->getValue('currentImages') != ''){
					$imagesArr = explode(';',$form->getValue('currentImages'));
				} else {
					$imagesArr = '';
				}
							
				if($form->upload1->getFileName()){
					$imagesArr[0] = $this->cleanFile($form->upload1->getFileName());
				}
//				if($form->upload2->getFileName()){
//					$imagesArr[1] = $this->cleanFile($form->upload2->getFileName());
//				}
//				if($form->upload3->getFileName()){
//					$imagesArr[2] = $this->cleanFile($form->upload3->getFileName());
//				}
//				if($form->upload4->getFileName()){
//					$imagesArr[3] = $this->cleanFile($form->upload4->getFileName());
//				}
				
				$imagesArr = implode(';',$imagesArr);
	
				$inventory->setStockNum($form->getValue('stockNum'));
				$inventory->setYear($form->getValue('year'));
				$inventory->setMake($form->getValue('make'));
				$inventory->setModel($form->getValue('model'));
				$inventory->setStyle($form->getValue('bodyStyle'));
				$inventory->setColor($form->getValue('color'));
				$inventory->setVin($form->getValue('vin'));
				$inventory->setName($form->getValue('name'));
				$inventory->setPrice($form->getValue('price'));
				$inventory->setDesciption($form->getValue('desciption'));
				$inventory->setImages($imagesArr);
				$inventoryMapper->save($inventory);
				
				$this->view->form = '<strong>Inventory has been updated!</strong>';
			// if form data is not valid print form
			} else {
				
				if($this->getRequest()->getParam('id') && $form->getValue('stockNum') == ''){
					$inventoryMapper->find($this->getRequest()->getParam('id'),$inventory);
					$data = array(
								'stockNum'=>$inventory->getStockNum(),
								'year'=>$inventory->getYear(),
								'make'=>$inventory->getMake(),
								'model'=>$inventory->getModel(),
								'bodyStyle'=>$inventory->getStyle(),
								'color'=>$inventory->getColor(),
								'vin'=>$inventory->getVin(),
								'name'=>$inventory->getName(),
								'price'=>$inventory->getPrice(),
								'desciption'=>$inventory->getDesciption(),
								'currentImages'=>$inventory->getImages(),
								);
		
					$form->setDefaults($data);
					
					$imagesArr = explode(';',$inventory->getImages());
					if(!empty($imagesArr[0])) $form->getElement('upload1')->setLabel('Upload Image 1: Current: '.$imagesArr[0]);
//					if(!empty($imagesArr[1])) $form->getElement('upload2')->setLabel('Upload Image 2: Current: '.$imagesArr[1]);
//					if(!empty($imagesArr[2])) $form->getElement('upload3')->setLabel('Upload Image 3: Current: '.$imagesArr[2]);
//					if(!empty($imagesArr[3])) $form->getElement('upload4')->setLabel('Upload Image 4: Current: '.$imagesArr[3]);
					
					
				}
				$this->view->form = $form->render();
				
				// populate preview image field
				if($inventory->getImages()){
					$this->view->images = $inventory->getImages();
				} else {
					$this->view->images = '';
				}
			}
		}
	}
	
	// cleans uploaded file for file name
	private function cleanFile($file){
		$file = explode('/',$file);
		$pcnt = count($file)-1;
		
	return $file[$pcnt];
	}
	
	public function downloadAction()
    {
		$this->_helper->layout()->disableLayout();
		header("Content-Type: text/comma-seperated-values");
		header("Content-Disposition: inline; filename=\"inventory.csv\";");
		$db = Zend_Registry::get('db');
		$select = $db->select()
					->from('inventory');
		$select->order('stockNum ASC');
		$results = $db->fetchAll($select);
		
		// print header row
		echo 'STOCK #, ';
		echo 'YEAR, ';
		echo 'VEHICLE MAKE, ';
		echo 'BODY, ';
		echo 'BODY STYLE, ';
		echo 'COLOR, ';
		echo 'VIN, ';
		echo 'NAME, ';
		echo 'PRICE, ';
		echo 'DESCRIPTION'."\n";
		
		foreach($results as $resItem){
			echo $resItem[stockNum].', ';
			echo $resItem[year].', ';
			echo $resItem[make].', ';
			echo $resItem[model].', ';
			echo $resItem[style].', ';
			echo $resItem[color].', ';
			echo $resItem[vin].', ';
			echo $resItem[images].', ';
			echo $resItem[name].', ';
			echo $resItem[price].', ';
			echo $resItem[description]."\n";
		}
	}
	
	public function uploadAction()
    {
		if($this->rest[9] == 1){
			$this->view->headTitle()->prepend('Upload Inventory');
			$form = new Zend_Form;
			$form->setAction('/admin_inventory/upload/')
				 ->setMethod('post')
				 ->setAttrib('name', 'inventoryUpload')
				 ->setAttrib('enctype', 'multipart/form-data');
			
			$element = new Zend_Form_Element_File('upload');
					
			$element->setLabel('Upload CSV:')
					->setDestination(APPLICATION_PATH.'/upload');
			// ensure only 1 file
			$element->addValidator('Count', false, 1);
			// limit to 100K
			//$element->addValidator('Size', false, 102400);
			// only JPEG, PNG, and GIFs
			$element->addValidator('Extension', false, 'csv');
			$form->addElement($element, 'upload');
			
			$form->addElement('image', 'apply', array('order' => 2, 'src' => '/images/buttons/apply.png'));
			
			// if post data is valid upload and insert selected spreaksheet
			if ($form->isValid($_POST)) {
				$values = $form->getValues();
				if (!$form->upload->receive()) {
					$this->view->form = "Upload error!";
				} else {
					$file = new SplFileObject($form->upload->getFileName());
					//$view = $this->getActionController()->view;
					$inventory = new Application_Model_Inventory;
					$inventoryMapper = new Application_Model_InventoryMapper;
					$insCount = 0;
					$walkCnt = 0;
					foreach ($file as $line) {
						$walkCnt++;
						$line = explode(',',$line);
						if((!empty($line[0]) && !empty($line[1]) && !empty($line[2]) && !empty($line[3]) && !empty($line[4]) && !empty($line[5]) && !empty($line[6])) && $walkCnt > 1){
						  $inventory->setStockNum($line[0]);
						  $inventory->setYear($line[1]);
						  $inventory->setMake($line[2]);
						  $inventory->setModel($line[3]);
						  $inventory->setStyle($line[4]);
						  $inventory->setColor($line[5]);
						  $inventory->setVin($line[6]);
						  $inventory->setImages($line[7]);
						  $inventory->setName($line[8]);
						  $inventory->setPrice($line[9]);
						  $inventory->setDescription($line[10]);
						  $inventoryMapper->save($inventory);
						  $insCount++;
						}
					}
					unlink($form->upload->getFileName());
					$this->view->form = "File uploaded! ".$insCount." Inventory items inserted or updated!";
				}
			
			// if form data is not valid print form
			} else {
				$this->view->form = $form;
			}
		}
	}

	// eof inventory section actions
	
}

