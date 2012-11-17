<?PHP
// application/models/PlaylistMapper.php
 
class Application_Model_PlaylistMapper
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
            $this->setDbTable('Application_Model_DbTable_Playlist');
        }
        return $this->_dbTable;
    }
 
    public function delete($id)
    {
		$where = $this->getDbTable()->getAdapter()->quoteInto('id = ?',$id);
		$this->getDbTable()->delete($where);
    }
 
    public function save(Application_Model_Playlist $playlist)
    {
        $data = array(
            'name'   => $playlist->getName(),
            'height'   => $playlist->getHeight(),
            'width'   => $playlist->getWidth(),
            'autoplay'   => $playlist->getAutoplay(),
            'controlbar'   => $playlist->getControlbar(),
            'playlistvisual'   => $playlist->getPlaylistvisual(),
        );
 
        if (($id = $playlist->getId()) == '') {
           unset($data['id']);
            $this->getDbTable()->insert($data);
        } else {
            $this->getDbTable()->update($data, array('id = ?' => $id));
        }
    }
 
    public function find($id, Application_Model_Playlist $playlist)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }
        $row = $result->current();
        $playlist->setId($row->id)
			  ->setName($row->name)
			  ->setHeight($row->height)
			  ->setWidth($row->width)
			  ->setAutoplay($row->autoplay)
			  ->setControlbar($row->controlbar)
			  ->setPlaylistvisual($row->playlistvisual)
			  ->setModified($row->modified);
    }
  
    public function fetchAll()
    {
        $resultSet = $this->getDbTable()->fetchAll();
        $entries   = array();
        foreach ($resultSet as $row) {
            $entry = new Application_Model_Playlist();
			$entry->setId($row->id)
				  ->setName($row->name)
				  ->setHeight($row->height)
				  ->setWidth($row->width)
				  ->setAutoplay($row->autoplay)
				  ->setControlbar($row->controlbar)
				  ->setPlaylistvisual($row->playlistvisual)
				  ->setModified($row->modified);
            $entries[] = $entry;
        }
        return $entries;
    }
}