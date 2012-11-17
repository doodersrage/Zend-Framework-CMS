<?PHP
// application/models/ConfigCatsMapper.php
 
class Application_Model_ConfigCatsMapper
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
            $this->setDbTable('Application_Model_DbTable_ConfigCats');
        }
        return $this->_dbTable;
    }
 
    public function delete($id)
    {
		$where = $this->getDbTable()->getAdapter()->quoteInto('id = ?',$id);
		$this->getDbTable()->delete($where);
    }
 
    public function save(Application_Model_ConfigCats $configcats)
    {
        $data = array(
            'name'   => $configcats->getName(),
            'desc'   => $configcats->getDesc(),
        );
 
        if (($id = $configcats->getId()) == '') {
            unset($data['id']);
            $this->getDbTable()->insert($data);
        } else {
            $this->getDbTable()->update($data, array('id = ?' => $id));
        }
    }
 
    public function find($id, Application_Model_ConfigCats $configcats)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }
        $row = $result->current();
        $configcats->setId($row->id)
				  ->setName($row->name)
				  ->setDesc($row->desc);
    }
  
    public function fetchAll()
    {
        $resultSet = $this->getDbTable()->fetchAll();
        $entries   = array();
        foreach ($resultSet as $row) {
            $configcats = new Application_Model_ConfigCats();
			$configcats->setId($row->id)
					  ->setName($row->name)
					  ->setDesc($row->desc);
            $entries[] = $configcats;
        }
        return $entries;
    }
}