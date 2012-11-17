<?PHP
// application/models/Routes.php
 
class Application_Model_Routes
{
    protected $_id;
    protected $_type;
    protected $_seg_id;
    protected $_uri;
 
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
  
    public function setType($type)
    {
        $this->_type = (string) $type;
        return $this;
    }
 
    public function getType()
    {
        return $this->_type;
    }
  
    public function setSeg_id($seg_id)
    {
        $this->_seg_id = (int) $seg_id;
        return $this;
    }
 
    public function getSeg_id()
    {
        return $this->_seg_id;
    }
  
    public function setUri($uri)
    {
        $this->_uri = (string) $uri;
        return $this;
    }
 
    public function getUri()
    {
        return $this->_uri;
    }
}
?>