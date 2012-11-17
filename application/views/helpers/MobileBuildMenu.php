<?php
class Zend_View_Helper_MobileBuildMenu  
{  
	private $children;
	
	// build menuing system
	function mobileBuildMenu($id = 0,$parent = ''){
		
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
			echo '<ul class="sf-menu">';
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
						$lnkFnl = '/'.$routes->getUri().'/';
					}elseif($item[link_name] == '' || $item[link_name] == NULL){
						$lnkFnl = '/content/?id='.$item[id].'/';
					} else {
						$lnkFnl = '/content/'.$item[link_name].'/';
					}
				}
				echo '<li id="parent'.$item[id].'"><a onmouseover="showMenu('.$item[id].');" href="'.$lnkFnl.'"'.$fileTrgt.' title="'.$item[title].'">'.$item[title].'</a></li>'."\n";
			}
			echo '</ul>';
			echo $this->children;
		}
	}
}  
 