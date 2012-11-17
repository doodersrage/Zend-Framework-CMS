<?PHP
// application/models/NewsItem.php
 
class Application_Model_NewsItem
{
    protected $_id;
    protected $_nc_id;
    protected $_name;
	protected $_date_added;
    protected $_description;
    protected $_image;
    protected $_remote_link;
    protected $_route_id;
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
 
    public function setNc_id($nc_id)
    {
        $this->_nc_id = (int) $nc_id;
        return $this;
    }
 
    public function getNc_id()
    {
        return $this->_nc_id;
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
  
    public function setDate_added($date_added)
    {
        $this->_date_added = $date_added;
        return $this;
    }
 
    public function getDate_added()
    {
        return $this->_date_added;
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
 
    public function setRemote_link($remote_link)
    {
        $this->_remote_link = (string) $remote_link;
        return $this;
    }
 
    public function getRemote_link()
    {
        return $this->_remote_link;
    }
 
    public function setRoute_id($route_id)
    {
        $this->_route_id = (int) $route_id;
        return $this;
    }
 
    public function getRoute_id()
    {
        return $this->_route_id;
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