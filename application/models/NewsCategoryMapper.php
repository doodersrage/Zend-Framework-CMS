<?PHP
// application/models/NewsCategoryMapper.php
 
class Application_Model_NewsCategoryMapper
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
            $this->setDbTable('Application_Model_DbTable_NewsCategory');
        }
        return $this->_dbTable;
    }
 
    public function save(Application_Model_NewsCategory $newscategory)
    {
        $data = array(
            'name'   => $newscategory->getName(),
            'description' => $newscategory->getDescription(),
        );
 
        if (($id = $newscategory->getId()) == '') {
            unset($data['id']);
            $this->getDbTable()->insert($data);
        } else {
            $this->getDbTable()->update($data, array('id = ?' => $id));
        }
    }
 
    public function delete($id)
    {
		$where = $this->getDbTable()->getAdapter()->quoteInto('id = ?',$id);
		$this->getDbTable()->delete($where);
    }
 
    public function find($id, Application_Model_NewsCategory $newscategory)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }
        $row = $result->current();
        $newscategory->setId($row->id)
                  ->setName($row->name)
                  ->setDescription($row->description)
                  ->setModified($row->modified);
    }
 
    public function titleSearch($title, Application_Model_NewsCategory $newscategory)
    {
        $result = $this->getDbTable();
		$select = $result->select()->where('LOWER(title) = ?',$title);
		$result = $result->fetchRow($select);
        if (0 == count($result)) {
            return;
        }
        $row = $result;
        $newscategory->setId($row->id)
                  ->setName($row->name)
                  ->setDescription($row->description)
                  ->setModified($row->modified);
    }
 
    public function fetchAll()
    {
        $resultSet = $this->getDbTable()->fetchAll();
        $entries   = array();
        foreach ($resultSet as $row) {
            $entry = new Application_Model_NewsCategory();
            $entry->setId($row->id)
                  ->setName($row->name)
                  ->setDescription($row->description)
                  ->setModified($row->modified);
            $entries[] = $entry;
        }
        return $entries;
    }
}