<?PHP
class Zend_View_Helper_LeftImages
{
    function leftImages()
    {
		$db = Zend_Registry::get('db');
		$select = $db->select()
					->from('inventory');
		$select->order('RAND()');
		$select->limit('5');
		$smallItems = $db->fetchAll($select);
		
		$images = '<div id="leftImages">';
		  foreach($smallItems as $item){
			  $images .= '<a class="enlarge" href="/upload/invent/images/'.$item[images].'" title="'.$item[name].'"><img class="carouselItem" src="/image/?image=/upload/invent/images/'.$item[images].'&amp;height=80&amp;width=135" alt="'.$item[name].'" /></a>';
		  }
		$images .= '</div>';
		
	return $images;
	}
}
