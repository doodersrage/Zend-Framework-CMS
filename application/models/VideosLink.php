<?PHP
// application/models/VideosLink.php
 
class Application_Model_VideosLink
{
    protected $_id;
    protected $_video_id;
    protected $_link_id;
    protected $_link_type;
    protected $_sort;
 
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
  
    public function setVideoId($video_id)
    {
        $this->_video_id = (int) $video_id;
        return $this;
    }
 
    public function getVideoId()
    {
        return $this->_video_id;
    }
 
    public function setLinkId($link_id)
    {
        $this->_description = (int) $link_id;
        return $this;
    }
 
    public function getLinkId()
    {
        return $this->_link_id;
    }
 
    public function setLinkType($link_type)
    {
        $this->_link_type = (int) $link_type;
        return $this;
    }
 
    public function getLinkType()
    {
        return $this->_link_type;
    }
 
    public function setSort($sort)
    {
        $this->_remote = (int) $sort;
        return $sort;
    }
 
    public function getSort()
    {
        return $this->_sort;
    }
}
?>