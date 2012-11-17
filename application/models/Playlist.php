<?PHP
// application/models/Playlist.php
 
class Application_Model_Playlist
{
    protected $_id;
    protected $_name;
    protected $_height;
    protected $_width;
    protected $_autoplay;
    protected $_controlbar;
    protected $_playlistvisual;
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
  
    public function setWidth($width)
    {
        $this->_width = (int) $width;
        return $this;
    }
 
    public function getWidth()
    {
        return $this->_width;
    }
  
    public function setHeight($height)
    {
        $this->_height = (int) $height;
        return $this;
    }
 
    public function getHeight()
    {
        return $this->_height;
    }
  
    public function setAutoplay($autoplay)
    {
        $this->_autoplay = (int) $autoplay;
        return $this;
    }
 
    public function getAutoplay()
    {
        return $this->_autoplay;
    }
  
    public function setControlbar($controlbar)
    {
        $this->_controlbar = (int) $controlbar;
        return $this;
    }
 
    public function getControlbar()
    {
        return $this->_controlbar;
    }
 
    public function setPlaylistvisual($playlistvisual)
    {
        $this->_playlistvisual = (int) $playlistvisual;
        return $this;
    }
 
    public function getPlaylistvisual()
    {
        return $this->_playlistvisual;
    }
 
    public function setModified($modified)
    {
        $this->_modified = $modified;
        return $this;
    }
 
    public function getModified()
    {
        return $this->_modified;
    }
}
?>