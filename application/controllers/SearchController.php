<?PHP

class SearchController extends Zend_Controller_Action
{
    protected $lnkList = array();

    public function init()
    {
        /* Initialize action controller here */
 		$this->view->headTitle(Zend_Registry::get('Default Page Title'));
		$this->view->headTitle()->prepend('Search');
		if(Zend_Registry::get('mobile') == false){
			$this->view->minifyHeadLink()->appendStylesheet('/css/content.css');
			$this->view->minifyHeadLink()->appendStylesheet('/js/fancybox/jquery.fancybox-1.3.4.css');
			$this->view->minifyHeadScript()->appendFile('/js/fancybox/jquery.fancybox-1.3.4.pack.js');
		}
   }
   
	// build menuing system
	function buildList($id = 0,$parent = ''){
		
		$routes = new Application_Model_Routes;
		$routesMapper = new Application_Model_RoutesMapper;
		
		$db = Zend_Registry::get('db');
		$select = $db->select()
					->from('pages')
					->where('parent_id = ?',$id)
					->where('menu = ?',0)
					->order('modified DESC')
					->order('pgsort DESC')
					->order('title ASC');
		$results = $db->fetchAll($select);
		
		foreach($results as $id => $item){
			$title = strtolower($item[title]);
			$copy_text = strtolower($item[copy_text]);
			$search = strtolower($this->getRequest()->getParam('searchVal'));
			if(strpos($title,$search) || strpos($copy_text,$search)){
				if($item[filelnk] != ''){
						$lnkFnl = $item[filelnk];
						$item[pglink] = $lnkFnl;
						$this->lnkList[] = $item;
				} else {
					if($item[route_id]){
						$routesMapper->find($item[route_id],$routes);
						$lnkFnl = '/'.$routes->getUri().'/';
					}elseif($item[link_name] == '' || $item[link_name] == NULL){
						$lnkFnl = '/content/?id='.$item[id].'/';
					} else {
						$lnkFnl = '/content/'.$item[link_name].'/';
					}
					$item[pglink] = $lnkFnl;
					$this->lnkList[] = $item;
				}
			}
			$this->buildList($item[id],$newLnk);
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

    public function indexAction()
    {
		if(Zend_Registry::get('mobile') == true){
			$this->_helper->layout->setLayout('mobile');
			$this->_helper->viewRenderer->setRender('mobile');
		}
		
		$this->getRequest()->getParam('searchval');
		
		// list all available vehicles with paginator
		$db = Zend_Registry::get('db');
		
		// first search vehicles then search pages
		// gather listing
		$select = $db->select()
					->from('inventory');
		
		// apply search filters
		if($this->getRequest()->getParam('searchVal')){
			$select->where('LOWER(year) like ?', '%'.strtolower($this->getRequest()->getParam('searchVal')).'%');
			$select->orwhere('LOWER(make) like ?', '%'.strtolower($this->getRequest()->getParam('searchVal')).'%');
			$select->orwhere('LOWER(model) like ?', '%'.strtolower($this->getRequest()->getParam('searchVal')).'%');
			$select->orwhere('LOWER(style) like ?', '%'.strtolower($this->getRequest()->getParam('searchVal')).'%');
		}

		$select->order('stockNum ASC');
		
		$results = $db->fetchAll($select);
		$this->view->vehicles = $results;
		
		// gather pages
		// gather listing
		$this->buildList();
		$results = $this->lnkList;
		$this->view->pages = $results;
		
		// press releases
		$select = $db->select()
			->from('press_releases')
			->where('LOWER(title) like ?', '%'.strtolower($this->getRequest()->getParam('searchVal')).'%')
			->orwhere('LOWER(copy_text) like ?', '%'.strtolower($this->getRequest()->getParam('searchVal')).'%')
			->order('date DESC');
		
		$results = $db->fetchAll($select);
		$this->view->press_releases = $results;
		
		// news items
		$select = $db->select()
			->from('news_item')
			->where('LOWER(name) like ?', '%'.strtolower($this->getRequest()->getParam('searchVal')).'%')
			->orwhere('LOWER(description) like ?', '%'.strtolower($this->getRequest()->getParam('searchVal')).'%')
			->order('modified DESC');
		
		$results = $db->fetchAll($select);
		$this->view->news = $results;
		
		// events calendar
		$select = $db->select()
					->from('events_cal')
					->where('LOWER(name) like ?', '%'.strtolower($this->getRequest()->getParam('searchVal')).'%')
					->orwhere('LOWER(description) like ?', '%'.strtolower($this->getRequest()->getParam('searchVal')).'%')
					->order('modified DESC');
		
		$results = $db->fetchAll($select);
		$this->view->events = $results;

		// partners
		$select = $db->select()
			->from('partners')
			->where('LOWER(name) like ?', '%'.strtolower($this->getRequest()->getParam('searchVal')).'%')
			->orwhere('LOWER(description) like ?', '%'.strtolower($this->getRequest()->getParam('searchVal')).'%')
			->order('modified DESC');
		
		$results = $db->fetchAll($select);
		$this->view->partners = $results;

		
	}
}
