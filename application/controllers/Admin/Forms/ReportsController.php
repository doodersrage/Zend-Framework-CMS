<?php

class Admin_Forms_ReportsController extends Zend_Controller_Action
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
	
	// bof form submissions section actions
	
    public function indexAction()
    {
		if($this->rest[6] == 1){
			$this->view->headTitle()->prepend('Review Form Submissions');
			$formsubs = new Application_Model_FormSubs;
			$formsubsMapper = new Application_Model_FormSubsMapper;
			
			// check for delete action
			if($this->_request->getPost('delete')){
				if(is_array($this->_request->getPost('delete'))){
					foreach($this->_request->getPost('delete') as $curDel){
						$formsubsMapper->delete($curDel,$formsubs);
					}
				}
			}
			
			// display listing
			$db = Zend_Registry::get('db');
			$select = $db->select()
						->from('form_subs');
			$select->order('date DESC');
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
    }
	
	public function downloadAction()
    {
		$this->_helper->layout()->disableLayout();
		header("Content-Type: text/comma-seperated-values");
		header("Content-Disposition: inline; filename=\"forms.csv\";");
		$db = Zend_Registry::get('db');
		// get type list
		$select = $db->select()
					 ->from(array('fs' => 'form_subs'))
					 ->where('type = ?',$this->getRequest()->getParam('type'));
		$select->order('date DESC');
		$select->limit(1);
		$results = $db->fetchAll($select);
		$fields = unserialize($results[0][values]);
		
		// print header row
		foreach($fields as $id => $field){
			switch($id){
				case 'x':
				case 'y':
				break;
				default:
					echo $id.', ';
				break;
			}
		}
		echo "\n";
		
		// get type list
		$select = $db->select()
					 ->from(array('fs' => 'form_subs'))
					 ->where('type = ?',$this->getRequest()->getParam('type'));
		$select->order('date DESC');
		$results = $db->fetchAll($select);
		
		foreach($results as $resItem){
			$fields = unserialize($resItem[values]);
			foreach($fields as $id => $field){
				switch($id){
					case 'x':
					case 'y':
					break;
					default:
						echo $field.', ';
					break;
				}
			}
			echo "\n";
		}
	}
	
    public function reportsAction()
    {
		if($this->rest[6] == 1){
			$this->view->headTitle()->prepend('Form Reports');
			$formsubs = new Application_Model_FormSubs;
			$formsubsMapper = new Application_Model_FormSubsMapper;
			$db = Zend_Registry::get('db');
			
			// get type list
			$select = $db->select()
						 ->distinct()
						 ->from(array('fs' => 'form_subs'), 'type');
			$select->order('date DESC');
			$results = $db->fetchAll($select);
			$this->view->types = $results;
			$this->view->download = '/admin_forms_reports/download/type/'.$results[0][type].'/';
			
			// if only one form type gather default form field values
			if(count($results) == 1){
				$select = $db->select()
							 ->from(array('fs' => 'form_subs'))
							 ->where('type = ?',$results[0][type]);
				$select->order('date DESC');
				$select->limit(1);
				$results = $db->fetchAll($select);
				$this->view->fields = unserialize($results[0][values]);
			} elseif(count($results) > 1 && $this->_request->getPost('type')) {
				// get type list
				$select = $db->select()
							 ->from(array('fs' => 'form_subs'))
							 ->where('type = ?',$this->_request->getPost('type'));
				$select->order('date DESC');
				$select->limit(1);
				$results = $db->fetchAll($select);
				$this->view->fields = unserialize($results[values]);
				$this->view->download = '/admin_form/download/?type='.$this->_request->getPost('type');
			}
			
			if($this->_request->getPost('fields')){
				// display listing
				$select = $db->select()
							->from('form_subs');
				$select->order('date DESC');
				$results = $db->fetchAll($select);
				//print_r($results);
				// gather and assemble report data
				$values = array();
				foreach($results as $id => $value){
					$formVals = unserialize($value[values]);
					foreach($formVals as $subID => $subVal){
						if($subID == $this->_request->getPost('fields')) $values[$subVal] = (empty($values[$subVal]) ? 1 : $values[$subVal] = $values[$subVal] + 1);
					}
				}
				
				$fieldNames = array();
				foreach($values as $id => $value){
					$fieldNames[] = $id;
				}
				//print_r($values);
				$this->view->results = '<table id="formResults"><CAPTION><EM>Form Submission Results.</EM></CAPTION><tr><th>Value</th><th>Total</th></tr>';
				foreach($fieldNames as $field){
					$this->view->results .= '<tr><td>'.$field.'</td><td>'.number_format($values[$field]).'</td></tr>';
				}
				$this->view->results .= '</table>';
				
				$this->view->chart = '<img src="https://chart.googleapis.com/chart?chs=700x250&amp;chd=t:'.implode(',',$values).'&amp;cht=p3&amp;chl='.implode('%7C',$fieldNames).'" alt="Yellow pie chart" border="1">';
				$this->view->chart .= '<br><img src="http://chart.apis.google.com/chart?chs=700x250&cht=p&chd=t:'.implode(',',$values).'&chl='.implode('%7C',$fieldNames).'" />';
			}
		}
	}
	
    public function submissionsViewAction()
    {
		if($this->rest[6] == 1){
			$this->view->headTitle()->prepend('Form Submissions View');
			$formsubs = new Application_Model_FormSubs;
			$formsubsMapper = new Application_Model_FormSubsMapper;
			if($this->getRequest()->getParam('id')){
				$formsubsMapper->find($this->getRequest()->getParam('id'),$formsubs);
				$this->view->formdata = $formsubs->getValues();
			}
		}
	}
	
	// eof form submissions section actions
	
}

