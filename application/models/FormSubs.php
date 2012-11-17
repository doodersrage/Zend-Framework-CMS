<?PHP
// application/models/Users.php
 
class Application_Model_FormSubs
{
    protected $_id;
    protected $_values;
    protected $_type;
    protected $_date;
 
    public function __construct()
    {
    }
 
    public function __set($name, $value)
    {
        $method = 'set' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid table property');
        }
        $this->$method($value);
    }
 
    public function __get($name)
    {
        $method = 'get' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid table property');
        }
        return $this->$method();
    }
 
    public function setId($id)
    {
        $this->_id = (int) $id;
        return $this;
    }
 
    public function getId()
    {
        return $this->_id;
    }
  
    public function setValues($values)
    {
        $this->_values = $values;
        return $this;
    }
 
    public function getValues()
    {
        return $this->_values;
    }
 
    public function setType($type)
    {
        $this->_type = $type;
        return $this;
    }
 
    public function getType()
    {
        return $this->_type;
    }
 
    public function setDate($date)
    {
        $this->_date = $date;
        return $this;
    }
 
    public function getDate()
    {
        return $this->_date;
    }
}
?>