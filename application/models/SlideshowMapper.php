<?PHP
// application/models/SlideshowMapper.php
 
class Application_Model_SlideshowMapper
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
            $this->setDbTable('Application_Model_DbTable_Slideshow');
        }
        return $this->_dbTable;
    }
 
    public function delete($id)
    {
		$where = $this->getDbTable()->getAdapter()->quoteInto('id = ?',$id);
		$this->getDbTable()->delete($where);
    }
 
    public function save(Application_Model_Slideshow $slideshow)
    {
        $data = array(
            'name'   => $slideshow->getName(),
            'height'   => $slideshow->getHeight(),
            'width'   => $slideshow->getWidth(),
            'autoplay'   => $slideshow->getAutoplay(),
            'controlbar'   => $slideshow->getControlbar(),
            'playlistvisual'   => $slideshow->getPlaylistvisual(),
        );
 
        if (($id = $slideshow->getId()) == '') {
           unset($data['id']);
            $this->getDbTable()->insert($data);
        } else {
            $this->getDbTable()->update($data, array('id = ?' => $id));
        }
    }
 
    public function find($id, Application_Model_Slideshow $slideshow)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }
        $row = $result->current();
        $slideshow->setId($row->id)
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
            $entry = new Application_Model_Slideshow();
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