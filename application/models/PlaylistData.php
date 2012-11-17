<?PHP
// application/models/PlaylistData.php
 
class Application_Model_PlaylistData
{
    protected $_pid;
    protected $_vid;
    protected $_sort_order;
 
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
 
    public function setPid($pid)
    {
        $this->_pid = (int) $pid;
        return $this;
    }
 
    public function getPid()
    {
        return $this->_pid;
    }
  
    public function setVid($vid)
    {
        $this->_vid = (int) $vid;
        return $this;
    }
 
    public function getVid()
    {
        return $this->_vid;
    }
  
    public function setSort_order($sort_order)
    {
        $this->_sort_order = (int) $sort_order;
        return $this;
    }
 
    public function getSort_order()
    {
        return $this->_sort_order;
    }
}
?>