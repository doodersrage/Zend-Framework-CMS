<?PHP
// application/models/FormFieldsMapper.php
 
class Application_Model_FormFieldsMapper
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
            $this->setDbTable('Application_Model_DbTable_FormFields');
        }
        return $this->_dbTable;
    }
 
    public function delete($id)
    {
		$where = $this->getDbTable()->getAdapter()->quoteInto('id = ?',$id);
		$this->getDbTable()->delete($where);
    }
 
    public function save(Application_Model_FormFields $formfields)
    {
        $data = array(
            'form_id'   => $formfields->getForm_id(),
            'name'   => $formfields->getName(),
		 	'description'   => $formfields->getDescription(),
            'type'   => $formfields->getType(),
            'width'   => $formfields->getWidth(),
            'height'   => $formfields->getHeight(),
            'default_val'   => $formfields->getDefault_val(),
            'required'   => $formfields->getRequired(),
			'order_val' => $formfields->getOrder_val(),
        );
 
        if (($id = $formfields->getId()) == '') {
            unset($data['id']);
            $this->getDbTable()->insert($data);
        } else {
            $this->getDbTable()->update($data, array('id = ?' => $id));
        }
    }
 
    public function find($id, Application_Model_FormFields $formfields)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }
        $row = $result->current();
        $formfields->setId($row->id)
				  ->setForm_id($row->form_id)
				  ->setName($row->name)
				  ->setDescription($row->description)
				  ->setType($row->type)
				  ->setWidth($row->width)
				  ->setHeight($row->height)
				  ->setDefault_val($row->default_val)
				  ->setRequired($row->required)
				  ->setOrder_val($row->order_val);
    }
  
    public function fetchAll()
    {
        $resultSet = $this->getDbTable()->fetchAll();
        $entries   = array();
        foreach ($resultSet as $row) {
            $entry = new Application_Model_FormFields();
			$entry->setId($row->id)
				  ->setForm_id($row->form_id)
				  ->setName($row->name)
				  ->setDescription($row->description)
				  ->setType($row->type)
				  ->setWidth($row->width)
				  ->setHeight($row->height)
				  ->setDefault_val($row->default_val)
				  ->setRequired($row->required)
				  ->setOrder_val($row->order_val);
            $entries[] = $entry;
        }
        return $entries;
    }
}