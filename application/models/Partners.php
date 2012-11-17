<?PHP
// application/models/Partners.php
 
class Application_Model_Partners
{
    protected $_id;
    protected $_name;
    protected $_description;
    protected $_image;
    protected $_link;
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
 
    public function setLink($link)
    {
        $this->_link = (string) $link;
        return $this;
    }
 
    public function getLink()
    {
        return $this->_link;
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