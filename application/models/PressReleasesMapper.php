<?PHP
// application/models/PressReleases.php
 
class Application_Model_PressReleasesMapper
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
            $this->setDbTable('Application_Model_DbTable_PressReleases');
        }
        return $this->_dbTable;
    }
 
    public function delete($id)
    {
		$where = $this->getDbTable()->getAdapter()->quoteInto('id = ?',$id);
		$this->getDbTable()->delete($where);
    }
 
    public function save(Application_Model_PressReleases $pressreleases)
    {
        $data = array(
            'title'   => $pressreleases->getTitle(),
            'date' => $pressreleases->getDate(),
            'copy_text' => $pressreleases->getCopy_text(),
            'filelnk' => $pressreleases->getFilelnk(),
        );
 
        if (($id = $pressreleases->getId()) == '') {
           unset($data['id']);
            $this->getDbTable()->insert($data);
        } else {
            $this->getDbTable()->update($data, array('id = ?' => $id));
        }
    }
 
    public function find($id, Application_Model_PressReleases $pressreleases)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }
        $row = $result->current();
        $pressreleases->setId($row->id)
			  ->setTitle($row->title)
			  ->setDate($row->date)
			  ->setCopy_text($row->copy_text)
			  ->setFilelnk($row->filelnk)
			  ->setModified($row->modified);
    }
  
    public function fetchAll()
    {
        $resultSet = $this->getDbTable()->fetchAll();
        $entries   = array();
        foreach ($resultSet as $row) {
            $entry = new Application_Model_PressReleases();
			$entry->setId($row->id)
			  ->setTitle($row->title)
			  ->setDate($row->date)
			  ->setCopy_text($row->copy_text)
			  ->setFilelnk($row->filelnk)
			  ->setModified($row->modified);
            $entries[] = $entry;
        }
        return $entries;
    }
}