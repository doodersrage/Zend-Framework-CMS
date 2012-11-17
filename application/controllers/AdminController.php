<?php

class AdminController extends Zend_Controller_Action
{
	private $rest;
	
    public function init()
    {
 		$this->view->headTitle(Zend_Registry::get('Default Page Title'));
    }
	
    public function indexAction()
    {
		header("Location: http://".$_SERVER['HTTP_HOST']."/admin_index/");
	}
	
}

