<?php

class AjaxController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
		$this->_helper->layout()->disableLayout();
    }

    public function indexAction()
    {
		$this->_helper->redirector('index','content');
    }
	
	// checks for existing page link_name assignment
	public function checkUrlAction(){
		
		$this->_helper->viewRenderer->setNoRender(true);
		
		// build parent drop down
		$db = Zend_Registry::get('db');
		$select = $db->select()
					->from('routes')
					->where('uri = ?',$this->_request->getPost('link'));
		if($this->_request->getPost('id')) $select->where('id != ?',$this->_request->getPost('id'));
		$select->limit(1);
		$results = $db->fetchRow($select);
		
		if($results[id]){
			$found = 1;
		} else {
			$found = 0;
		}

		echo $found;
	}
	
	public function savePitemAction()
	{
		$this->_helper->viewRenderer->setNoRender(true);
		$PlaylistData = new Application_Model_PlaylistData;
		
		$PlaylistDataMapper = new Application_Model_PlaylistDataMapper;
		$PlaylistData->setPid($this->_request->getPost('pid'));
		$PlaylistData->setVid($this->_request->getPost('vid'));
		$PlaylistDataMapper->save($PlaylistData);
	}
	
	public function updateSortAction()
	{
		
		switch($this->_request->getPost('sort')){
			case 'up':
				$sortVal = +1;
			break;
			case 'down':
				$sortVal = -1;
			break;
		}
		
		$this->_helper->viewRenderer->setNoRender(true);
		$PlaylistData = new Application_Model_PlaylistData;
		
		// gather existing sort info
		$PlaylistDataMapper = new Application_Model_PlaylistDataMapper;
		$PlaylistData->setPid($this->_request->getPost('pid'));
		$PlaylistData->setVid($this->_request->getPost('vid'));
		$PlaylistDataMapper->find($PlaylistData);
		
		// update sort info
		$PlaylistData->setSort_order($PlaylistData->getSort_order()+$sortVal);
		$PlaylistDataMapper->sortVid($PlaylistData);
	}
	
	public function delPitemAction()
	{
		$this->_helper->viewRenderer->setNoRender(true);
		$PlaylistData = new Application_Model_PlaylistData;
		$PlaylistDataMapper = new Application_Model_PlaylistDataMapper;
		$PlaylistDataMapper->delete($this->_request->getPost('pid'),$this->_request->getPost('vid'));
	}
	
	public function gatherVideosAction()
	{
		$PlaylistData = new Application_Model_PlaylistData;
		$PlaylistDataMapper = new Application_Model_PlaylistDataMapper;
		$videos = new Application_Model_Videos;
		$videosMapper = new Application_Model_VideosMapper;
		if($this->_request->getPost('pid')){
			$PlaylistDataAll = $PlaylistDataMapper->fetchAll($this->_request->getPost('pid'));
			$SVids = array();
			$vidNum = 0;
			foreach($PlaylistDataAll as $CPD){
				$videosMapper->find($CPD->vid,$videos);
				if($videos->getLocal() != ''){
				
					$vidPrecLnk = '<a href="/jwplayer/player.swf?file=/upload/video/'.urlencode($videos->getLocal()).'&amp;autostart=1" class="vidPrevSel"><img src="/images/admin/mag.png" alt="preview" /></a>';
				
				} elseif($videos->getRemote() != '') {
				
					$vidPrecLnk = '<a href="/jwplayer/player.swf?file='.urlencode($videos->getRemote()).'&amp;autostart=1" class="vidPrevSel"><img class="vidPrevSel" src="/images/admin/mag.png" alt="preview" /></a>';
				
				} else {
				
					$vidPrecLnk = '';
				
				}
				$SVids[] = '<div class="videoSelBlock">
							<div class="vidName">'.$vidNum.'. '.$videos->getName().'</div>
							<div class="vidPreview">'.$vidPrecLnk.'</div>
							<div class="vidSort"><img class="vidSortDown" id="'.$videos->getId().'" src="/images/admin/downarrow.png" alt="preview" /></div>
							<div class="vidSort"><img class="vidSortUp" id="'.$videos->getId().'" src="/images/admin/uparrow.png" alt="preview" /></div>
							<div class="vidDel"><img class="vidDelSel" id="'.$videos->getId().'" src="/images/admin/icon_redX.png" alt="preview" /></div>
							<div class="clear"></div>
							</div>
							<div class="clear"></div>';
				$vidNum++;
			}
			
			$this->view->vidListFnl = implode("\n",$SVids);			
			
		} else {
			$this->view->vidListFnl = 'No playlist selected!';			
		}
	}
	
	public function searchVideosAction()
	{
		$videos = new Application_Model_Videos;
		$videosMapper = new Application_Model_VideosMapper;
		if($this->_request->getPost('srch')){
			$videosMapperAll = $videosMapper->fetchSearchAll($this->_request->getPost('srch'));
			$SVids = array();
			foreach($videosMapperAll as $CPD){
				if($CPD->local != ''){
					$vidPrecLnk = '<a href="/jwplayer/player.swf?file=/upload/video/'.urlencode($CPD->local).'&amp;autostart=1" class="vidPrevSel"><img src="/images/admin/mag.png" alt="preview" /></a>';
				} elseif($CPD->remote != '') {
					$vidPrecLnk = '<a href="/jwplayer/player.swf?file='.urlencode($CPD->remote).'&amp;autostart=1" class="vidPrevSel"><img src="/images/admin/mag.png" alt="preview" /></a>';
				} else {
					$vidPrecLnk = '';
				}
				$SVids[] = '<div class="videoSelBlock">
							<div class="vidName">'.$CPD->name.'</div>
							<div class="vidPreview">'.$vidPrecLnk.'</div>
							<div class="vidDel"><img class="vidDelAdd" id="'.$CPD->id.'" src="/images/admin/bullet_add.png" alt="preview" /></div>
							</div>';
			}
			
			$this->view->vidListFnl = implode("\n",$SVids);			
			
		} else {
			$this->view->vidListFnl = 'No video found!';			
		}
	}
}
