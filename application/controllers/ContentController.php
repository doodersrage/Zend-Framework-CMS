<?php

class ContentController extends Zend_Controller_Action
{

    protected $lnkList = array();
	protected $_bc = array();
	
	public function init()
    {
        /* Initialize action controller here */
//		$this->_helper->cache(array('index'), array('allentries'));
		$this->getResponse()->setHeader('Expires', '', true);
		$this->getResponse()->setHeader('Cache-Control', 'public', true);
		$this->getResponse()->setHeader('Cache-Control', 'max-age=3800');
		$this->getResponse()->setHeader('Pragma', '', true);
 		$this->view->headTitle(Zend_Registry::get('Default Page Title'));
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
		$db = Zend_Registry::get('db');
        $request_val = $this->_getParam('name');
		switch($request_val){
			case 'rss':
				$this->rssAction();
				$this->_helper->viewRenderer->setRender('rss');
				end;
			break;
			case 'xml':
				$this->_helper->layout->setLayout('xml');
				$this->_helper->viewRenderer->setRender('xml');
				end;
			break;
			default:
				$request_id = str_replace('-',' ',$this->_getParam('id'));
				if(empty($request_id) && empty($request_val)){
					$this->_redirect('/');
				} else {
			
					$pages = new Application_Model_Pages;
					$pages->setTitle($request_val);
					$pagesMapper = new Application_Model_PagesMapper;
					if(empty($request_val)){
						$pagesMapper->find($request_id, $pages);
					} else {
						$pagesMapper->linkNameSearch($request_val, $pages);
		
						$routes = new Application_Model_Routes;
						$routesMapper = new Application_Model_RoutesMapper;
						
						if($pages->getLink_name()){
							$routesMapper->find($pages->getRoute_id(),$routes);
							if($routes->getUri()){
								$lnkFnl = '/'.$routes->getUri().'/';
								$this->_redirect($lnkFnl, array('code'=>301));
							}
						}
					}
					
					// set registry id value for menu
					Zend_Registry::set('Content ID',$pages->getId());
					
					// print error if page is not found
					if($pages->getId() == ''){
						$this->view->headTitle()->prepend('Page Not Found');
						throw new Exception("Page not found!");
					// if page is found gather copy
					} else {
						// first check for linked file assignment
						if($pages->getFilelnk()){
							// disable output then redirect user
							//$this->_helper->layout()->disableLayout();
							//$this->_helper->viewRenderer->setNoRender(true);
							header('Location: '.$pages->getFilelnk());
						}
						
						$this->view->id = $pages->getId();
						$this->view->title = $pages->getTitle();
						
						// html tidy config
						$tidy_config = array(
											 'drop-proprietary-attributes'    =>    true, 
											 'output-xhtml' => true,
											 'show-body-only' => true,
											 'word-2000' => true,
											 'indent' => true,
											 'wrap' => 0,
											 );
						
						$copy_text = tidy_parse_string(str_replace(array('<div class="formpad">&nbsp;</div>','<div class="clear">&nbsp;</div>','APPROVAL_CODE'),array('<div class="formpad"></div>','<div class="clear"></div>',(isset($_SESSION['approvalCode']) ? $_SESSION['approvalCode'] : '')),$pages->getCopy_text()), $tidy_config, 'UTF8');
						$copy_text->cleanRepair(); 
						
						$this->view->copy_text = $copy_text;
						
						$mobile_text = tidy_parse_string(str_replace(array('<div class="formpad">&nbsp;</div>','<div class="clear">&nbsp;</div>','APPROVAL_CODE'),array('<div class="formpad"></div>','<div class="clear"></div>',(isset($_SESSION['approvalCode']) ? $_SESSION['approvalCode'] : '')),$pages->getMobile_text()), $tidy_config, 'UTF8');
						$mobile_text->cleanRepair(); 
						$this->view->mobile_text = $mobile_text;
						
						$this->view->seo_text = str_replace(array('<div class="clear">&nbsp;</div>'),array('<div class="clear"></div>'),$pages->getSeo_text());
						if($pages->getTitle_tag() != ''){
							$this->view->headTitle()->prepend($pages->getTitle_tag());
						}
						if($pages->getDesc_tag() != ''){
							$this->view->headMeta()->appendName('description', $pages->getDesc_tag());	
						}
						if($pages->getKeyword_tag() != ''){
							$this->view->headMeta()->appendName('keywords', $pages->getKeyword_tag());
						}
						if($pages->getCalendar() == 1){
							$this->_helper->calendar();
						}
						
						// load playlist
						if($pages->getPlaylist() > 0){
							$playlistRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('Playlist');
							$this->view->videoOP = $playlistRenderer->direct($pages->getPlaylist());
						}
						
						// load form
						if($pages->getForm_id() > 0){
							$formRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('FormRender');
							$this->view->form = $formRenderer->direct($pages->getForm_id());
						}
						
						if($pages->getSlideshow() > 0){
							$this->view->minifyHeadScript()->appendFile('/js/nivo-slider/jquery.nivo.slider.pack.js')
													->appendFile('/js/fancybox/jquery.fancybox-1.3.4.pack.js');
							$this->view->minifyHeadLink()->appendStylesheet('/js/nivo-slider/nivo-slider.css')
													->appendStylesheet('/js/fancybox/jquery.fancybox-1.3.4.css');
							$slideshowRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('Slideshow');
							$this->view->slideshowOP = $slideshowRenderer->direct($pages->getSlideshow());
						}
													
						// build breadcrumbs
						$this->breadCrumbs($pages->id);
						$links = array();
						foreach($this->_bc as $id => $curbc){
							$select = $db->select()
										->from('pages')
										->where('id = ?',$id)
										->where('menu = ?',0);
							$results = $db->fetchRow($select);
							if($results[filelnk] != ''){
								$lnkFnl = $results[filelnk];
							} else {
								if($results[link_name] == '' || $results[link_name] == NULL){
									$lnkFnl = '/content/?id='.$results[id].'/';
								} else {
									$lnkFnl = '/content/'.$results[link_name].'/';
								}
							}
							unset($this->_bc[$id]);
							$links[] = '<a href="'.$lnkFnl.'">'.$results[title].'</a>';
						}
						krsort($links);
						$links = array('<a href="/">HOME</a>',implode(' &raquo; ',$links));
						$this->view->bc = implode(' &raquo; ',$links);
					}
					
					$this->view->minifyHeadScript()->appendFile('/js/jquery.validate.min.js');
					if(Zend_Registry::get('mobile') == false){
						$this->view->minifyHeadLink()->appendStylesheet('/js/fancybox/jquery.fancybox-1.3.4.css');
						$this->view->minifyHeadScript()->appendFile('/js/fancybox/jquery.fancybox-1.3.4.pack.js');
						$this->view->minifyHeadLink()->appendStylesheet('/css/content.css');
						$this->view->headScript()->appendFile('/js/content.js');
					} else {
						$this->_helper->layout->setLayout('mobile');
						$this->_helper->viewRenderer->setRender('mobile');
					}
				}
			break;
		}
    }
	
