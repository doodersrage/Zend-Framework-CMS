<?php

class ErrorController extends Zend_Controller_Action
{
	public function init()
    {
 		$this->view->headTitle(Zend_Registry::get('404 Page Title Tag'));
		
		// check for redirect is exists
		// perform redirect if found
		$db = Zend_Registry::get('db');
		
		$uri = $_SERVER["REQUEST_URI"];
		// check for existing redirect for current URI request
		$select = $db->select()
					->from('redirects')
					->where('olduri = ?',$uri);
		$results = $db->fetchRow($select);
		
		// redirect the user of a redirect is found
		if($results){
			$this->_redirect($results[newuri], array('code'=>301)); 
		}
	}
	
    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');
        
        if (!$errors) {
            $this->view->message = 'You have reached the error page';
            return;
        }
        
        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
        
                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $this->view->message = 'Page not found';
                break;
            default:
                // application error
                $this->getResponse()->setHttpResponseCode(500);
                $this->view->message = 'Application error';
                break;
        }
        
        // Log exception, if logger available
        if ($log = $this->getLog()) {
            $log->crit($this->view->message, $errors->exception);
        }
        
        // conditionally display exceptions
        if ($this->getInvokeArg('displayExceptions') == true) {
            $this->view->exception = $errors->exception;
        }
        
        $this->view->request   = $errors->request;
    }

    public function getLog()
    {
        $bootstrap = $this->getInvokeArg('bootstrap');
        if (!$bootstrap->hasResource('Log')) {
            return false;
        }
        $log = $bootstrap->getResource('Log');
        return $log;
    }


}

