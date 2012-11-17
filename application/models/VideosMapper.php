<?PHP
// application/models/VideosMapper.php
 
class Application_Model_VideosMapper
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
            $this->setDbTable('Application_Model_DbTable_Videos');
        }
        return $this->_dbTable;
    }
 
    public function delete($id)
    {
		$where = $this->getDbTable()->getAdapter()->quoteInto('id = ?',$id);
		$this->getDbTable()->delete($where);
    }
 
    public function save(Application_Model_Videos $videos)
    {
        $data = array(
            'name'   => $videos->getName(),
            'description' => $videos->getDescription(),
            'image' => $videos->getImage(),
            'remote' => $videos->getRemote(),
            'local' => $videos->getLocal(),
            'modified' => date('Y-m-d H:i:s'),
        );
 
        if (($id = $videos->getId()) == '') {
            unset($data['id']);
            $this->getDbTable()->insert($data);
        } else {
            $this->getDbTable()->update($data, array('id = ?' => $id));
        }
    }
 
    public function find($id, Application_Model_Videos $videos)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }
        $row = $result->current();
        $videos->setId($row->id)
                  ->setName($row->name)
                  ->setDescription($row->description)
                  ->setImage($row->image)
                  ->setRemote($row->remote)
                  ->setLocal($row->local)
                  ->setModified($row->modified);
    }
 
    public function titleSearch($name, Application_Model_Videos $videos)
    {
        $result = $this->getDbTable();
		$select = $result->select()->where('LOWER(name) = ?',$name);
		$result = $result->fetchRow($select);
        if (0 == count($result)) {
            return;
        }
        $row = $result;
        $videos->setId($row->id)
                  ->setName($row->name)
                  ->setDescription($row->description)
                  ->setImage($row->image)
                  ->setRemote($row->remote)
                  ->setLocal($row->local)
                  ->setModified($row->modified);
    }
  
    public function fetchSearchAll($pid)
    {
        $where = $this->getDbTable()->getAdapter()->quoteInto('name like ?','%'.$pid.'%');
		$resultSet = $this->getDbTable()->fetchAll($where);
        $entries   = array();
        foreach ($resultSet as $row) {
            $entry = new Application_Model_Videos();
            $entry->setId($row->id)
                  ->setName($row->name)
                  ->setDescription($row->description)
                  ->setImage($row->image)
                  ->setRemote($row->remote)
                  ->setLocal($row->local)
                  ->setModified($row->modified);
            $entries[] = $entry;
        }
        return $entries;
    }
 
    public function fetchAll()
    {
        $resultSet = $this->getDbTable()->fetchAll();
        $entries   = array();
        foreach ($resultSet as $row) {
            $entry = new Application_Model_Videos();
            $entry->setId($row->id)
                  ->setName($row->name)
                  ->setDescription($row->description)
                  ->setImage($row->image)
                  ->setRemote($row->remote)
                  ->setLocal($row->local)
                  ->setModified($row->modified);
            $entries[] = $entry;
        }
        return $entries;
    }
}