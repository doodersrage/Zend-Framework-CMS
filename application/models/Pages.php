<?PHP
// application/models/Pages.php
 
class Application_Model_Pages
{
    protected $_id;
    protected $_title;
    protected $_filelnk;
    protected $_copy_text;
    protected $_title_tag;
    protected $_mobile_text;
    protected $_seo_text;
    protected $_parent_id;
    protected $_playlist;
    protected $_slideshow;
    protected $_form_id;
    protected $_desc_tag;
    protected $_keyword_tag;
    protected $_pgsort;
    protected $_calendar;
    protected $_menu;
    protected $_new_window;
    protected $_modified;
	protected $_link_name;
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
  
    public function setTitle($title)
    {
        $this->_title = (string) $title;
        return $this;
    }
 
    public function getTitle()
    {
        return $this->_title;
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
 
    public function setCopy_text($copy_text)
    {
        $this->_copy_text = (string) $copy_text;
        return $this;
    }
 
    public function getCopy_text()
    {
        return $this->_copy_text;
    }
 
    public function setMobile_text($mobile_text)
    {
        $this->_mobile_text = $mobile_text;
        return $this;
    }
 
    public function getMobile_text()
    {
        return $this->_mobile_text;
    }
 
    public function setSeo_text($seo_text)
    {
        $this->_seo_text = $seo_text;
        return $this;
    }
 
    public function getSeo_text()
    {
        return $this->_seo_text;
    }
 
    public function setLink_name($link_name)
    {
        $this->_link_name = $link_name;
        return $this;
    }
 
    public function getLink_name()
    {
        return $this->_link_name;
    }
 
    public function setTitle_tag($title_tag)
    {
        $this->_title_tag = $title_tag;
        return $this;
    }
 
    public function getTitle_tag()
    {
        return $this->_title_tag;
    }
 
    public function setDesc_tag($desc_tag)
    {
        $this->_desc_tag = $desc_tag;
        return $this;
    }
 
    public function getDesc_tag()
    {
        return $this->_desc_tag;
    }
 
    public function setKeyword_tag($keyword_tag)
    {
        $this->_keyword_tag = $keyword_tag;
        return $this;
    }
 
    public function getKeyword_tag()
    {
        return $this->_keyword_tag;
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
 
    public function setParent_id($parent_id)
    {
        $this->_parent_id = $parent_id;
        return $this;
    }
 
    public function getParent_id()
    {
        return $this->_parent_id;
    }
 
    public function setPlaylist($playlist)
    {
        $this->_playlist = $playlist;
        return $this;
    }
 
    public function getPlaylist()
    {
        return $this->_playlist;
    }
 
    public function setSlideshow($slideshow)
    {
        $this->_slideshow = $slideshow;
        return $this;
    }
 
    public function getSlideshow()
    {
        return $this->_slideshow;
    }
 
    public function setForm_id($form_id)
    {
        $this->_form_id = $form_id;
        return $this;
    }
 
    public function getForm_id()
    {
        return $this->_form_id;
    }
 
    public function setPgsort($pgsort)
    {
        $this->_pgsort = $pgsort;
        return $this;
    }
 
    public function getPgsort()
    {
        return $this->_pgsort;
    }
 
    public function setCalendar($calendar)
    {
        $this->_calendar = $calendar;
        return $this;
    }
 
    public function getCalendar()
    {
        return $this->_calendar;
    }
 
    public function setMenu($menu)
    {
        $this->_menu = $menu;
        return $this;
    }
 
    public function getMenu()
    {
        return $this->_menu;
    }
 
    public function setNew_window($new_window)
    {
        $this->_new_window = $new_window;
        return $this;
    }
 
    public function getNew_window()
    {
        return $this->_new_window;
    }
 
    public function setRoute_id($route_id)
    {
        $this->_route_id = (int)$route_id;
        return $this;
    }
 
    public function getRoute_id()
    {
        return $this->_route_id;
    }
}
?>