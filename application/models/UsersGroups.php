<?PHP
// application/models/UsersGroups.php
 
class Application_Model_UsersGroups
{
    protected $_id;
    protected $_name;
    protected $_restrictions;
 
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
  
    public function setName($name)
    {
        $this->_name = (string) $name;
        return $this;
    }
 
    public function getName()
    {
        return $this->_name;
    }
 
    public function setRestrictions($restrictions)
    {
        $this->_restrictions = (string) $restrictions;
        return $this;
    }
 
    public function getRestrictions()
    {
        return $this->_restrictions;
    }
}
?>