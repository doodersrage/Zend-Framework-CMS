<?PHP
// application/models/PartnersMapper.php
 
class Application_Model_PartnersMapper
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
            $this->setDbTable('Application_Model_DbTable_Partners');
        }
        return $this->_dbTable;
    }
 
    public function save(Application_Model_Partners $partners)
    {
        $data = array(
            'name'   => $partners->getName(),
            'description' => $partners->getDescription(),
            'image' => $partners->getImage(),
            'link' => $partners->getLink(),
            'modified' => date('Y-m-d H:i:s'),
        );
 
        if (($id = $partners->getId()) == '') {
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
 
    public function find($id, Application_Model_Partners $partners)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }
        $row = $result->current();
        $partners->setId($row->id)
                  ->setName($row->name)
                  ->setDescription($row->description)
                  ->setImage ($row->image )
                  ->setLink ($row->link )
                  ->setModified($row->modified);
    }
 
    public function titleSearch($title, Application_Model_Partners $partners)
    {
        $result = $this->getDbTable();
		$select = $result->select()->where('LOWER(name) = ?',$title);
		$result = $result->fetchRow($select);
        if (0 == count($result)) {
            return;
        }
        $row = $result;
        $partners->setId($row->id)
                  ->setName($row->name)
                  ->setDescription($row->description)
                  ->setImage ($row->image )
                  ->setLink ($row->link )
                  ->setModified($row->modified);
    }
 
    public function fetchAll()
    {
        $resultSet = $this->getDbTable()->fetchAll();
        $entries   = array();
        foreach ($resultSet as $row) {
            $entry = new Application_Model_Partners();
            $entry->setId($row->id)
                  ->setName($row->name)
                  ->setDescription($row->description)
                  ->setImage ($row->image )
                  ->setLink ($row->link )
                  ->setModified($row->modified);
            $entries[] = $entry;
        }
        return $entries;
    }
}