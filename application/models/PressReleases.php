<?PHP
// application/models/PlaylistData.php
 
class Application_Model_PressReleases
{
    protected $_id;
    protected $_title;
    protected $_date;
    protected $_copy_text;
    protected $_filelnk;
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
  
    public function setTitle($title)
    {
        $this->_title = (string) $title;
        return $this;
    }
 
    public function getTitle()
    {
        return $this->_title;
    }
  
    public function setCopy_text($copy_text)
    {
        $this->_copy_text = (string) $copy_text;
        return $this;
    }
 
    public function getCopy_text()
    {
        return $this->_copy_text;
    }
  
    public function setDate($date)
    {
        $this->_date = (string) $date;
        return $this;
    }
 
    public function getDate()
    {
        return $this->_date;
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