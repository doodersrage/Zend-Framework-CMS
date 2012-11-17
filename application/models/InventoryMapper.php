<?PHP
// application/models/InventoryMapper.php
 
class Application_Model_InventoryMapper
{
    protected $_dbTable;
 
    public function setDbTable($dbTable)
    {
        if (is_string($dbTable)) {
            $dbTable = new $dbTable();
        }
        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception('Invalid table data gateway provided');
        }
        $this->_dbTable = $dbTable;
        return $this;
    }
 
    public function getDbTable()
    {
        if (null === $this->_dbTable) {
            $this->setDbTable('Application_Model_DbTable_Inventory');
        }
        return $this->_dbTable;
    }
 
    public function save(Application_Model_Inventory $inventory)
    {
        $data = array(
            'stockNum'   => $inventory->getStockNum(),
            'year'   => $inventory->getYear(),
            'make' => $inventory->getMake(),
            'model' => $inventory->getModel(),
            'style' => $inventory->getStyle(),
            'color' => $inventory->getColor(),
            'vin' => $inventory->getVin(),
            'images' => $inventory->getImages(),
            'name' => $inventory->getName(),
            'price' => $inventory->getPrice(),
            'desciption' => $inventory->getDesciption(),
        );
 
        if (($id = $inventory->getStockNum()) == '') {
            unset($data['stockNum']);
            $this->getDbTable()->insert($data);
        } else {
			if($this->find($inventory->getStockNum(),$inventory)){
				$this->getDbTable()->update($data, array('stockNum = ?' => $id));
			} else {
				$this->getDbTable()->insert($data);
			}
        }
    }
 
    public function delete($id)
    {
		$where = $this->getDbTable()->getAdapter()->quoteInto('stockNum = ?',$id);
		$this->getDbTable()->delete($where);
    }
 
    public function find($id, Application_Model_Inventory $inventory)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return false;
        }
        $row = $result->current();
        $inventory->setStockNum($row->stockNum)
                  ->setYear($row->year)
                  ->setMake($row->make)
                  ->setModel($row->model)
                  ->setStyle($row->style)
                  ->setColor($row->color)
                  ->setVin($row->vin)
                  ->setImages($row->images)
                  ->setName($row->name)
                  ->setPrice($row->price)
                  ->setDesciption($row->desciption);
				  
	return true;
    }
 
    public function titleSearch($model, Application_Model_Inventory $inventory)
    {
        $result = $this->getDbTable();
		$select = $result->select()->where('LOWER(model) = ?',$title);
		$result = $result->fetchRow($select);
        if (0 == count($result)) {
            return;
        }
        $row = $result;
        $inventory->setStockNum($row->stockNum)
                  ->setYear($row->year)
                  ->setMake($row->make)
                  ->setModel($row->model)
                  ->setStyle($row->style)
                  ->setColor($row->color)
                  ->setVin($row->vin)
                  ->setImages($row->images)
                  ->setName($row->name)
                  ->setPrice($row->price)
                  ->setDesciption($row->desciption);
    }
 
    public function fetchAll()
    {
        $resultSet = $this->getDbTable()->fetchAll();
        $entries   = array();
        foreach ($resultSet as $row) {
            $entry = new Application_Model_Inventory();
			$entry->setStockNum($row->stockNum)
                  ->setYear($row->year)
                  ->setMake($row->make)
                  ->setModel($row->model)
                  ->setStyle($row->style)
                  ->setColor($row->color)
                  ->setVin($row->vin)
                  ->setImages($row->images)
                  ->setName($row->name)
                  ->setPrice($row->price)
                  ->setDesciption($row->desciption);
            $entries[] = $entry;
        }
        return $entries;
    }
}