<?PHP
// application/models/SlideshowDataMapper.php
 
class Application_Model_SlideshowDataMapper
{
    protected $_dbTable;
 
    public function setDbTable($dbTable)
    {
        if (is_string($dbTable)) {
            $dbTable = new $dbTable();
        }
        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception('Invalid table data gateway propictureed');
        }
        $this->_dbTable = $dbTable;
        return $this;
    }
 
    public function getDbTable()
    {
        if (null === $this->_dbTable) {
            $this->setDbTable('Application_Model_DbTable_SlideshowData');
        }
        return $this->_dbTable;
    }
 
    public function delete($pid,$picture)
    {
		$values = array($pid,$picture);
		$where = '(pid = ? AND img = ?)';
		foreach($values as $value) $where = $this->getDbTable()->getAdapter()->quoteInto($where,$value,'',1);
		$this->getDbTable()->delete($where);
    }
 
    public function find(Application_Model_SlideshowData $slideshow)
    {
		$db = Zend_Registry::get('db');
		$select = $db->select()
					->from('slideshow_data');
		$select->where('pid = ?', $slideshow->getPid());
		$select->where('img = ?', $slideshow->getImg());
		$select->order('sort_order ASC');
		$row = $db->fetchRow($select);
		$slideshow->setPid($row[pid])
				->setImg($row[img])
				->setDesc($row[desc])
				->setSort_order($row[sort_order]);
    }
 
    public function save(Application_Model_SlideshowData $slideshow)
    {
        $data = array(
            'pid'   => $slideshow->getPid(),
            'img'   => $slideshow->getImg(),
            'desc'   => $slideshow->getDesc(),
            'sort_order'   => $slideshow->getSort_order(),
        );
 
		$this->getDbTable()->insert($data);
    }
 
    public function update(Application_Model_SlideshowData $slideshow)
    {
        $data = array(
            'pid'   => $slideshow->getPid(),
            'img'   => $slideshow->getImg(),
            'desc'   => $slideshow->getDesc(),
            'sort_order'   => $slideshow->getSort_order(),
        );
 
		$this->getDbTable()->update($data, array(
											'pid = ?'   => $slideshow->getPid(),
											'img = ?'   => $slideshow->getImg(),
											));
    }
 
    public function sortPicture(Application_Model_SlideshowData $slideshow)
    {
        $data = array(
            'sort_order'   => $slideshow->getSort_order(),
        );
 
		$this->getDbTable()->update($data, array(
											'pid = ?'   => $slideshow->getPid(),
											'img = ?'   => $slideshow->getImg(),
											));
    }
 
//    public function find($id, Application_Model_DbTable_SlideshowData $slideshow)
//    {
//        $result = $this->getDbTable()->find($id);
//        if (0 == count($result)) {
//            return;
//        }
//        $row = $result->current();
//        $slideshow->setId($row->id)
//			  ->setName($row->name)
//			  ->setModified($row->modified);
//    }
  
    public function fetchAll($pid)
    {
		$db = Zend_Registry::get('db');
		$select = $db->select()
					->from('slideshow_data');
		$select->where('pid = ?', $pid);
		$select->order('sort_order DESC')
				->order('img ASC');
		$resultSet = $db->fetchAll($select);
        $entries   = array();
        foreach ($resultSet as $row) {
            $entry = new Application_Model_SlideshowData();
			$entry->setPid($row[pid])
				  ->setImg($row[img])
				  ->setDesc($row[desc])
				  ->setSort_order($row[sort_order]);
            $entries[] = $entry;
        }
        return $entries;
    }
}