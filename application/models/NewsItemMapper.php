<?PHP
// application/models/NewsItemMapper.php
 
class Application_Model_NewsItemMapper
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
            $this->setDbTable('Application_Model_DbTable_NewsItem');
        }
        return $this->_dbTable;
    }
 
    public function save(Application_Model_NewsItem $newscategory)
    {
        $data = array(
            'nc_id'   => $newscategory->getNc_id(),
            'name'   => $newscategory->getName(),
            'date_added'   => strtotime($newscategory->getDate_added()),
            'description' => $newscategory->getDescription(),
            'image' => $newscategory->getImage(),
            'remote_link' => $newscategory->getRemote_link(),
            'route_id' => $newscategory->getRoute_id(),
            'modified' => date('Y-m-d H:i:s'),
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
 
    public function find($id, Application_Model_NewsItem $newscategory)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }
        $row = $result->current();
        $newscategory->setId($row->id)
                  ->setNc_id($row->nc_id)
                  ->setName($row->name)
                  ->setDate_added($row->date_added)
                  ->setDescription($row->description)
                  ->setImage($row->image)
                  ->setRemote_link($row->remote_link)
                  ->setRoute_id($row->route_id)
                  ->setModified($row->modified);
    }
 
    public function titleSearch($title, Application_Model_NewsItem $newscategory)
    {
        $result = $this->getDbTable();
		$select = $result->select()->where('LOWER(title) = ?',$title);
		$result = $result->fetchRow($select);
        if (0 == count($result)) {
            return;
        }
        $row = $result;
        $newscategory->setId($row->id)
                  ->setNc_id($row->nc_id)
                  ->setName($row->name)
                  ->setDate_added($row->date_added)
                  ->setDescription($row->description)
                  ->setImage($row->image)
                  ->setRemote_link($row->remote_link)
                  ->setRoute_id($row->route_id)
                  ->setModified($row->modified);
    }
 
    public function fetchAll()
    {
        $resultSet = $this->getDbTable()->fetchAll();
        $entries   = array();
        foreach ($resultSet as $row) {
            $entry = new Application_Model_NewsItem();
            $entry->setId($row->id)
                  ->setNc_id($row->nc_id)
                  ->setName($row->name)
                  ->setDate_added($row->date_added)
                  ->setDescription($row->description)
                  ->setImage($row->image)
                  ->setRemote_link($row->remote_link)
                  ->setRoute_id($row->route_id)
                  ->setModified($row->modified);
            $entries[] = $entry;
        }
        return $entries;
    }
}