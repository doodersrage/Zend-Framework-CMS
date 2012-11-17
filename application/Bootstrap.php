<?php
if (!empty($_REQUEST['PHPSESSID'])) {
	Zend_Session::setId($_REQUEST['PHPSESSID']);
}

Zend_Session::start();

//enable/disable mobile
if($_GET['mobSwitch'] == 1){
	unset($_SESSION['noMob']);
} elseif($_GET['mobSwitch'] == 2) {
	$_SESSION['noMob'] = 1;
}


class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	protected $_front;
	
	public function init()
    {
	}
		
	protected function _initConfig(){
		include_once(APP_BASE_PATH.'/application/configs/db.php');
		$config = new Zend_Config($config);
		return $config;
	}
	
	protected function _initDb(){
		$this->bootstrap('config');
		$config = $this->getResource('config');
		$db = Zend_Db::factory($config->database);
		Zend_Db_Table_Abstract::setDefaultAdapter($db);
		Zend_Registry::set('db', $db);
		return $db;
	}

//	// full page cache
//	protected function _initCache()
//    {
//        $dir = APP_BASE_PATH . "/cache/page";
//        $frontendOptions = array(
//            'lifetime' => 3600,
//            'content_type_memorization' => true,
//			'default_options'           => array(
//			'cache' => true,
//			'cache_with_get_variables' => true,
//			'cache_with_post_variables' => true,
//			'cache_with_session_variables' => true,
//			'cache_with_cookie_variables' => true,
//            ),
//        'regexps' => array(
//                // cache the whole IndexController
//                '^/.*' => array('cache' => true),
//                // cache the whole IndexController
//                '^/admin.*' => array('cache' => false),
//                 )
//        );
//
//        $backendOptions = array(
//                'cache_dir' =>$dir
//        );
//
//        // getting a Zend_Cache_Frontend_Page object
//        $cache = Zend_Cache::factory('Page',
//                             'File',
//                             $frontendOptions,
//                             $backendOptions);
// 
//        $cache->start();
//    }
	
	protected function _initRegistry(){
		$this->bootstrap('db');
		$config = new Application_Model_Config;
		$configMapper = new Application_Model_ConfigMapper;
		
		$configTot = $configMapper->fetchAll();
		
		// set default content id
		Zend_Registry::set('Content ID', 0);
		
		foreach($configTot as $item){
			Zend_Registry::set($item->name, $item->defin);
		}
	}
	
    protected function _initView()
	{

		// configure default doctype, includes, and mobile settings
        $view = new Zend_View();
//        $view->doctype('XHTML1_TRANSITIONAL');
//		// setting content type and character set
//		$view->headMeta()->appendHttpEquiv('Content-Type',
//										   'text/html; charset=UTF-8')
//						 ->appendHttpEquiv('Content-Language', 'en-US'); 
		
		$view->headMeta()->appendName('robots', 'noodp,NOYDIR');	
		// set default header title
		$view->headTitle()->setSeparator(' | ');
		
		// check for first page request and if so set appropriate session value
		
		// added for mobile device detection
		// Include the Tera-WURFL file
		require_once(APP_BASE_PATH.'/Tera-WURFL/TeraWurfl.php');
		
		// instantiate the Tera-WURFL object
		$wurflObj = new TeraWurfl();
		
		// Get the capabilities of the current client.
		$wurflObj->getDeviceCapabilitiesFromAgent();
		Zend_Registry::set('DEVICE',$wurflObj->getDeviceCapability("model_name"));
		if($wurflObj->getDeviceCapability("is_wireless_device")){
			$device = strtolower($wurflObj->getDeviceCapability("model_name"));
			switch($device){
				case "ipad":
					$view->headLink()->appendStylesheet('/css/ipad.css');
				break;
				default:
					if(!isset($_SESSION['first'])){
						$_SESSION['noMob'] = 1;
						$_SESSION['first'] = 1;
					}
				break;
			}
		}
	
		// see if this client is on a wireless device (or if they can't be identified)
		if(isset($_SESSION['noMob'])){
			Zend_Registry::set('mobile', true);
			$view->headScript()->appendFile('/js/jquery-1.7.1.min.js');
			$view->headScript()->appendFile('/js/jquery.mobile-1.0.min.js');
			$view->headLink()->appendStylesheet('/css/jquery.mobile-1.0.min.css');
		} else {
			$view->headScript()->appendFile('/js/jquery-1.5.1.min.js');
			Zend_Registry::set('mobile', false);
			// added for hello bar
			$uri = $_SERVER['REQUEST_URI'];
			$pos = strpos($uri, 'admin');
			if($pos == false){
				if(!$_SESSION['hello'] || $_SESSION['hello'] == 2){
					$view->headScript()->appendFile('/js/hello.js');
					$view->headLink()->appendStylesheet('/css/hello.css');
					$_SESSION['hello'] = rand(1,3);
				} elseif($_SESSION['hello'] != 'submitted') {
					$_SESSION['hello'] = rand(1,3);
				}
			}
			
		}
	}
		
	public function _initRouter()
	{
		// initialize front controller and router
		$front = Zend_Controller_Front::getInstance();
		$router = $front->getRouter();
		
		// read in dynamic routes
		$this->bootstrap('db');
		$db = Zend_Registry::get('db');
		
		$uri = $_SERVER["REQUEST_URI"];
		
		// gather and trim submitted uri
		$uriLen = strlen($_SERVER["REQUEST_URI"]);
		$uriCln = explode('?',$uri);
		$uriCln = substr($uriCln[0],1,-1);
		
		// if uri string not empty query route
		if(!empty($uriCln)){
			$select = $db->select()
						->from('routes')
						->where('uri = ?',$uriCln);
			$results = $db->fetchRow($select);
			
			// if uri found within dynamic routes assign route
			if($results){
				switch($results[type]){
					case 'content':
						$route = new Zend_Controller_Router_Route(
							$uriCln,
							array(
								'id'       => $results[seg_id],
								'controller' => 'content',
								'action'     => 'index'
							)
						);
						$router->addRoute($results[type].$results[id], $route);
					break;
					case 'news_item':
						$route = new Zend_Controller_Router_Route(
							$uriCln,
							array(
								'id'       => $results[seg_id],
								'controller' => 'news',
								'action'     => 'item'
							)
						);
						$router->addRoute($results[type].$results[id], $route);
					break;
					case 'event':
						$route = new Zend_Controller_Router_Route(
							$uriCln,
							array(
								'id'       => $results[seg_id],
								'controller' => 'event',
								'action'     => 'name'
							)
						);
						$router->addRoute($results[type].$results[id], $route);
					break;
				}
			}
		}

	
		// static routes
		// Add some routes
		$route = new Zend_Controller_Router_Route(
			'content/:name',
			array(
				'name'       => null,
				'controller' => 'content',
				'action'     => 'index'
			)
		);
		$router->addRoute('content', $route);
		
		// Add some routes
		$route = new Zend_Controller_Router_Route(
			'event/name/:event_name/:id',
			array(
				'sponsor_name'       => null,
				'controller' => 'event',
				'action'     => 'name'
			)
		);
		$router->addRoute('event', $route);
		
		// Add some routes
		$route = new Zend_Controller_Router_Route(
			'reference-materials/item/:item_name/:id',
			array(
				'sponsor_name'       => null,
				'controller' => 'reference-materials',
				'action'     => 'item'
			)
		);
		$router->addRoute('reference_item', $route);
		
		// Add some routes
		$route = new Zend_Controller_Router_Route(
			'news/item/:item_name/:id',
			array(
				'sponsor_name'       => null,
				'controller' => 'news',
				'action'     => 'item'
			)
		);
		$router->addRoute('news_item', $route);
		
		// Add some routes
		$route = new Zend_Controller_Router_Route(
			'news/category/:category_name/:id',
			array(
				'sponsor_name'       => null,
				'controller' => 'news',
				'action'     => 'category'
			)
		);
		$router->addRoute('news_category', $route);
		
		// Add some routes
		$route = new Zend_Controller_Router_Route(
			'partners/partner/:partner_name/:id',
			array(
				'sponsor_name'       => null,
				'controller' => 'partners',
				'action'     => 'partner'
			)
		);
		$router->addRoute('partners', $route);
		
		// Add some routes
		$route = new Zend_Controller_Router_Route(
			'press-releases/item/:item_name/:id',
			array(
				'sponsor_name'       => null,
				'controller' => 'press-releases',
				'action'     => 'item'
			)
		);
		$router->addRoute('press_releases_item', $route);

		// Returns the router resource to bootstrap resource registry
		return $router;
	}
}

