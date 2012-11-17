<?PHP
// application/models/ReferenceMaterials.php
 
class Application_Model_ReferenceMaterials
{
    protected $_id;
    protected $_filelnk;
    protected $_new_window;
    protected $_name;
    protected $_description;
    protected $_image;
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
 
    public function setFilelnk($filelnk)
    {
        $this->_filelnk = (string) $filelnk;
        return $this;
    }
 
    public function getFilelnk()
    {
        return $this->_filelnk;
    }
 
    public function setNew_window($new_window)
    {
        $this->_new_window = (int) $new_window;
        return $this;
    }
 
    public function getNew_window()
    {
        return $this->_new_window;
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
 
    public function setDescription($description)
    {
        $this->_description = (string) $description;
        return $this;
    }
 
    public function getDescription()
    {
        return $this->_description;
    }
 
    public function setImage($image)
    {
        $this->_image = (string) $image;
        return $this;
    }
 
    public function getImage()
    {
        return $this->_image;
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