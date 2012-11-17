<?PHP
class Zend_View_Helper_RightImages
{
    function rightImages()
    {
		$db = Zend_Registry::get('db');
		$select = $db->select()
					->from('inventory');
		$select->order('RAND()');
		$select->limit('2');
		$rightItems = $db->fetchAll($select);
		
		$images = '<div id="rightImages">';
		  foreach($rightItems as $item){
			  $images .= '<a class="enlarge" href="/upload/invent/images/'.$item[images].'" title="'.$item[name].'"><img class="carouselItem" src="/image/?image=/upload/invent/images/'.$item[images].'&amp;height=235&amp;width=330" alt="'.$item[name].'" /></a>';
		  }
		$images .= '</div>';
		
	return $images;
	}
}
