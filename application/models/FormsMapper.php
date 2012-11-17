<?PHP
// application/models/FormsMapper.php
 
class Application_Model_FormsMapper
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
            $this->setDbTable('Application_Model_DbTable_Forms');
        }
        return $this->_dbTable;
    }
 
    public function delete($id)
    {
		$where = $this->getDbTable()->getAdapter()->quoteInto('id = ?',$id);
		$this->getDbTable()->delete($where);
    }
 
    public function save(Application_Model_Forms $forms)
    {
        $data = array(
            'name'   => $forms->getName(),
            'description'   => $forms->getDescription(),
            'email'   => $forms->getEmail(),
            'message'   => $forms->getMessage(),
            'captcha'   => $forms->getCaptcha(),
        );
 
        if (($id = $forms->getId()) == '') {
            unset($data['id']);
            $this->getDbTable()->insert($data);
        } else {
            $this->getDbTable()->update($data, array('id = ?' => $id));
        }
    }
 
    public function find($id, Application_Model_Forms $forms)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }
        $row = $result->current();
        $forms->setId($row->id)
			  ->setName($row->name)
			  ->setDescription($row->description)
			  ->setEmail($row->email)
			  ->setMessage($row->message)
			  ->setCaptcha($row->captcha);
    }
  
    public function fetchAll()
    {
        $resultSet = $this->getDbTable()->fetchAll();
        $entries   = array();
        foreach ($resultSet as $row) {
            $entry = new Application_Model_Forms();
			$entry->setId($row->id)
			  ->setName($row->name)
			  ->setDescription($row->description)
			  ->setEmail($row->email)
			  ->setMessage($row->message)
			  ->setCaptcha($row->captcha);
            $entries[] = $entry;
        }
        return $entries;
    }
}