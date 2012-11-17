
<?php

class Sc_Action_Helper_Playlist extends Zend_Controller_Action_Helper_Abstract
{
	public $view;
	
    function direct($request_val = NULL)
    {
		$view = $this->getActionController()->view;
		$Playlist = new Application_Model_Playlist;
		$PlaylistMapper = new Application_Model_PlaylistMapper;
		$PlaylistMapper->find($request_val,$Playlist);
		$PlaylistData = new Application_Model_PlaylistData;
		$PlaylistDataMapper = new Application_Model_PlaylistDataMapper;
		$videos = new Application_Model_Videos;
		$videosMapper = new Application_Model_VideosMapper;
		$SVids = array();
		if(!empty($request_val)){

			$PlaylistDataAll = $PlaylistDataMapper->fetchAll($request_val);
			if(Zend_Registry::get('mobile') == false){
				foreach($PlaylistDataAll as $CPD){
					$videosMapper->find($CPD->vid,$videos);
					if($videos->getLocal() != ''){
					
						$vidPrecLnk = '/upload/video/'.$videos->getLocal();
					
					} elseif($videos->getRemote() != '') {
					
						$vidPrecLnk = $videos->getRemote();
					
					} else {
					
						$vidPrecLnk = '';
					
					}
					$SVids[] = '{ file: "'.$vidPrecLnk.'" }';
				}
				
				$videoOP = '<div id="videobox'.$request_val.'" class="videobox'.$request_val.'">					
								<div id="video-container" class="video_cont"></div>
								<script type="text/javascript">
								//<![CDATA[
								$(function() {
								jwplayer("video-container").setup({'."\n";
				$videoOP .=			($Playlist->autoplay == 1 ? 'autostart: true,'."\n" : '');
				$videoOP .=			'players: [
									{ type: "flash", src: "/jwplayer/player.swf" },
									{ type: "html5" }
									],
									playlist: [
									'.implode(",\n",$SVids).'
									],
									repeat: "list",'."\n";
				$videoOP .=			($Playlist->controlbar != 1 ? 'controlbar: "true",'."\n" : '');
				$videoOP .=			'allowscriptaccess: "always",
									autoscroll: true,
									flashVersion: "10.0.0",'."\n";
				$videoOP .=			($Playlist->playlistvisual == 1 ? '"playlist.position": "right",
									"playlist.size": "100",'."\n" : '');
				$videoOP .=			'height: '.$Playlist->height.',
									width: '.($Playlist->width+100).'
								});
								var player = document.getElementById(\'video-container\');
								});    //]]>
								</script>
								</div>
							</div>';
			} else {
				foreach($PlaylistDataAll as $CPD){
					$videosMapper->find($CPD->vid,$videos);
					if($videos->getLocal() != ''){
					
						$vidPrecLnk = '/upload/video/'.$videos->getLocal();
					
					} elseif($videos->getRemote() != '') {
					
						$vidPrecLnk = $videos->getRemote();
					
					} else {
					
						$vidPrecLnk = '';
					
					}
					$SVids[] = '<a href="'.$vidPrecLnk.'"><img src="/image/?image=/upload/video/images/'.$videos->image.'&height=200&width=300" alt="'.$videos->name.'"/></a>';
				}
				
				$videoOP='<div id="video-container" class="video_cont">'.implode(",\n",$SVids).'</div>';
			}
		}
	return $videoOP;
	}
	
    public function setView(Zend_View_Interface $view)
    {
        $this->view = $view;
    }
}