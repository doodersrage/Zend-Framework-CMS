<?PHP
// application/models/Redirects.php
 
class Application_Model_Redirects
{
    protected $_id;
    protected $_olduri;
    protected $_newuri;
 
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
  
    public function setOlduri($olduri)
    {
        $this->_olduri = (string) $olduri;
        return $this;
    }
 
    public function getOlduri()
    {
        return $this->_olduri;
    }
  
    public function setNewuri($newuri)
    {
        $this->_newuri = (string) $newuri;
        return $this;
    }
 
    public function getNewuri()
    {
        return $this->_newuri;
    }
}
?>