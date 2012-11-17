<?PHP
// application/models/LocationsMapper.php
 
class Application_Model_LocationsMapper
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
            $this->setDbTable('Application_Model_DbTable_Locations');
        }
        return $this->_dbTable;
    }
 
    public function delete($id)
    {
		$where = $this->getDbTable()->getAdapter()->quoteInto('id = ?',$id);
		$this->getDbTable()->delete($where);
    }
 
    public function save(Application_Model_Locations $locations)
    {
        $data = array(
            'name'   => $locations->getName(),
            'specials' => $locations->getSpecials(),
        );
 
        if (($id = $locations->getId()) == '') {
            unset($data['id']);
            $this->getDbTable()->insert($data);
        } else {
            $this->getDbTable()->update($data, array('id = ?' => $id));
        }
    }
 
    public function find($id, Application_Model_Locations $locations)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }
        $row = $result->current();
        $locations->setId($row->id)
			  ->setName($row->name)
			  ->setSpecials($row->specials);
    }
  
    public function fetchAll()
    {
        $resultSet = $this->getDbTable()->fetchAll();
        $entries   = array();
        foreach ($resultSet as $row) {
            $locations = new Application_Model_Locations();
			$locations->setId($row->id)
				  ->setName($row->name)
				  ->setSpecials($row->specials);
            $entries[] = $locations;
        }
        return $entries;
    }
}