<?PHP
class InventoryController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
		$this->view->headTitle(Zend_Registry::get('Default Page Title'));
		$this->loadDropDowns();
    }
	
	public function loadDropDowns(){
		
		$db = Zend_Registry::get('db');
		
		// generate year drop down
		$select = $db->select()
					->distinct()
					->from('inventory','year');
		$select->order('year DESC');
		$resYears = $db->fetchAll($select);
		
		// generate body style drop down
		$select = $db->select()
					->distinct()
					->from('inventory','style');
		$select->order('style ASC');
		$resStyles = $db->fetchAll($select);
		
		// generate body make drop down
		$select = $db->select()
					->distinct()
					->from('inventory','make');
		$select->order('make ASC');
		$resMake = $db->fetchAll($select);
		
		// generate body model drop down
		$select = $db->select()
					->distinct()
					->from('inventory','model');
		$select->order('model ASC');
		$resModel = $db->fetchAll($select);
		
		// generate body model drop down
		$select = $db->select()
					->distinct()
					->from('inventory','color');
		$select->order('color ASC');
		$resColor = $db->fetchAll($select);
				
		// build years drop down
		$this->view->fromOpts = '';
		foreach($resYears as $curYr){
			$this->view->fromOpts .= '<option'.($this->getRequest()->getParam('from') == $curYr[year] ? ' selected="selected" ' : '').'>'.$curYr[year].'</option>';
		}
		$this->view->toOpts = '';
		foreach($resYears as $curYr){
			$this->view->toOpts .= '<option'.($this->getRequest()->getParam('to') == $curYr[year] ? ' selected="selected" ' : '').'>'.$curYr[year].'</option>';
		}
		// build styles drop down
		$this->view->styleOpts = '';
		foreach($resStyles as $curStyle){
			$this->view->styleOpts .= '<option'.($this->getRequest()->getParam('style') == $curStyle[style] ? ' selected="selected" ' : '').'>'.$curStyle[style].'</option>';
		}
		// build make drop down
		$this->view->makeOpts = '';
		foreach($resMake as $curMake){
			$this->view->makeOpts .= '<option'.($this->getRequest()->getParam('make') == $curMake[make] ? ' selected="selected" ' : '').'>'.$curMake[make].'</option>';
		}
		// build make drop down
		$this->view->modelOpts = '';
		foreach($resModel as $curModel){
			$this->view->modelOpts .= '<option'.($this->getRequest()->getParam('model') == $curModel[model] ? ' selected="selected" ' : '').'>'.$curModel[model].'</option>';
		}
		// build color drop down
		foreach($resColor as $curColor){
			$this->view->colorOpts .= '<option'.($this->getRequest()->getParam('color') == $curColor[color] ? ' selected="selected" ' : '').'>'.$curColor[color].'</option>';
		}

	}
	
	public function indexAction(){
		$this->view->headTitle()->prepend('Inventory');
		
		// list all available vehicles with paginator
		$db = Zend_Registry::get('db');
		
		// gather listing
		$select = $db->select()
					->from('inventory');
		
		// apply search filters
		if($this->getRequest()->getParam('from')){
			$select->where('year >= ?', $this->getRequest()->getParam('from'));
		}
		
		if($this->getRequest()->getParam('to')){
			$select->where('year <= ?', $this->getRequest()->getParam('to'));
		}
		
		if($this->getRequest()->getParam('style') && $this->getRequest()->getParam('style') != 'All'){
			$select->where('style = ?', $this->getRequest()->getParam('style'));
		}
		
		if($this->getRequest()->getParam('make') && $this->getRequest()->getParam('make') != 'All'){
			$select->where('make = ?', $this->getRequest()->getParam('make'));
		}
		
		if($this->getRequest()->getParam('model') && $this->getRequest()->getParam('model') != 'All'){
			$select->where('model = ?', $this->getRequest()->getParam('model'));
		}
		
		if($this->getRequest()->getParam('color') && $this->getRequest()->getParam('color') != 'All'){
			$select->where('color = ?', $this->getRequest()->getParam('color'));
		}

		$select->order('year DESC');
		
		$results = $db->fetchAll($select);
        if(isset($results)) {
            $paginator = Zend_Paginator::factory($results);
            $paginator->setItemCountPerPage(10);
            $paginator->setCurrentPageNumber($this->_getParam('page'));
            $this->view->paginator = $paginator;
 
            Zend_Paginator::setDefaultScrollingStyle('Sliding');
            Zend_View_Helper_PaginationControl::setDefaultViewPartial(
                'inventory/paginator.phtml'
            );
        }
		
		// play jingle
		$this->view->videoOP = $this->_helper->playlist(0);
		
		// if mobile device switch to mobile view
		if(Zend_Registry::get('mobile') == true){
			$this->_helper->layout->setLayout('mobile');
			$this->_helper->viewRenderer('mobindex');
		}
	}
	
	public function vehicleAction(){
		
		$inventory = new Application_Model_Inventory;
		$inventoryMapper = new Application_Model_InventoryMapper;
		$inventoryMapper->find($this->getRequest()->getParam('stocknum'),$inventory);
		$this->view->headTitle()->prepend(strtolower(trim($inventory->make)).' '.strtolower(trim($inventory->model)).' '.strtolower(trim($inventory->year)).' '.strtolower(trim($inventory->style)).' '.strtolower(trim($inventory->color)).' | Inventory Results');
		$this->view->headMeta()->appendName('description', strtolower(trim($inventory->make)).' '.strtolower(trim($inventory->model)).' '.strtolower(trim($inventory->year)).' '.strtolower(trim($inventory->style)).' '.strtolower(trim($inventory->color)));
		$this->view->headMeta()->appendName('keywords', strtolower(trim($inventory->make)).','.strtolower(trim($inventory->model)).','.strtolower(trim($inventory->year)).','.strtolower(trim($inventory->style)).','.strtolower(trim($inventory->color)));
		
		$this->view->inventory = $inventory;
		// if mobile device switch to mobile view
		if(Zend_Registry::get('mobile') == true){
			$this->_helper->layout->setLayout('mobile');
			$this->_helper->viewRenderer('mobvehicle');
		}
	}
	
}