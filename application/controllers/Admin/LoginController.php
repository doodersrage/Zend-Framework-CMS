<?php

class Admin_LoginController extends Zend_Controller_Action
{
	private $rest;
	
    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->layout->setLayout('admin');
		$this->view->headTitle()->prepend('Admin');
		
		$storage = new Zend_Auth_Storage_Session();
        $data = $storage->read();
        if(!empty($data)){
			if($this->getRequest()->getParam('logout') == 1){
				$storage = new Zend_Auth_Storage_Session();
				$storage->clear();
			}
			$this->_helper->redirector('index','admin_index');			
		}
		
    }
	
	public function indexAction(){
		$this->view->headTitle()->prepend('Login');
		$storage = new Zend_Auth_Storage_Session();
        $data = $storage->read();
        if($data){
			$this->_helper->redirector('index','admin_index');
        }
		$form = new Zend_Form;
		$form->setAction('/admin_login/')
			 ->setMethod('post')
			 ->setAttrib('name', 'inventEdit')
			 ->setAttrib('id', 'loginForm')
			 ->setAttrib('enctype', 'multipart/form-data');
			 
		$username = $form->createElement('text','username');
        $username->setLabel('Username: *')
                ->setRequired(true);
                
        $password = $form->createElement('password','password');
        $password->setLabel('Password: *')
                ->setRequired(true);
                
        $signin = $form->createElement('submit','signin');
        $signin->setLabel('Sign in')
                ->setIgnore(true);
                
        $form->addElements(array(
                        $username,
                        $password,
                        $signin,
        ));
        $users = new Application_Model_Users;
		$usersMapper = new Application_Model_UsersMapper;
		$dbAdapter = Zend_Db_Table::getDefaultAdapter();
        if($this->getRequest()->isPost()){
            if($form->isValid($_POST)){
                $data = $form->getValues();
                $auth = Zend_Auth::getInstance();
                $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter,'users');
                $authAdapter->setIdentityColumn('username')
                            ->setCredentialColumn('password');
                $authAdapter->setIdentity($data['username'])
                            ->setCredential($this->_helper->pass($data['password']));
                $result = $auth->authenticate($authAdapter);
                if($result->isValid()){
                    $storage = new Zend_Auth_Storage_Session();
                    $storage->write($authAdapter->getResultRowObject());
                    $this->_helper->redirector('index','admin_index');
                } else {
                    $this->view->errorMessage = "<div id=\"message\">Invalid username or password. Please try again.</div>";
                }         
            }
        }
		$this->view->form = $form->render();
	}
	
}

