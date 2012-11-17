<?PHP
// application/models/PagesMapper.php
 
class Application_Model_PagesMapper
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
            $this->setDbTable('Application_Model_DbTable_Pages');
        }
        return $this->_dbTable;
    }
 
    public function delete($id)
    {
		$where = $this->getDbTable()->getAdapter()->quoteInto('id = ?',$id);
		$this->getDbTable()->delete($where);
    }
	
    public function save(Application_Model_Pages $pages)
    {
        $data = array(
            'title'   => $pages->getTitle(),
            'filelnk'   => $pages->getFilelnk(),
            'copy_text' => $pages->getCopy_text(),
            'mobile_text' => $pages->getMobile_text(),
            'seo_text' => $pages->getSeo_text(),
            'parent_id' => $pages->getParent_id(),
            'playlist' => $pages->getPlaylist(),
            'slideshow' => $pages->getSlideshow(),
            'form_id' => $pages->getForm_id(),
			'route_id' => $pages->getRoute_id(),
			'link_name' => $pages->getLink_name(),
            'title_tag' => $pages->getTitle_tag(),
            'desc_tag' => $pages->getDesc_tag(),
            'keyword_tag' => $pages->getKeyword_tag(),
            'pgsort' => $pages->getPgsort(),
            'calendar' => $pages->getCalendar(),
            'menu' => $pages->getMenu(),
            'new_window' => $pages->getNew_window(),
            'modified' => date('Y-m-d H:i:s'),
        );
 
        if (($id = $pages->getId()) == '') {
            unset($data['id']);
            $this->getDbTable()->insert($data);
        } else {
            $this->getDbTable()->update($data, array('id = ?' => $id));
        }
    }
 
    public function find($id, Application_Model_Pages $pages)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }
        $row = $result->current();
        $pages->setId($row->id)
                  ->setTitle($row->title)
                  ->setFilelnk($row->filelnk)
                  ->setCopy_text($row->copy_text)
                  ->setMobile_text($row->mobile_text)
                  ->setSeo_text($row->seo_text)
                  ->setParent_id($row->parent_id)
                  ->setPlaylist($row->playlist)
                  ->setSlideshow($row->slideshow)
                  ->setForm_id($row->form_id)
				  ->setRoute_id($row->route_id)
				  ->setLink_name($row->link_name)
                  ->setTitle_tag($row->title_tag)
                  ->setDesc_tag($row->desc_tag)
                  ->setKeyword_tag($row->keyword_tag)
                  ->setPgsort($row->pgsort)
                  ->setCalendar($row->calendar)
                  ->setMenu($row->menu)
                  ->setNew_window($row->new_window)
                  ->setModified($row->modified);
    }
 
    public function titleSearch($title, Application_Model_Pages $pages)
    {
        $result = $this->getDbTable();
		$select = $result->select()->where('LOWER(title) = ?',strtolower($title));
		$result = $result->fetchRow($select);
        if (0 == count($result)) {
            return;
        }
        $row = $result;
        $pages->setId($row->id)
                  ->setTitle($row->title)
                  ->setFilelnk($row->filelnk)
                  ->setCopy_text($row->copy_text)
                  ->setMobile_text($row->mobile_text)
                  ->setSeo_text($row->seo_text)
                  ->setParent_id($row->parent_id)
                  ->setPlaylist($row->playlist)
                  ->setSlideshow($row->slideshow)
                  ->setForm_id($row->form_id)
				  ->setRoute_id($row->route_id)
                  ->setTitle_tag($row->title_tag)
                  ->setDesc_tag($row->desc_tag)
				  ->setLink_name($row->link_name)
                  ->setKeyword_tag($row->keyword_tag)
                  ->setPgsort($row->pgsort)
                  ->setCalendar($row->calendar)
                  ->setMenu($row->menu)
                  ->setNew_window($row->new_window)
                  ->setModified($row->modified);
    }
 
    public function linkNameSearch($link_name, Application_Model_Pages $pages)
    {
        $result = $this->getDbTable();
		$select = $result->select()->where('LOWER(link_name) = ?',strtolower($link_name));
		$result = $result->fetchRow($select);
        if (0 == count($result)) {
            return;
        }
        $row = $result;
        $pages->setId($row->id)
                  ->setTitle($row->title)
                  ->setFilelnk($row->filelnk)
                  ->setCopy_text($row->copy_text)
                  ->setMobile_text($row->mobile_text)
                  ->setSeo_text($row->seo_text)
                  ->setParent_id($row->parent_id)
                  ->setPlaylist($row->playlist)
                  ->setSlideshow($row->slideshow)
                  ->setForm_id($row->form_id)
				  ->setRoute_id($row->route_id)
                  ->setTitle_tag($row->title_tag)
                  ->setDesc_tag($row->desc_tag)
				  ->setLink_name($row->link_name)
                  ->setKeyword_tag($row->keyword_tag)
                  ->setPgsort($row->pgsort)
                  ->setCalendar($row->calendar)
                  ->setMenu($row->menu)
                  ->setNew_window($row->new_window)
                  ->setModified($row->modified);
    }
 
    public function fetchAll()
    {
        $resultSet = $this->getDbTable()->fetchAll();
        $entries   = array();
        foreach ($resultSet as $row) {
            $entry = new Application_Model_Pages();
            $entry->setId($row->id)
                  ->setTitle($row->title)
                  ->setFilelnk($row->filelnk)
                  ->setCopy_text($row->copy_text)
                  ->setMobile_text($row->mobile_text)
                  ->setSeo_text($row->seo_text)
                  ->setParent_id($row->parent_id)
                  ->setPlaylist($row->playlist)
                  ->setSlideshow($row->slideshow)
                  ->setForm_id($row->form_id)
				  ->setRoute_id($row->route_id)
                  ->setTitle_tag($row->title_tag)
                  ->setDesc_tag($row->desc_tag)
				  ->setLink_name($row->link_name)
                  ->setKeyword_tag($row->keyword_tag)
                  ->setPgsort($row->pgsort)
                  ->setCalendar($row->calendar)
                  ->setMenu($row->menu)
                  ->setNew_window($row->new_window)
                  ->setModified($row->modified);
            $entries[] = $entry;
        }
        return $entries;
    }
}