<?php

class FormProcController extends Zend_Controller_Action
{

    public function init()
    {
    }
	
	public function indexAction()
    {
	}
	
	public function dynamicProcAction(){
		//n load constant contac classes
		include_once(APP_BASE_PATH.'/lib/cc/cc_class.php');
		$ccContactOBJ = new CC_Contact();
		$ccListOBJ = new CC_List(); 
		$disabled = null;

		$db = Zend_Registry::get('db');
		$forms = new Application_Model_Forms;
		$formsMapper = new Application_Model_FormsMapper;
		$form_id = $this->_request->getPost('form_id');
		$formsMapper->find($form_id,$forms);	
		
		// gather form fields data
		$select = $db->select()
					->from('form_fields')
					->where('form_id = ?',$form_id);
		$select->order('order_val DESC');
		$select->order('name DESC');
		$results = $db->fetchAll($select);
		
		// check fields for errors
		$errors = array();
		$formResults = array();
		foreach($results as $id => $val){
			$fieldName = strtolower(preg_replace("/[^A-Za-z0-9\s\s+]/","-",$val[name]));
			
			// added for contact form constant contact add
			if($fieldName == 'sign-up-for-offers-' && trim($this->_request->getPost($fieldName)) == 'yes'){
				$postFields["first_name"] = $this->_request->getPost('name');
				$postFields["email_address"] = $this->_request->getPost('email');
				$postFields["country_code"] = 'US';
				$postFields["mail_type"] = 'HTML';
				$postFields["lists"] = array('http://api.constantcontact.com/ws/customers/mulletman/lists/8');
					
				$contactXML = $ccContactOBJ->createContactXML(null,$postFields);
				$ccContactOBJ->addSubscriber($contactXML);
			}
			
			if($val[type] == 'fileupload'){
				if($_FILES[$fieldName]["error"] > 0 && $val[required] == 1){
						$errors[] = $val[name];
				} else {
					$fileName = time().'_'.$_FILES[$fieldName]["name"];
					if (!file_exists(APP_BASE_PATH.'upload/forms/'.$_FILES[$fieldName]["name"])) {
						move_uploaded_file($_FILES[$fieldName]["tmp_name"], APP_BASE_PATH.'/upload/forms/'.$fileName);
					}		
					$formResults[$val[name]] = 'http://'.$_SERVER["HTTP_HOST"].'/upload/forms/'.$fileName;
				}
			} else {
				if(!is_array($this->_request->getPost($fieldName))){
					if($this->_request->getPost($fieldName) == '' && $val[required] == 1){
						$errors[] = $val[name];
					} else {
						$formResults[$val[name]] = $this->_request->getPost($fieldName);
					}
				} else {
					foreach($this->_request->getPost($fieldName) as $curId => $curVal){
						$formResults[$val[name]][$curId] = $curVal;
					}
				}
			}
		}
		
		// create and send email if address is assigned
		if($forms->getEmail() != '' && count($errors) == 0){
			
			$bodyText = '';
			foreach($formResults as $id => $val){
				$bodyText .= $id.': ' . $val . "\n";
			}
	
			// send email
			$mail = new Zend_Mail();
			$mail->setBodyText($bodyText);
			$mail->setFrom(Zend_Registry::get('Contact Email'), $forms->getName());
			$mail->addTo($forms->getEmail(), 'Recipient');
			$mail->setSubject($forms->getName());
			$mail->send();
			
		}
		
		if(count($errors) > 0){
			
			$errorMessage = '<p>Errors were found with the following submitted fields: '.(implode(', ',$errors)).'</p>';
			$this->view->message = $errorMessage;
			
		} else {
				
			$formsubs = new Application_Model_FormSubs;
			$formsubsMapper = new Application_Model_FormSubsMapper;
			$formsubs->setValues(serialize($formResults));
			$formsubs->setType($forms->getName());
			$formsubsMapper->save($formsubs);
			
			$this->view->message = $forms->getMessage();
			
		}
		if(Zend_Registry::get('mobile') == false){
			$this->view->minifyHeadLink()->appendStylesheet('/js/fancybox/jquery.fancybox-1.3.4.css');
			$this->view->minifyHeadScript()->appendFile('/js/fancybox/jquery.fancybox-1.3.4.pack.js');
			$this->view->minifyHeadLink()->appendStylesheet('/css/content.css');
			$this->view->headScript()->appendFile('/js/content.js');
			
			$select = $db->select()
						->from('inventory');
			$select->order('RAND()');
			$select->limit('10');
			$this->view->smallItems = $db->fetchAll($select);
	
			$select = $db->select()
						->from('inventory');
			$select->order('RAND()');
			$select->limit('3');
			$this->view->rightItems = $db->fetchAll($select);
		} else {
			$this->_helper->layout->setLayout('mobile');
			$this->_helper->viewRenderer->setRender('mobile');
		}
	}
			
