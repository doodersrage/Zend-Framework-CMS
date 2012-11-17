<?php

class EmailRedirController extends Zend_Controller_Action
{
	public function init(){
	   $this->_helper->layout()->disableLayout();
	   $this->_helper->viewRenderer->setNoRender(true);
	}
	
	function indexAction(){
		switch($this->getRequest()->getParam('emailClub')){
			case 'Ghent':
				$location = 'https://ynot-norfolk.foodtecsolutions.com/signup';
			break;
			case 'Great Neck':
				$location = 'https://ynot-colonial.foodtecsolutions.com/signup';
			break;
			case 'Kempsville':
				$location = 'https://ynot-vbeach.foodtecsolutions.com/signup';
			break;
			case 'Greenbrier':
				$location = 'https://ynot-chesapeake.foodtecsolutions.com/signup';
			break;
		};
		
		header('Location: '.$location);
		exit;
	}
}