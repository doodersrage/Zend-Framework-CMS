<?php
class Zend_View_Helper_BuildMenu  
{  
	private $children;
	
	// build menuing system
	function buildMenu($id = 0,$parent = ''){
		
		$routes = new Application_Model_Routes;
		$routesMapper = new Application_Model_RoutesMapper;

		$cache = Zend_Cache::factory
		(
			'Output' // frontend caching method
			, 'File'
			, array('lifetime'=>90)
			, array('cache_dir'=>$_SERVER['DOCUMENT_ROOT'] . '/cache')
		);
		 
		if (!($cache->start('topMenu'))) 
		{				
			$db = Zend_Registry::get('db');
			$select = $db->select()
						->from('pages')
						->where('parent_id = ?',$id)
						->where('menu = ?',0)
						->order('pgsort DESC')
						->order('title ASC');
			$results = $db->fetchAll($select);
			
			if(count($results)){
				echo '<div id="topNav"><ul class="sf-menu">';
				foreach($results as $id => $item){
					if($item[new_window] == 1){
						$fileTrgt = ' target="_blank" ';
					} else {
						$fileTrgt = '';
					}
					if($item[filelnk] != ''){
						$lnkFnl = $item[filelnk];
					} else {
						if($item[route_id]){
							$routesMapper->find($item[route_id],$routes);
							$lnkFnl = 'http://www.ynotpizza.com/'.$routes->getUri().'/';
						}elseif($item[link_name] == '' || $item[link_name] == NULL){
							$lnkFnl = 'http://www.ynotpizza.com/content/?id='.$item[id].'/';
						} else {
							$lnkFnl = 'http://www.ynotpizza.com/content/'.$item[link_name].'/';
						}
					}
					echo '<li id="parent'.$item[id].'"><a onmouseover="showMenu('.$item[id].');" href="'.$lnkFnl.'"'.$fileTrgt.' title="'.$item[title].'">'.$item[title].'</a></li>'."\n";
					$this->bldChldBox($item[id],$newLnk);
				}
				echo '</ul></div>';
				echo $this->children;
			}
			$cache->end(); // output buffering ends
		}
	}
	
	function cleanDups($val){
		if(strpos($val,'--')){
			$val = str_replace('--','-',$val);
		}
		if(strpos($val,'--')){
			$val = $this->cleanDups($val);
		}
	return $val;
	}
	
	// get child page links
	function bldChldBox($id,$parent = ''){
		
		$routes = new Application_Model_Routes;
		$routesMapper = new Application_Model_RoutesMapper;

		$db = Zend_Registry::get('db');
		$select = $db->select()
					->from('pages')
					->where('parent_id = ?',$id)
					->where('menu = ?',0)
					->order('pgsort DESC')
					->order('title ASC');
		$results = $db->fetchAll($select);
		
		if(count($results)){
			$this->children .= '<div class="childDDMenu" id="childrenDD'.$id.'" style="display:none">';
			foreach($results as $id => $item){
				if($item[new_window] == 1){
					$fileTrgt = ' target="_blank" ';
				} else {
					$fileTrgt = '';
				}
				if($item[filelnk] != ''){
					$lnkFnl = $item[filelnk];
				} else {
					if($item[route_id]){
						$routesMapper->find($item[route_id],$routes);
						$lnkFnl = 'http://www.ynotpizza.com/'.$routes->getUri().'/';
					}elseif($item[link_name] == '' || $item[link_name] == NULL){
						$lnkFnl = 'http://www.ynotpizza.com/content/?id='.$item[id].'/';
					} else {
						$lnkFnl = 'http://www.ynotpizza.com/content/'.$item[link_name].'/';
					}
				}
				$this->children .= '<a class="childDDMenuLink" href="'.$lnkFnl.'"'.$fileTrgt.' title="'.$item[title].'">'.$item[title].'</a>'."\n";
			}
			
			$this->children .= '</div>';
		}
	}
}  
 