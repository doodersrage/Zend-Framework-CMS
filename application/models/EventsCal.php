<?PHP
// application/models/EventsCal.php
 
class Application_Model_EventsCal
{
    protected $_id;
    protected $_start;
    protected $_finish;
	protected $_neighborhood;
    protected $_name;
    protected $_description;
    protected $_short_desc;
    protected $_image;
    protected $_route_id;
 
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
  
    public function setStart($start)
    {
        $this->_start = $start;
        return $this;
    }
 
    public function getStart()
    {
        return $this->_start;
    }
  
    public function setFinish($finish)
    {
        $this->_finish = $finish;
        return $this;
    }
 
    public function getFinish()
    {
        return $this->_finish;
    }
  
    public function setNeighborhood($neighborhood)
    {
        $this->_neighborhood = $neighborhood;
        return $this;
    }
 
    public function getNeighborhood()
    {
        return $this->_neighborhood;
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
        $this->_description = $description;
        return $this;
    }
 
    public function getDescription()
    {
        return $this->_description;
    }
 
    public function setShort_desc($short_desc)
    {
        $this->_short_desc = $short_desc;
        return $this;
    }
 
    public function getShort_desc()
    {
        return $this->_short_desc;
    }
 
    public function setImage($image)
    {
        $this->_image = $image;
        return $this;
    }
 
    public function getImage()
    {
        return $this->_image;
    }
 
    public function setRoute_id($route_id)
    {
        $this->_route_id = $route_id;
        return $this;
    }
 
    public function getRoute_id()
    {
        return $this->_route_id;
    }
}
?>