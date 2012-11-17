<?PHP
// application/models/VideosLinkMapper.php
 
class Application_Model_VideosLinkMapper
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
            $this->setDbTable('Application_Model_DbTable_VideosLink');
        }
        return $this->_dbTable;
    }
 
    public function save(Application_Model_VideosLink $videosLink)
    {
        $data = array(
            'video_id'   => $videosLink->getVideoId(),
            'link_id' => $videosLink->getLinkId(),
            'link_type' => $videosLink->getLinkType(),
            'sort' => $videosLink->getSort(),
        );
 
        if (($id = $videosLink->getId()) == '') {
            unset($data['id']);
            $this->getDbTable()->insert($data);
        } else {
            $this->getDbTable()->update($data, array('id = ?' => $id));
        }
    }
 
    public function find($id, Application_Model_VideosLink $videosLink)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }
        $row = $result->current();
        $videosLink->setId($row->id)
                  ->setVideoId($row->video_id)
                  ->setLinkId($row->link_id)
                  ->setLinkType($row->link_type)
                  ->setSort($row->sort);
    }
 
    public function fetchAll()
    {
        $resultSet = $this->getDbTable()->fetchAll();
        $entries   = array();
        foreach ($resultSet as $row) {
            $entry = new Application_Model_Partners();
            $entry->setId($row->id)
                  ->setVideoId($row->video_id)
                  ->setLinkId($row->link_id)
                  ->setLinkType($row->link_type)
                  ->setSort($row->sort);
            $entries[] = $entry;
        }
        return $entries;
    }
}