<?PHP
// application/models/FormSubsMapper.php
 
class Application_Model_FormSubsMapper
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
            $this->setDbTable('Application_Model_DbTable_FormSubs');
        }
        return $this->_dbTable;
    }
 
    public function delete($id)
    {
		$where = $this->getDbTable()->getAdapter()->quoteInto('id = ?',$id);
		$this->getDbTable()->delete($where);
    }
 
    public function save(Application_Model_FormSubs $formsubs)
    {
        $data = array(
            'values'   => $formsubs->getValues(),
            'type'   => $formsubs->getType(),
        );
 
        if (($id = $formsubs->getId()) == '') {
            unset($data['id']);
            $this->getDbTable()->insert($data);
        } else {
            $this->getDbTable()->update($data, array('id = ?' => $id));
        }
    }
 
    public function find($id, Application_Model_FormSubs $formsubs)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }
        $row = $result->current();
        $formsubs->setId($row->id)
			  ->setValues($row->values)
			  ->setType($row->type)
			  ->setDate($row->date);
    }
  
    public function fetchAll()
    {
        $resultSet = $this->getDbTable()->fetchAll();
        $entries   = array();
        foreach ($resultSet as $row) {
            $entry = new Application_Model_FormSubs();
			$entry->setId($row->id)
				  ->setValues($row->values)
				  ->setType($row->type)
				  ->setDate($row->date);
            $entries[] = $entry;
        }
        return $entries;
    }
}