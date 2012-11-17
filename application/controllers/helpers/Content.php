<?php

class Sc_Action_Helper_Content extends Zend_Controller_Action_Helper_Abstract
{
	public $view;
	
    function direct($request_val)
    {
		$view = $this->getActionController()->view;
		$pages = new Application_Model_Pages;
		$pages->setTitle($request_val);
		$pagesMapper = new Application_Model_PagesMapper;
		$pagesMapper->titleSearch($request_val, $pages);
		// print error if page is not found
		if($pages->getId() == ''){
			$view->headTitle()->prepend('Page Not Found');
			$view->copy_text = '<div class="errorPageMess"><p>Please check to see if the page has moved!</p></div>';
		// if page is found gather copy
		} else {
			// first check for linked file assignment
			if($pages->getFilelnk()){
				// disable output then redirect user
				//$this->_helper->layout()->disableLayout();
				//$this->_helper->viewRenderer->setNoRender(true);
				header('Location: '.$pages->getFilelnk());
			}
//			// load playlist
//			if(!$pages->getJingle()){
//				$playlistRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('Playlist');
//				$view->videoOP = $playlistRenderer->direct($pages->getPlaylist());
//			}
			
			// if file is not found continue loading copy
			if($request_val == 'contact us') {
				$view->headScript()->appendFile('http://maps.google.com/maps/api/js?sensor=false')
									->appendFile('/js/contact.js');
			}
			$view->title = $pages->getTitle();
			$view->copy_text = str_replace(array('<div class="formpad">&nbsp;</div>','<div class="clear">&nbsp;</div>','APPROVAL_CODE'),array('<div class="formpad"></div>','<div class="clear"></div>',(isset($_SESSION['approvalCode']) ? $_SESSION['approvalCode'] : '')),$pages->getCopy_text());
			$view->seo_text = str_replace(array('<div class="clear">&nbsp;</div>'),array('<div class="clear"></div>'),$pages->getSeo_text());
			if($pages->getTitle_tag() != ''){
				$view->headTitle()->prepend($pages->getTitle_tag());
			}
			if($pages->getDesc_tag() != ''){
				$view->headMeta()->appendName('description', $pages->getDesc_tag());	
			}
			if($pages->getKeyword_tag() != ''){
				$view->headMeta()->appendName('keywords', $pages->getKeyword_tag());
			}
		}
    }
	
    public function setView(Zend_View_Interface $view)
    {
        $this->view = $view;
    }
}