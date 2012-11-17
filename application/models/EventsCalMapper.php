<?PHP
// application/models/EventsMapper.php
 
class Application_Model_EventsCalMapper
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
            $this->setDbTable('Application_Model_DbTable_EventsCal');
        }
        return $this->_dbTable;
    }
 
    public function delete($id)
    {
		$where = $this->getDbTable()->getAdapter()->quoteInto('id = ?',$id);
		$this->getDbTable()->delete($where);
    }
 
    public function save(Application_Model_EventsCal $event)
    {
        $data = array(
            'start'   => $event->getStart(),
            'finish'   => $event->getFinish(),
            'neighborhood'   => $event->getNeighborhood(),
            'name'   => $event->getName(),
            'description'   => $event->getDescription(),
            'short_desc'   => $event->getShort_desc(),
            'image'   => $event->getImage(),
            'route_id'   => $event->getRoute_id(),
        );
 
        if (($id = $event->getId()) == '') {
           unset($data['id']);
            $this->getDbTable()->insert($data);
        } else {
            $this->getDbTable()->update($data, array('id = ?' => $id));
        }
    }
  
    public function find($id, Application_Model_EventsCal $event)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }
        $row = $result->current();
        $event->setId($row->id)
			  ->setStart($row->start)
			  ->setFinish($row->finish)
			  ->setNeighborhood($row->neighborhood)
			  ->setName($row->name)
			  ->setDescription($row->description)
			  ->setShort_desc($row->short_desc)
			  ->setImage($row->image)
			  ->setRoute_id($row->route_id);
    }
  
    public function fetchAll()
    {
        $resultSet = $this->getDbTable()->fetchAll();
        $entries   = array();
        foreach ($resultSet as $row) {
            $entry = new Application_Model_EventsCal();
			$entry->setId($row->id)
				  ->setStart($row->start)
				  ->setFinish($row->finish)
				  ->setNeighborhood($row->neighborhood)
				  ->setName($row->name)
				  ->setDescription($row->description)
				  ->setShort_desc($row->short_desc)
				  ->setImage($row->image)
				  ->setRoute_id($row->route_id);
            $entries[] = $entry;
        }
        return $entries;
    }
}