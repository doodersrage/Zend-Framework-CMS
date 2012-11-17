<?PHP
// application/models/ConfigMapper.php
 
class Application_Model_ConfigMapper
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
            $this->setDbTable('Application_Model_DbTable_Config');
        }
        return $this->_dbTable;
    }
 
    public function delete($id)
    {
		$where = $this->getDbTable()->getAdapter()->quoteInto('id = ?',$id);
		$this->getDbTable()->delete($where);
    }
 
    public function save(Application_Model_Config $config)
    {
        $data = array(
            'cat_id'   => $config->getCat_id(),
            'name'   => $config->getName(),
            'type' => $config->getType(),
            'defin' => $config->getDefin(),
            'funct' => $config->getFunct(),
        );
 
        if (($id = $config->getId()) == '') {
            unset($data['id']);
            $this->getDbTable()->insert($data);
        } else {
            $this->getDbTable()->update($data, array('id = ?' => $id));
        }
    }
 
    public function find($id, Application_Model_Config $config)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }
        $row = $result->current();
        $config->setId($row->id)
			  ->setCat_id($row->cat_id)
			  ->setName($row->name)
			  ->setType($row->type)
			  ->setDefin($row->defin)
			  ->setFunct($row->funct);
    }
  
    public function fetchAll()
    {
        $resultSet = $this->getDbTable()->fetchAll();
        $entries   = array();
        foreach ($resultSet as $row) {
            $config = new Application_Model_Config();
			$config->setId($row->id)
				  ->setCat_id($row->cat_id)
				  ->setName($row->name)
				  ->setType($row->type)
				  ->setDefin($row->defin)
				  ->setFunct($row->funct);
            $entries[] = $config;
        }
        return $entries;
    }
}