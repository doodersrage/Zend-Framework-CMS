<?PHP
// application/models/ReferenceMaterialsMapper.php
 
class Application_Model_ReferenceMaterialsMapper
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
            $this->setDbTable('Application_Model_DbTable_ReferenceMaterials');
        }
        return $this->_dbTable;
    }
 
    public function save(Application_Model_ReferenceMaterials $referencematerials)
    {
        $data = array(
            'filelnk'   => $referencematerials->getFilelnk(),
            'new_window'   => $referencematerials->getNew_window(),
            'name'   => $referencematerials->getName(),
            'description' => $referencematerials->getDescription(),
            'image' => $referencematerials->getImage(),
            'modified' => date('Y-m-d H:i:s'),
        );
 
        if (($id = $referencematerials->getId()) == '') {
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
 
    public function find($id, Application_Model_ReferenceMaterials $referencematerials)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }
        $row = $result->current();
        $referencematerials->setId($row->id)
                  ->setFilelnk($row->filelnk)
                  ->setNew_window($row->new_window)
                  ->setName($row->name)
                  ->setDescription($row->description)
                  ->setImage($row->image)
                  ->setModified($row->modified);
    }
 
    public function titleSearch($title, Application_Model_ReferenceMaterials $referencematerials)
    {
        $result = $this->getDbTable();
		$select = $result->select()->where('LOWER(title) = ?',$title);
		$result = $result->fetchRow($select);
        if (0 == count($result)) {
            return;
        }
        $row = $result;
        $referencematerials->setId($row->id)
                  ->setFilelnk($row->filelnk)
                  ->setNew_window($row->new_window)
                  ->setName($row->name)
                  ->setDescription($row->description)
                  ->setImage($row->image)
                  ->setModified($row->modified);
    }
 
    public function fetchAll()
    {
        $resultSet = $this->getDbTable()->fetchAll();
        $entries   = array();
        foreach ($resultSet as $row) {
            $entry = new Application_Model_ReferenceMaterials();
            $entry->setId($row->id)
                  ->setFilelnk($row->filelnk)
                  ->setNew_window($row->new_window)
                  ->setName($row->name)
                  ->setDescription($row->description)
                  ->setImage($row->image)
                  ->setModified($row->modified);
            $entries[] = $entry;
        }
        return $entries;
    }
}