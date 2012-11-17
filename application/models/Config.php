<?PHP
// application/models/Config.php
 
class Application_Model_Config
{
    protected $_id;
    protected $_cat_id;
    protected $_name;
    protected $_type;
    protected $_defin;
    protected $_funct;
 
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
 
    public function setCat_id($cat_id)
    {
        $this->_cat_id = (int) $cat_id;
        return $this;
    }
 
    public function getCat_id()
    {
        return $this->_cat_id;
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
 
    public function setType($type)
    {
        $this->_type = (string) $type;
        return $this;
    }
 
    public function getType()
    {
        return $this->_type;
    }
 
    public function setDefin($defin)
    {
        $this->_defin = (string) $defin;
        return $this;
    }
 
    public function getDefin()
    {
        return $this->_defin;
    }
 
    public function setFunct($funct)
    {
        $this->_funct = (string) $funct;
        return $this;
    }
 
    public function getFunct()
    {
        return $this->_funct;
    }
 
}
?>