<?PHP
// application/models/Docs.php
 
class Application_Model_Docs
{
    protected $_id;
    protected $_filename;
    protected $_modified;
 
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
  
    public function setFilename($filename)
    {
        $this->_filename = (string) $filename;
        return $this;
    }
 
    public function getFilename()
    {
        return $this->_filename;
    }
 
    public function setModified($modified)
    {
        $this->_modified = (string) $modified;
        return $this;
    }
 
    public function getModified()
    {
        return $this->_modified;
    }
 
}
?>