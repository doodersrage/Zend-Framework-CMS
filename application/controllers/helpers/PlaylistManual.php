<?php

class Sc_Action_Helper_PlaylistManual extends Zend_Controller_Action_Helper_Abstract
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
			$curVid = 0;
			foreach($PlaylistDataAll as $CPD){
				if($curVid <= 2){
					$videosMapper->find($CPD->vid,$videos);
					if($videos->getLocal() != ''){
					
						$vidPrecLnk = '/upload/video/'.$videos->getLocal();
					
					} elseif($videos->getRemote() != '') {
					
						$vidPrecLnk = $videos->getRemote();
					
					} else {
					
						$vidPrecLnk = '';
					
					}
					$SVids[$curVid][vid] = '{ file: "'.$vidPrecLnk.'" }';
					$SVids[$curVid][img] = $videos->image;
					$SVids[$curVid][name] = $videos->name;
					$SVids[$curVid][desc] = $videos->description;
				}
				$curVid++;
			}
			
			$videoOP = '';
			foreach($SVids as $id => $item){
			  $videoOP .= '<div class="videoDesckTitle" id="vidTitle'.$id.'"'.($id == 0 ? ' style="display:block" ' : ' style="display:none" ').'>
							   <p>'.substr($item[name],0,41).'</p>
						  </div>';
			}
			$videoOP .= '<div id="videoDeckWrap">
							<div id="videoBlockWrap">
								<div id="videoBlockLeft">
									<div id="videobox'.$request_val.'" class="videobox'.$request_val.'">					
										<div id="video-container" class="video_cont"></div>
										<script type="text/javascript">
										//<![CDATA[
										$(function() {
										jwplayer("video-container").setup({'."\n";
					$videoOP .=				($Playlist->autoplay == 1 ? 'autostart: true,'."\n" : '');
					$videoOP .=				'players: [
											{ type: "flash", src: "/jwplayer/player.swf" },
											{ type: "html5" }
											],
											playlist: ['."\n";
											$vidsArr = array();
											foreach($SVids as $id => $item){
												$vidsArr[] = $item[vid];
											}
					$videoOP .=				implode(",\n",$vidsArr)."\n".
											'],
											repeat: "list",'."\n";
					$videoOP .=				($Playlist->controlbar != 1 ? 'controlbar: "true",'."\n" : '');
					$videoOP .=				'allowscriptaccess: "always",
											autoscroll: true,
											flashVersion: "10.0.0",'."\n";
					$videoOP .=				($Playlist->playlistvisual == 1 ? '"playlist.position": "right",
											"playlist.size": "100",'."\n" : '');
					$videoOP .=				'height: '.$Playlist->height.',
											width: '.($Playlist->playlistvisual == 1 ? $Playlist->width+100 : $Playlist->width).'
										});
										var player = document.getElementById(\'video-container\');
										});    //]]>
										</script>
										</div>
									</div>
						<div id="videoBlockRight">';
						foreach($SVids as $id => $item){
							$videoOP .= '<img class="videoLink" id="'.$id.'" src="/image/?image='.($item[img] ? '/upload/video/images/'.$item[img] : '/images/not_available.jpg').'&amp;height=68&amp;width=90" width="90" height="68" alt="manual playlist item" />';
						}
						$videoOP .= '</div>
								  </div>
								</div>';
						foreach($SVids as $id => $item){
							$videoOP .= '<div class="videoDesc" id="vidDesc'.$id.'"'.($id == 0 ? ' style="display:block" ' : ' style="display:none" ').'>
										  '.substr($item[desc],0,220).'...
										</div>';
						}
		}
	return $videoOP;
	}
	
    public function setView(Zend_View_Interface $view)
    {
        $this->view = $view;
    }
}