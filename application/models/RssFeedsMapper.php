<?PHP
// application/models/RssFeedsMapper.php
 
class Application_Model_RssFeedsMapper
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
            $this->setDbTable('Application_Model_DbTable_RssFeeds');
        }
        return $this->_dbTable;
    }
 
    public function delete($id)
    {
		$where = $this->getDbTable()->getAdapter()->quoteInto('id = ?',$id);
		$this->getDbTable()->delete($where);
    }
 
    public function save(Application_Model_RssFeeds $docs)
    {
        $data = array(
            'name'   => $docs->getName(),
            'url' => $docs->getUrl(),
        );
 
        if (($id = $docs->getId()) == '') {
            unset($data['id']);
            $this->getDbTable()->insert($data);
        } else {
            $this->getDbTable()->update($data, array('id = ?' => $id));
        }
    }
 
    public function find($id, Application_Model_RssFeeds $docs)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }
        $row = $result->current();
        $docs->setId($row->id)
			  ->setName($row->name)
			  ->setUrl($row->url);
    }
  
    public function fetchAll()
    {
        $resultSet = $this->getDbTable()->fetchAll();
        $entries   = array();
        foreach ($resultSet as $row) {
            $entry = new Application_Model_RssFeeds();
			$entry->setId($row->id)
				  ->setName($row->name)
				  ->setUrl($row->url);
            $entries[] = $entry;
        }
        return $entries;
    }
}