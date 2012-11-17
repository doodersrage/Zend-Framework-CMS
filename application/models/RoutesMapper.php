<?PHP
// application/models/RoutesMapper.php
 
class Application_Model_RoutesMapper
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
            $this->setDbTable('Application_Model_DbTable_Routes');
        }
        return $this->_dbTable;
    }
 
    public function delete($id)
    {
		$where = $this->getDbTable()->getAdapter()->quoteInto('id = ?',$id);
		$this->getDbTable()->delete($where);
    }
 
    public function save(Application_Model_Routes $routes)
    {
        $data = array(
            'type'   => $routes->getType(),
            'seg_id' => $routes->getSeg_id(),
            'uri' => $routes->getUri(),
        );
 
        if (($id = $routes->getId()) == '') {
            unset($data['id']);
            $this->getDbTable()->insert($data);
        } else {
            $this->getDbTable()->update($data, array('id = ?' => $id));
        }
    }
 
    public function find($id, Application_Model_Routes $routes)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }
        $row = $result->current();
        $routes->setId($row->id)
			  ->setType($row->type)
			  ->setSeg_id($row->seg_id)
			  ->setUri($row->uri);
    }
  
    public function fetchAll()
    {
        $resultSet = $this->getDbTable()->fetchAll();
        $entries   = array();
        foreach ($resultSet as $row) {
            $entry = new Application_Model_Routes();
			$entry->setId($row->id)
				  ->setType($row->type)
				  ->setSeg_id($row->seg_id)
				  ->setUri($row->uri);
            $entries[] = $entry;
        }
        return $entries;
    }
}