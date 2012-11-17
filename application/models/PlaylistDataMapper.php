<?PHP
// application/models/PlaylistDataMapper.php
 
class Application_Model_PlaylistDataMapper
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
            $this->setDbTable('Application_Model_DbTable_PlaylistData');
        }
        return $this->_dbTable;
    }
 
    public function delete($pid,$vid)
    {
		$values = array($pid,$vid);
		$where = '(pid = ? AND vid = ?)';
		foreach($values as $value) $where = $this->getDbTable()->getAdapter()->quoteInto($where,$value,'',1);
		$this->getDbTable()->delete($where);
    }
 
    public function find(Application_Model_PlaylistData $playlist)
    {
		$db = Zend_Registry::get('db');
		$select = $db->select()
					->from('playlist_data');
		$select->where('pid = ?', $playlist->getPid());
		$select->where('vid = ?', $playlist->getVid());
		$select->order('sort_order ASC');
		$row = $db->fetchRow($select);
		$playlist->setPid($row[pid])
				->setVid($row[vid])
				->setSort_order($row[sort_order]);
    }
 
    public function save(Application_Model_PlaylistData $playlist)
    {
        $data = array(
            'pid'   => $playlist->getPid(),
            'vid'   => $playlist->getVid(),
            'sort_order'   => 0,
        );
 
		$this->getDbTable()->insert($data);
    }
 
    public function sortVid(Application_Model_PlaylistData $playlist)
    {
        $data = array(
            'sort_order'   => $playlist->getSort_order(),
        );
 
		$this->getDbTable()->update($data, array('pid = ?'   => $playlist->getPid(),'vid = ?'   => $playlist->getVid()));
    }
 
//    public function find($id, Application_Model_DbTable_PlaylistData $playlist)
//    {
//        $result = $this->getDbTable()->find($id);
//        if (0 == count($result)) {
//            return;
//        }
//        $row = $result->current();
//        $playlist->setId($row->id)
//			  ->setName($row->name)
//			  ->setModified($row->modified);
//    }
  
    public function fetchAll($pid)
    {
		$db = Zend_Registry::get('db');
		$select = $db->select()
					->from(array('pld'=>'playlist_data'))
					->joinInner(array('vd'=>'videos'),'vd.id=pld.vid');
		$select->where('pld.pid = ?', $pid);
		$select->order('pld.sort_order DESC');
		$select->order('vd.name ASC');
		$resultSet = $db->fetchAll($select);
        $entries   = array();
        foreach ($resultSet as $row) {
            $entry = new Application_Model_PlaylistData();
			$entry->setPid($row[pid])
				  ->setVid($row[vid])
				  ->setSort_order($row[sort_order]);
            $entries[] = $entry;
        }
        return $entries;
    }
}