	private function breadCrumbs($parent = 0){
		$db = Zend_Registry::get('db');
		$select = $db->select()
					->from('pages')
					->where('id = ?',$parent)
					->where('menu = ?',0);
		$results = $db->fetchAll($select);
		foreach($results as $item){
			$this->_bc[$item[id]] = $this->cleanDups(strtolower(preg_replace("/[^A-Za-z0-9\s\s+]/","-",$item[title])));
			$this->breadCrumbs($item[parent_id]);
		}
	}
		
	public function xmlAction(){
        $this->_helper->layout->setLayout('xml');
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
			$lnkName = array();
			$newLnk = (!empty($parent) ? $parent.'-' : '').$this->cleanDups(strtolower(preg_replace("/[^A-Za-z0-9\s\s+]/","-",$item[title])));
			$lnkName[] = $newLnk;
			$lnkName[] = $item[id];
			$lnkStr = implode('/',$lnkName);
			if($item[filelnk] != ''){
				// do nothing
			} else {
				if($item[route_id]){
					$routesMapper->find($item[route_id],$routes);
					$lnkFnl = '/'.$routes->getUri().'/';
				}elseif($item[link_name] == '' || $item[link_name] == NULL){
					$lnkFnl = 'http://www.ynotpizza.com/content/?id='.$item[id].'/';
				} else {
					$lnkFnl = 'http://www.ynotpizza.com/content/'.$item[link_name].'/';
				}
				$item[pglink] = $lnkFnl;
				$this->lnkList[] = $item;
			}
			$this->buildList($item[id],$newLnk);
		}
	}

	public function rssAction(){
        $this->_helper->layout->setLayout('rss');
		// list all available vehicles with paginator
		$db = Zend_Registry::get('db');
				
		// gather pages
		// gather listing
		
		$feed = new Zend_Feed_Writer_Feed;
		$feed->setTitle(Zend_Registry::get('Site Name'));
		$feed->setLink('http://www.ynotpizza.com/');
		$feed->setFeedLink('http://www.ynotpizza.com/news/rss/', 'atom');
		$feed->addAuthor(array(
			'name'  => Zend_Registry::get('Site Name'),
			'email' => Zend_Registry::get('Contact Email'),
			'uri'   => 'http://www.ynotpizza.com/',
		));
		$feed->setDateModified(time());
		$feed->addHub('http://pubsubhubbub.appspot.com/');
		 
		 $this->buildList();
		 $results = $this->lnkList;
		/**
		* Add one or more entries. Note that entries must
		* be manually added once created.
		*/
		foreach($results as $item){
			$entry = $feed->createEntry();
			$entry->setTitle($item[title]);
			$entry->setLink($item[pglink]);
			$entry->addAuthor(array(
			'name'  => Zend_Registry::get('Site Name'),
			'email' => Zend_Registry::get('Contact Email'),
			'uri'   => 'http://www.ynotpizza.com/',
			));
			$dateSet = strtotime($item[modified]);
			if($dateSet == -62169958800) $dateSet = time();
			$entry->setDateModified($dateSet);
			$entry->setDateCreated($dateSet);
			$entry->setContent(
				str_replace(array('&nbsp;','&copy;','&rsquo;','&ldquo;','&rdquo;','&ndash','&eacute;','&mdash;','&reg;','&bull;','&cent'),'',$item[copy_text])
			);
			$feed->addEntry($entry);
		}
		 
		/**
		* Render the resulting feed to Atom 1.0 and assign to $out.
		* You can substitute "atom" with "rss" to generate an RSS 2.0 feed.
		*/
		$this->view->out = $feed->export('atom');	
	}

}