	public function contactPostAction()
    {
        /* Initialize action controller here */
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		// set post vars
		$shareMessage = $this->_request->getPost('message');
		$shareFindUs = $this->_request->getPost('findUs');
		$sharePhone = $this->_request->getPost('phone');
		$shareZip = $this->_request->getPost('zip');
		$shareName = $this->_request->getPost('name');
		$shareEmail = $this->_request->getPost('email');
		$sharePublish = $this->_request->getPost('publish');
		$shareReply = $this->_request->getPost('reply');
		$shareOffers = $this->_request->getPost('offers');
		
		// serialize vals for db storage
		$storedVals = serialize($this->_request->getPost());
		
		$formsubs = new Application_Model_FormSubs;
		$formsubsMapper = new Application_Model_FormSubsMapper;
		$formsubs->setValues($storedVals);
		$formsubs->setType('contact');
		$formsubsMapper->save($formsubs);
		
		// send email
		$mail = new Zend_Mail();
		
		$bodyText = 'Message: ' . $shareMessage . "\n";
		$bodyText .= 'How did you find us?: ' . $shareFindUs . "\n";
		$bodyText .= 'Phone: ' . $sharePhone . "\n";
		$bodyText .= 'Zip: ' . $shareZip . "\n";
		$bodyText .= 'Name: ' . $shareName . "\n";
		$bodyText .= 'Email: ' . $shareEmail . "\n";
		$bodyText .= 'Allow Publish?: ' . $sharePublish . "\n";
		$bodyText .= 'Allow Reply?: ' . $shareReply . "\n";
		$bodyText .= 'Sign up or offers?: ' . $shareOffers . "\n";
		
		$mail->setBodyText($bodyText);
		$mail->setFrom(Zend_Registry::get('Contact Email'), 'Website Contact Form Submission');
		$mail->addTo(Zend_Registry::get('Contact Email'), 'Recipient');
		$mail->setSubject('Website Contact Form Submission');
		//$mail->send();
		
		$this->_helper->redirector->gotoUrl('/content/thank-you/');
		
	}

	// added for hello bar
	public function helloAction()
    {
		//n load constant contac classes
		include_once(APP_BASE_PATH.'/lib/cc/cc_class.php');
		$ccContactOBJ = new CC_Contact();
		$ccListOBJ = new CC_List(); 
		$disabled = null;
		
       /* Initialize action controller here */
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		// set post vars
		$shareName = trim($_POST['nameFld']);
		$shareEmail = trim($_POST['emailFld']);
		
		if($shareName && $shareEmail){
			// serialize vals for db storage
			$storedVals = serialize($this->_request->getPost());
			
			$formsubs = new Application_Model_FormSubs;
			$formsubsMapper = new Application_Model_FormSubsMapper;
			$formsubs->setValues($storedVals);
			$formsubs->setType('hello bar');
			$formsubsMapper->save($formsubs);
			
			$postFields["first_name"] = $shareName;
			$postFields["email_address"] = $shareEmail;
			$postFields["country_code"] = 'US';
			$postFields["mail_type"] = 'HTML';
			$postFields["lists"] = array('http://api.constantcontact.com/ws/customers/mulletman/lists/7');
				
			$contactXML = $ccContactOBJ->createContactXML(null,$postFields);
			$ccContactOBJ->addSubscriber($contactXML);
				
			// send email
			$mail = new Zend_Mail();
			
			$bodyText .= 'Name: ' . $shareName . "\n";
			$bodyText .= 'Email: ' . $shareEmail . "\n";
			$mail->setBodyText($bodyText);
			$mail->setFrom(Zend_Registry::get('Contact Email'), 'Hello Bar Contest Form Submission');
			$mail->addTo(Zend_Registry::get('Contact Email'), 'Recipient');
			$mail->setSubject('Hello Bar Contest Form Submission');
			//$mail->send();
			
			$_SESSION['hello'] = 'submitted';
		}
		
		
	}

}